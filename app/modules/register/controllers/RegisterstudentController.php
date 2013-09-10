<?php

class Register_RegisterstudentController extends Zend_Controller_Action {

    public function init()
    {
        $sesion  = Zend_Auth::getInstance();
      if(!$sesion->hasIdentity() ){
        $this->_helper->redirector('index',"index",'default');
      }
      $login = $sesion->getStorage()->read();
      $this->sesion = $login;
    }
    public function indexAction(){
        try{
            $where['subid'] = $this->sesion->subid;        
            $where['eid'] = $this->sesion->eid;  
            $where['oid'] = $this->sesion->oid;  
            $where['perid'] = $this->sesion->period->perid;  
            $where['facid'] = $this->sesion->faculty->facid;
            $where['escid'] = $this->sesion->escid;
            $this->view->subid=$where['subid'];
            $this->view->facid=$where['facid'];
            $this->view->perid=$where['perid'];
            $fm=new Register_Form_Buscar();
            $pfac='T'.$escid['1'];
            $this->view->fm=$fm;
            $escuelas = new Api_Model_DbTable_Speciality();
            if($where['subid']<>'1901'){
                $data['eid']=$where['eid'];
                $data['oid']=$where['oid'];
                $data['subid']=$where['subid'];
                $data['state']='A';
               $lesc = $escuelas->_getFilter($data); 
            }
            else{
            $lesc = $escuelas->_getspeciality($where); 
            }
            if ($lesc ) $this->view->escuelas=$lesc;
        }catch (Exception $ex){
            print "Error: Cargar ".$ex->getMessage();
        }
    }

    public function liststudentAction(){

          $this->_helper->getHelper('layout')->disableLayout();
          $tipo = trim(base64_decode($this->_getParam('ver')));
          $perid = $this->sesion->period->perid;  
          $eid = $this->sesion->eid;        
          $oid = $this->sesion->oid; 

            if ($tipo=="vertodo")
            {
                $escid = $this->sesion->escid;
                $pfac='T'.$escid['1'];
                $estados='I';
                $bdu = new Api_Model_DbTable_Registration();        
                $str = " and ( upper(last_name0) || ' ' || upper(last_name1) || ', ' || upper(first_name) like '%$nombre%' and u.uid like '$codigo%')";
                $datos= $bdu->_getAlumnosXMatriculaXTodasescuelasxEstado($eid, $oid,$str,$escid['1'],$perid,$estados);  
                $this->view->datos=$datos;
            }
            else
            {
                $nombre = trim(strtoupper($this->_getParam('nombre')));
                $estados= trim(strtoupper($this->_getParam('estadom')));
                $codigo = $this->_getParam('uid');        
                $escid=$this->_getParam('escid');          
                $bdu = new Api_Model_DbTable_Registration();        
                $str = " and ( upper(last_name0) || ' ' || upper(last_name1) || ', ' || upper(first_name) like '%$nombre%' and u.uid like '$codigo%')";

                if ($escid=='T1' || $escid=='T2' || $escid=='T3' || $escid=='T4' || $escid=='T5' || $escid=='T6' || $escid=='T8' || $escid=='T9'){
                    $datos= $bdu->_getAlumnosXMatriculaXTodasescuelasxEstado($eid, $oid,$str,$escid['1'],$perid,$estados);  
                    $this->view->datos=$datos;
                }else{
                    $datos= $bdu->_getAlumnosXMatriculaxEstado($eid, $oid,$str,$escid,$perid,$estados);
                    $this->view->datos=$datos;
                }
            }


    }

        public function detailAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $where['uid'] = base64_decode($this->_getParam('uid'));
            $where['pid'] = base64_decode($this->_getParam('pid'));
            $where['escid'] = base64_decode($this->_getParam('escid'));
            $where['subid'] = base64_decode($this->_getParam('subid'));
            $where['perid'] = $this->sesion->period->perid;
            $where['eid'] = $this->sesion->eid;    
            $where['oid'] = $this->sesion->oid;      
            $this->view->perid=$where['perid'];        
            // Obtener fecha periodo
            $infoper = new Api_Model_DbTable_Periods();
            $infoperiodo = $infoper->_getOne($where);
            // print_r($infoperiodo);
            if ($infoperiodo) {
                $this->view->finimat = $infoperiodo['start_registration'];
                $this->view->ffinmat = $infoperiodo['end_registration'];
            }
            
            // Obteniendo la facultad
            $escuela= new Api_Model_DbTable_Speciality();
            $dataescid=$escuela->_getFacspeciality($where);
            // print_r($dataescid);
            if ($dataescid) {
              $this->view->facultad =$dataescid[0]['nomfac']; 
            }
            //Obteniendo la escuela y especialidad(si lo tuviera)
            if ($dataescid['parent']==""){
               $this->view->escuela=strtoupper($dataescid[0]['nomesc']);
            }else{
                $dato['eid'] = $this->sesion->eid;    
                $dato['oid'] = $this->sesion->oid;
                $dato['escid'] = $dataescid['parent']; 
                $dato['subid'] = $dataescid['sub']; 
                $esc = $escuela->_getOne($dato);
                $this->view->escuela=strtoupper($esc['name']);
                $dataescid=$escuela->_getOne($where);
                $this->view->especialidad= strtoupper($dataescid['name']);
            }
            // //Obtenemos el valor de la matricula
            $matri = new Api_Model_DbTable_Registration();
            $rmatri = $matri->_getRegister($where);
            // print_r($rmatri);
            if(!$rmatri) return false;
            $where['regid']=$rmatri['regid'];
            $this->view->matricula_ = $rmatri;
            
            //Obteniendo los datos del alumno
            $alum = new Api_Model_DbTable_Person();
            $ralum= $alum->_getOne($where);
            if ($ralum) $this->view->alumno = $ralum['last_name0']." ".$ralum['last_name1'].", ".$ralum['first_name'];
            
            // //Obtenemos los cursos matriculados
            $lcursos = new Api_Model_DbTable_Registrationxcourse();
            $listacurso =$lcursos->_getFilter($where);
            // print_r($listacurso);

            foreach ($listacurso as $cursomas){
                //Agregar valores al registro de matricula curso para mandar a la vista
                $where['courseid']=$cursomas['courseid'];
                $where['curid']=$cursomas['curid'];
                $where['turno']=$cursomas['turno'];

                $nuevoreg = new Api_Model_DbTable_Course();
                $rcus = $nuevoreg->_getOne($where);
                // print_r($rcus);
                $cursomas['semid'] = $rcus['semid'];
                $cursomas['credits'] = $rcus['credits'];
                $cursomas['namecourse'] = $rcus['name'];

                //Obteniendo numero de veces matrocula a un curso
                // $veces = new Api_Model_DbTable_Course();
                // $listusuario = $veces ->_getCoursesXStudentXV($cursomas); 
                // $cursomas['veces'] = intval($listusuario[0]['veces']);

                // $db = Zend_Registry::get('Adaptador1');
                // $sql2="select courseid,turno,perid from base_registration_course where uid='".$where['uid']."' and perid!='".$where['perid']."' and notafinal!='-3' and notafinal!='-2'";
                // $curmat = $db->fetchAll($sql2);   

                $curmat =$lcursos->_register($where);
                $i=0;
                foreach ($curmat as $row2) {
                    if($where['courseid']==$row2['courseid']){
                        $periodo=$row2['perid'];
                        $p=substr($periodo,2,3);
                        if($p=='A' or $p=='B' or $p=='N' or $p=='V'){
                         $i=$i+1;  
                         }               
                     }
                 }
                $cursomas['veces'] = intval($i);
                //Sacamos los docentes por curso
                $bdprofesores = new Api_Model_DbTable_Coursexteacher();        
                $datosss= $bdprofesores->_getinfoDoc($where);
                $ndoc=$datosss[0]['nameteacher'];
                $cursomas['teacherp'] = $ndoc;
                // print_r($cursomas);      
                $cantmatr = new Api_Model_DbTable_Registrationxcourse();
                $numat = $cantmatr->_getCantRegistration($where);

                if ($numat)
                  $cursomas['nunmatriculados'] = $numat;    
                
                $nupremat = $cantmatr->_getCantiPreResgistration($where);

                if ($nupremat)
                    $cursomas['nunmapretriculados'] = $nupremat;
                
                $listacurso1[]= $cursomas;
            }      
            // print_r($listacurso1);      
             $this->view->listacurso = $listacurso1;

            //obtenemos los pagos realizados
            $pagos = new Api_Model_DbTable_Payments();
            $rpagos = $pagos->_getOne($where);
            if ($rpagos){
                //verificamos fechas de pago y tiempo de pago
                $this->view->fechapago = $rpagos['date_payment'];
                $fpago = $this->view->fechapago;
                $this->view->montopagado = (float)$rpagos['amount'];
                $where['ratid'] = $rpagos['ratid'];
                $montoapagar="";
                $tasa = new Api_Model_DbTable_Rates();
                $rtm = $tasa->_getOne($where);
                if ($rtm){
                    // Verificando el monto a pagar y la fecha de pago
                    $fechaactual =strtotime($fpago);
                    $ffn= strtotime($rtm['f_fin_tn']." 11:59:00");
                    $fi1= strtotime($rtm['f_fin_ti1']." 11:59:00");
                    $fi2= strtotime($rtm['f_fin_ti2']." 11:59:00");
                    switch ($fechaactual){                        
                        case ($fechaactual < $ffn):{
                            // Esta dentro de la fecha normal                            
                            $montoapagar = $rtm['t_normal'];                            
                            break;
                        }
                        case ($ffn < $fechaactual && $fechaactual < $fi1):{
                            // Esta dentro de la fecha normal
                            $montoapagar = $rtm['t_incremento1'];
                            break;
                        }
                        case ($fi1 < $fechaactual && $fechaactual < $fi2 ):{
                            // Esta con el primer incremento ( normalmente +50%)
                            $montoapagar = $rtm['t_incremento2'];
                            break;
                            }
                        default:{
                            $montoapagar = "Monto no aceptado";
                            break;
                            }                        
                    }
                    $this->view->montoapagar=(float)$montoapagar ;
                }                
            }

            //obtenemos las condiciones de la matricula registradas
            $condi = new Api_Model_DbTable_Condition();
            $date['eid']=$where['eid'];
            $date['oid']=$where['oid'];
            $date['pid']=$where['pid'];
            $date['uid']=$where['uid'];
            $date['escid']=$where['escid'];
            $date['perid']=$where['perid'];
            $date['subid']=$where['subid'];
            $rcondision=$condi->_getFilter($date);
            // print_r($rcondision);
            if ($rcondision) $this->view->condision = $rcondision;
            
            //Sacamos la lista de pagos del alumno
            $listapagos = new Api_Model_DbTable_PaymentsDetail();
            $reg = $listapagos->_getAll($where);

            if ($reg) $this->view->pagosrealizados = $reg;




        }catch(Exception $ex ){
            print ("Error Controlador Mostrar Datos: ".$ex->getMessage());
        } 
    }


        public function listcoursependientAction()
    {
            $this->_helper->getHelper('layout')->disableLayout();
           
            $subid = base64_decode($this->_getParam('subid'));
            $pid = base64_decode($this->_getParam('pid'));
            $uid = base64_decode($this->_getParam('uid'));
            $escid = base64_decode($this->_getParam('escid'));
            $curid = ($this->_getParam('curid'));

            $eid = $this->sesion->eid;    
            $oid = $this->sesion->oid;
            $perid= $this->sesion->period->perid;
    
        require_once 'Zend/Loader.php';
        Zend_Loader::loadClass('Zend_Rest_Client');
        $base_url = 'http://localhost:8080/';
        $endpoint = '/s1st3m4s/und4c/validate';
        $data = array('uid' => $uid, 'pid' => $pid, 'escid' => $escid,'subid' =>$subid,'eid' =>$eid,'oid' =>$oid,'perid'=>$perid,'curid'=>$curid);
        $client = new Zend_Rest_Client($base_url);
        $httpClient = $client->getHttpClient();
        $httpClient->setConfig(array("timeout" => 680));
        $response = $client->restget($endpoint,$data);
        $lista=$response->getBody();
        if ($lista){
        $data = Zend_Json::decode($lista);
        $this->view->datos=$data; 
        }

    }

        public function turnoAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $str = base64_decode($this->_getParam('str'));
            if ($str=="") return false;
            $turno = trim($this->_getParam('t'));
            $nro = $this->_getParam('nro');
            $perid = $this->sesion->period->perid;
            //$perid='12B';
            $eid = $this->sesion->eid;        
            $oid = $this->sesion->oid;        
            // $rid='AL';
            $data['turno']=$turno;
            $data['updated']=date("Y-m-d h:m:s");
            $bdmatricula_curso = new Api_Model_DbTable_Registrationxcourse();  
            $g=$bdmatricula_curso->fetchRow($str);
            if ($g){                
                if ($bdmatricula_curso->_updatestr($data,$str)){
                    $str1="eid ='$eid' and oid='$oid' and courseid='".$g['courseid']."' and curid='".$g['curid']."' and perid='$perid' and escid='".$g['escid']."' and subid='".$g['subid']."' and turno='$turno' and uid='".$g['uid']."' and pid='".$g['pid']."'";
                    $bdmatricula_curso1 = new Api_Model_DbTable_Registrationxcourse(); 
                     $gg=$bdmatricula_curso1->fetchRow($str1);
                     $regq = $gg->toArray();
                     if ($gg) $this->view->reg = $gg->toArray();
                     $this->view->nro=$nro;                
                }
            }?>
             <script type="text/javascript">
                            window.location.reload();
            </script>
<?php
        }catch(Exception $ex ){
            print ("Error Controlador Mostrar Datos: ".$ex->getMessage());
        } 
    }

        public function deletecourseAction()
    {
        try{

            $where['perid']= $this->sesion->period->perid;
            $where['uid'] = base64_decode($this->_getParam('uid'));
            $where['pid'] = base64_decode($this->_getParam('pid'));
            $where['escid'] = base64_decode($this->_getParam('escid'));
            $where['subid'] = base64_decode($this->_getParam('subid'));
            $where['courseid'] = base64_decode($this->_getParam('courseid'));
            $where['curid'] = base64_decode($this->_getParam('curid'));
            $where['turno'] = base64_decode($this->_getParam('turno'));
            $where['eid'] = $this->sesion->eid;        
            $where['oid'] = $this->sesion->oid;
            $where['regid']=$where['uid'].$where['perid'];
            $bdmatricula_curso = new Api_Model_DbTable_Registrationxcourse();  
            $bdmatricula_curso ->_delete($where);        

            $this->_helper->_redirector("detail","registerstudent","register",array('uid' => base64_encode($where['uid']) ,'pid' => base64_encode($where['pid']),'escid' => base64_encode($where['escid']),'subid' => base64_encode($where['subid'])));


        }catch(Exception $ex ){
            print ("Error Controlador Mostrar Datos: ".$ex->getMessage());
        } 
    }

        public function validateAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $where['uid'] = base64_decode($this->_getParam('uid'));
            $where['pid'] = base64_decode($this->_getParam('pid'));
            $where['escid'] = base64_decode($this->_getParam('escid'));
            $where['subid'] = base64_decode($this->_getParam('subid'));
            $where['perid'] = $this->sesion->period->perid;
            $flag = base64_decode($this->_getParam('flag'));
            $where['regid']=$where['uid'].$where['perid'];
            $where['eid'] = $this->sesion->eid;        
            $where['oid'] = $this->sesion->oid;        

            $bdmatricula_curso = new Api_Model_DbTable_Registrationxcourse();        
            $bdmatricula = new Api_Model_DbTable_Registration(); 
            $data= $bdmatricula->_getOne($where);
            //Verifico que la matricula exista
            if ($data){
                // Creo el STR que ira despues del WHERE
                $str=" eid='".$where['eid']."' and oid=''".$where['oid']."' and subid='".$where['subid']."' and escid='".$where['escid']."' and uid='".$where['uid']."' and pid='".$where['pid']."' and perid='".$where['perid']."' and regid= '".$where['regid']."' ";
                //Si el Valor es E se eliminar
                    if (trim($flag)=="E"){

                            $DataUsuario['state']="B";
                            $DataUsuario['updated']=date("Y-m-d h:m:s");
                            $DataUsuario['modified']=$this->sesion->uid; 
                            if ($bdmatricula->_update($DataUsuario,$where)){
                            $bdmatricula->_delete($where);
                            $this->view->msgeliminar="La matricula fue eliminada correctamente";}             
                    }
                    else{
                        //Asigno los valores al array para la modiciacion
                        $DataUsuario['state']=trim($flag);
                        $DataUsuario['updated']=date("Y-m-d h:m:s");
                        $DataUsuario['modified']=$this->sesion->uid; 
                        if ($bdmatricula->_update($DataUsuario,$where)){
                            //si actualizo correctamente la matricula, ahora actualizo los cursos de la matricula  
                                $DataUsuario1['state']=trim($flag);
                                $DataUsuario1['updated']=date("Y-m-d h:m:s");
                                $DataUsuario1['modified']=$this->sesion->uid;
                                $DataUsuario1['approved']=$this->sesion->uid; 
                                $DataUsuario1['approved_date']=date("Y-m-d h:m:s");                                  
                            if ($bdmatricula_curso->_updatestateregister($DataUsuario1,$where)){
                                $this->_helper->_redirector("detail","registerstudent","register",array('uid' => base64_encode($where['uid']) ,'pid' => base64_encode($where['pid']),'escid' => base64_encode($where['escid']),'subid' => base64_encode($where['subid'])));
                                }else{
                                    print "No se pudo actualizar los cursos de la matricula";
                                }
                        }else{
                            print "No se pudo actualizar la matricula";
                        }
                    }
            }
        }catch(Exception $ex ){
            print ("Error Controlador Mostrar Datos: ".$ex->getMessage());
        } 
    }


        public function printAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $where['uid'] = base64_decode($this->_getParam('uid'));
            $where['pid'] = base64_decode($this->_getParam('pid'));
            $where['escid'] = base64_decode($this->_getParam('escid'));
            $where['subid'] = base64_decode($this->_getParam('subid'));
            $where['perid'] = $this->sesion->period->perid;
            $where['eid'] = $this->sesion->eid;        
            $where['oid'] = $this->sesion->oid;        

            // Obteniendo la facultad
            $escuela= new Api_Model_DbTable_Speciality();
            $dataescid=$escuela->_getFacspeciality($where);
            // print_r($dataescid);
            if ($dataescid) {
              $nomfac=strtoupper($dataescid[0]['nomfac']); 
              $this->view->facultad=$nomfac;
            }
            //Obteniendo la escuela y especialidad(si lo tuviera)
            if ($dataescid['parent']==""){
               $this->view->escuela=strtoupper($dataescid[0]['nomesc']);
            }else{
                $dato['eid'] = $this->sesion->eid;    
                $dato['oid'] = $this->sesion->oid;
                $dato['escid'] = $dataescid['parent']; 
                $dato['subid'] = $dataescid['sub']; 
                $esc = $escuela->_getOne($dato);
                $this->view->escuela=strtoupper($esc['name']);
                $dataescid=$escuela->_getOne($where);
                $this->view->especialidad= strtoupper($dataescid['name']);
            }
            // //Obtenemos el valor de la matricula
            $matri = new Api_Model_DbTable_Registration();
            $rmatri = $matri->_getRegister($where);
            // print_r($rmatri);
            if(!$rmatri) return false;
            $where['regid']=$rmatri['regid'];
            $perid= $rmatri['perid'];
            $semestre=$rmatri['semid'];
            $codigo_matri=$rmatri['uid'];
            $totalcreditos=$rmatri['credits'];
            $estadomatricula=$rmatri['state'];
            $this->view->perid=$perid;
            $this->view->estadomatricula=$estadomatricula;
            $this->view->semestre = $semestre;
            $this->view->cod_matri = $codigo_matri;
            $this->view->total_creditos = $totalcreditos;

            $db_cur_alu= new Api_Model_DbTable_Studentxcurricula();
            $curicula_alumno = $db_cur_alu->_getOne($where);
            if(!$curicula_alumno) return false;
            $curricula_alu = $curicula_alumno['curid'];
            $this->view->curricula = $curricula_alu;
            

            //Obteniendo los datos del alumno
            $alum = new Api_Model_DbTable_Person();
            $ralum= $alum->_getOne($where);
            if ($ralum) $this->view->alumno = $ralum['last_name0']." ".$ralum['last_name1'].", ".$ralum['first_name'];

            // //Obtenemos los cursos matriculados
            $lcursos = new Api_Model_DbTable_Registrationxcourse();
            $listacurso =$lcursos->_getFilter($where);
            // print_r($listacurso);

            foreach ($listacurso as $cursomas){
                //Agregar valores al registro de matricula curso para mandar a la vista
                $where['courseid']=$cursomas['courseid'];
                $where['curid']=$cursomas['curid'];
                $where['turno']=$cursomas['turno'];

                $nuevoreg = new Api_Model_DbTable_Course();
                $rcus = $nuevoreg->_getOne($where);
                // print_r($rcus);
                $cursomas['semid'] = $rcus['semid'];
                $cursomas['credits'] = $rcus['credits'];
                $cursomas['namecourse'] = $rcus['name'];

                //Obteniendo numero de veces matrocula a un curso
                $veces = new Api_Model_DbTable_Course();
                $listusuario = $veces ->_getCoursesXStudentXV($cursomas); 
                $cursomas['veces'] = intval($listusuario[0]['veces']);

                //Sacamos los docentes por curso
                $bdprofesores = new Api_Model_DbTable_Coursexteacher();        
                $datosss= $bdprofesores->_getinfoDoc($where);
                $ndoc=$datosss[0]['nameteacher'];
                $cursomas['teacherp'] = $ndoc;
                // print_r($cursomas);                      
                $listacurso1[]= $cursomas;
            }          
             $this->view->listacurso = $listacurso1;           

        }catch(Exception $ex ){
            print ("Error Controlador Mostrar Datos: ".$ex->getMessage());
        } 
    }


}
