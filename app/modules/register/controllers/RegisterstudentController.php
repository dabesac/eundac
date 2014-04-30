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
            // $pfac='T'.$escid['1'];
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
                if($where['facid']=='5' || $where['facid']=='2'){
                $data['eid']=$where['eid'];
                $data['oid']=$where['oid'];
                $data['facid']=$where['facid'];
                $data['state']='A';
                $lesc = $escuelas->_getFilter($data); 
                }
                else{
                $lesc = $escuelas->_getspeciality($where); 
                }
            }
            if ($lesc ) $this->view->escuelas=$lesc;
        }catch (Exception $ex){
            print "Error: Cargar ".$ex->getMessage();
        }
    }

    public function liststudentAction(){
        $this->_helper->getHelper('layout')->disableLayout();
        $state = $this->_getParam('state');
        $perid = $this->sesion->period->perid;  
        $eid = $this->sesion->eid;        
        $oid = $this->sesion->oid;
        $subid = $this->sesion->subid;
        $escid = $this->sesion->escid;

        /*if ($state == null) {
            $pfac='T'.$escid['1'];
            $estados='I';
            $bdu = new Api_Model_DbTable_Registration();        
            $str = " and ( upper(last_name0) || ' ' || upper(last_name1) || ', ' || upper(first_name) like '%$nombre%' and u.uid like '$codigo%')";
            $datos= $bdu->_getAlumnosXMatriculaXTodasescuelasxEstado($eid, $oid,$str,$escid['1'],$perid,$estados, $subid);  
            $this->view->datos=$datos;
        }else*/
        if ($state) {
            if ($subid != '1901') {
                $escid = '';
            }
            $nombre = null;
            $codigo = null;
            $estados = $state;
            $bdu = new Api_Model_DbTable_Registration();        
            $str = " and ( upper(last_name0) || ' ' || upper(last_name1) || ', ' || upper(first_name) like '%$nombre%' and u.uid like '$codigo%')";
            $datos= $bdu->_getAlumnosXMatriculaXTodasescuelasxEstado($eid, $oid,$str,$escid['1'],$perid,$estados);  
            $this->view->datos=$datos;
        }
    }

    public function detailAction()
    {
        try{
            
            $this->_helper->layout()->disableLayout();

            //Databases
            $specialityDb     = new Api_Model_DbTable_Speciality();
            $userDb           = new Api_Model_DbTable_Users();
            $personDb         = new Api_Model_DbTable_Person();
            $coursesRegisterDb= new Api_Model_DbTable_Registrationxcourse();
            $registerDb       = new Api_Model_DbTable_Registration();
            $coursesDb        = new Api_Model_DbTable_Course();
            $teachersDb       = new Api_Model_DbTable_Coursexteacher();
            $paymentDb        = new Api_Model_DbTable_Payments();
            $paymentsDetailDb = new Api_Model_DbTable_PaymentsDetail();
            $conditionDb      = new Api_Model_DbTable_Condition();
            $bankReceiptsDb   = new Api_Model_DbTable_Bankreceipts();
            $rateDb           = new Api_Model_DbTable_Rates();
            //________________________________________________________________

            $uid  = base64_decode($this->_getParam('uid'));
            $pid  = base64_decode($this->_getParam('pid'));
            $escid= base64_decode($this->_getParam('escid'));
            $subid= base64_decode($this->_getParam('subid'));
            $semid= base64_decode($this->_getParam('semid'));

            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $perid = $this->sesion->period->perid;

           
            //Información de Facultad
            $where = array('eid'=>$eid, 'oid'=>$oid, 'escid'=>$escid);
            $faculty = $specialityDb->_getFacspeciality($where);
            $this->view->infoSpeciality = $faculty;

            $where = array( 'eid'   =>$eid, 
                            'oid'   =>$oid, 
                            'uid'   =>$uid, 
                            'pid'   =>$pid ,
                            'escid' =>$escid, 
                            'subid' =>$subid);
            $person = $userDb->_getInfoUser($where);
            $this->view->person = $person;
            
            //Información de Condición
            $where = array( 'eid'   => $eid, 
                            'oid'   => $oid, 
                            'uid'   => $uid, 
                            'pid'   => $pid,
                            'escid' => $escid, 
                            'subid' => $subid,
                            'perid' => $perid );

            $attrib = array('doc_authorize', 'comments');
            $condition = $conditionDb->_getFilter($where, $attrib);
            $this->view->condition = $condition;
            
            //Información de Pago
            $attrib = array('date_payment', 'amount', 'ratid');
            $paymentData = $paymentDb->_getFilter($where, $attrib);
            if ($paymentData) {
                $paymentData[0]['date_payment'] = substr($paymentData[0]['date_payment'], 0, 10);
                $paymentData[0]['date_fix'] = date('d-m-Y', strtotime($paymentData[0]['date_payment']));
                $this->view->paymentData = $paymentData;

                //Detalle de Pago
                $paymentsDetail = $paymentsDetailDb->_getFilter($where);
                $this->view->paymentsDetail = $paymentsDetail;

                //Información de Taza
                $where = array( 'eid'   => $eid, 
                                'oid'   => $oid, 
                                'ratid' => $paymentData[0]['ratid'], 
                                'perid' => $perid );
                $rate = $rateDb->_getFilter($where);
                $this->view->rate = $rate;
            }

            //Verificar Fecha de Pago
            $datePago       = strtotime($paymentData[0]['date_payment']);
            $dateNormal     = strtotime($rate[0]['f_fin_tnd']);
            $dateIncrement1 = strtotime($rate[0]['f_fin_ti1']);
            $dateIncrement2 = strtotime($rate[0]['f_fin_ti2']);
            $dateIncrement3 = strtotime($rate[0]['f_fin_ti3']);

            $montoPagado = $paymentData[0]['amount'];

            $pagoAtiempo = 'yes';
            if ($datePago <= $dateNormal) {
                $tipePayment['tipoPago'] = 'AT';
                $tipePayment['incremento'] = null; //s
            }elseif ($datePago <= $dateIncrement1){
                $tipePayment['tipoPago']   = 'I1';
                $tipePayment['incremento'] = $rate[0]['t_incremento1'];
                $tipePayment['porcentaje'] = $rate[0]['v_t_incremento1'];
                $pagoAtiempo = 'no';
            }elseif ($datePago <= $dateIncrement2){
                $tipePayment['tipoPago']    = 'I2';
                $tipePayment['incremento'] = $rate[0]['t_incremento2'];
                $tipePayment['porcentaje'] = $rate[0]['v_t_incremento2'];
                $pagoAtiempo = 'no';
            }elseif ($datePago <= $dateIncrement3){
                $tipePayment['tipoPago']    = 'I3';
                $tipePayment['incremento'] = $rate[0]['t_incremento3'];
                $tipePayment['porcentaje'] = $rate[0]['v_t_incremento2'];
                $pagoAtiempo = 'no';
            }else {
                $tipePayment['tipoPago']    = 'FT';
                $pagoAtiempo = 'no';
                $tipePayment['incremento'] = null; //s
            }
            
            if ($montoPagado >= $tipePayment['incremento']) {
                $tipePayment['pago_completo'] = 'yes';
            }else{
                 $tipePayment['pago_completo'] = 'no';
            }

            $this->view->tipePayment = $tipePayment;
            $this->view->pagoAtiempo  = $pagoAtiempo;


            //Estado de la Matricula
            $where = array( 'eid'   =>$eid, 
                            'oid'   =>$oid, 
                            'uid'   =>$uid, 
                            'pid'   =>$pid,
                            'escid' =>$escid, 
                            'subid' =>$subid,
                            'perid' =>$perid );
            $attrib = array('state', 'credits');
            $stateRegister = $registerDb->_getFilter($where, $attrib);
            $this->view->fullCredits   = $stateRegister[0]['credits'];
            
            if ($stateRegister[0]['state'] == 'I') {
                $stateRegister = 'I';
            }elseif ($stateRegister[0]['state'] == 'M') {
                $stateRegister = 'M';
            }elseif ($stateRegister[0]['state'] == 'O') {
                $stateRegister = 'O';
            }elseif ($stateRegister[0]['state'] == 'R') {
                $stateRegister = 'R';
            }elseif ($stateRegister[0]['state'] == 'B') {
                $stateRegister = 'B';
            }
            $this->view->stateRegister = $stateRegister;

            //Cursos Prematriculados
            $where = array( 'eid'  =>$eid, 
                            'oid'  =>$oid, 
                            'uid'  =>$uid, 
                            'pid'  =>$pid,
                            'escid'=>$escid, 
                            'subid'=>$subid,
                            'perid'=>$perid,
                            'state'=>$stateRegister);
            $attrib = array('courseid', 'turno', 'curid', 'uid', 'pid', 'escid', 'subid', 'curid');
            $courses = $coursesRegisterDb->_getFilter($where, $attrib);
            $curid = $courses[0]['curid'];
            $matriculaCondicional = 'No';

            $c = 0;
            foreach ($courses as $course) {
                //Obteniendo Cursos
                $attrib = array('courseid', 'curid', 'name', 'credits', 'semid');
                $where = array( 'eid'     =>$eid, 
                                'oid'     =>$oid, 
                                'escid'   =>$escid, 
                                'subid'   =>$subid,
                                'courseid'=>$course['courseid'],
                                'curid'   =>$course['curid'] );
                $coursesName[$c] = $coursesRegisterDb->_getInfoCourse($where, $attrib);

                //Numero de Veces que llevo un curso
                $attrib = array('perid', 'notafinal');
                $where = array( 
                                'eid'     => $eid, 
                                'oid'     => $oid, 
                                'uid'     => $uid, 
                                'pid'     => $pid,
                                'escid'   => $escid, 
                                'subid'   => $subid,
                                'courseid'=> $course['courseid'],
                                'curid'   => $course['curid'],
                                'state'   => 'M' );
                $veces = $coursesRegisterDb->_getFilter($where, $attrib);
                $j = 0;
                if ($veces) {
                    foreach ($veces as $vez) {
                        if ($vez['perid']['2'] != 'D' and $vez['perid']['2'] != 'E' and $vez['notafinal'] != '-3' and $vez['perid'] != $perid) {
                            $j++;
                        }
                    }
                }
                if ($j >= 2) {
                    $coursesCondition[$c]['courseid'] = $course['courseid'];
                    $coursesCondition[$c]['veces']    = $j;
                    $coursesCondition[$c]['credits']  = $coursesName[$c][0]['credits'];
                    $coursesCondition[$c]['montoxCredito'] = 11 * ($j-1);
                    $matriculaCondicional = 'Si';
                }
                $coursesName[$c]['veces'] = $j;

                //Codigo de Profesores
                $attrib = array('uid', 'pid');
                $where = array( 'eid'     =>$eid, 
                                'oid'     =>$oid, 
                                'escid'   =>$escid, 
                                'subid'   =>$subid,
                                'courseid'=>$course['courseid'],
                                'curid'   =>$course['curid'],
                                'turno'   =>$course['turno'],
                                'perid'   =>$perid,
                                'state'   =>'A',
                                'is_main' =>'S' );
                $teacher = $teachersDb->_getFilter($where, $attrib);
                $where = array( 'eid'   => $eid, 
                                'pid'   => $teacher[0]['pid'] );
                $teachers[$c] = $personDb->_getFilter($where);
                $c++;
            }
            //Pago por Tercera vez
            if ($matriculaCondicional == 'Si') {
                $where = array( 'code_student' => $uid,
                                'perid'        => $perid,
                                'concept'      => '00000045' );

                $paymentsConditional = $bankReceiptsDb->_getFilter($where);
                $this->view->paymentsConditional = $paymentsConditional;
                $this->view->coursesCondition = $coursesCondition;
            }

            //Data del Estudiante
            $dataStudent = array(   'uid'   => $uid,
                                    'pid'   => $pid,
                                    'semid' => $semid,
                                    'escid' => $escid,
                                    'subid' => $subid,
                                    'perid' => $perid,
                                    'eid'   => $eid,
                                    'oid'   => $oid,
                                    'curid' => $curid );
            $this->view->dataStudent = $dataStudent;
            

            $this->view->courses = $courses;
            $this->view->coursesName = $coursesName;
            $this->view->teachers = $teachers;
            $this->view->matriculaCondicional = $matriculaCondicional;    
        }catch(Exception $ex ){
            print ("Error Controlador Mostrar Datos: ".$ex->getMessage());
        } 
    }       


    public function deleteregisterAction(){
        $this->_helper->layout()->disableLayout();
        //Databases
        $registerDb = new Api_Model_DbTable_Registration();
        $registerxcourseDb = new Api_Model_DbTable_Registrationxcourse();
        //________________________

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $perid = $this->sesion->period->perid;
        $escid = base64_decode($this->_getParam('escid'));
        $subid = base64_decode($this->_getParam('subid'));
        $pid = base64_decode($this->_getParam('pid'));
        $uid = base64_decode($this->_getParam('uid'));
        $state = base64_decode($this->_getParam('state'));
        $regid = $uid.$perid;

        $where = array( 'eid'   => $eid, 
                        'oid'   => $oid, 
                        'pid'   => $pid, 
                        'uid'   => $uid, 
                        'escid' => $escid, 
                        'subid' => $subid,
                        'perid' => $perid,
                        'regid' => $regid,
                        'state' => $state );


        $data = array(  'modified' => $this->sesion->uid,
                        'updated'  => date('Y-m-d h:m:s') );

        if ($registerxcourseDb->_updatestateregister($data, $where)) {
            if ($registerDb->_update($data, $where)){
                $registerDb->_delete($where);
                echo 'true';
            }else{
                echo 'false';
            }
        }else{
            echo 'false';
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
        $endpoint = '/'.base64_encode('s3lf.040c0c030$0$0').'/'.base64_encode('__999c0n$um3r999__').'/validate';
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

    public function editturnoAction(){
        try {
            $this->_helper->layout()->disableLayout();

            $coursesPeriodsDb  = new Api_Model_DbTable_PeriodsCourses();
            $coursesTeachersDb = new Api_Model_DbTable_Coursexteacher();
            $personDb          = new Api_Model_DbTable_Person();


            $perid = $this->sesion->period->perid;
            $uid = base64_decode($this->_getParam('uid'));
            $pid = base64_decode($this->_getParam('pid'));
            $escid = base64_decode($this->_getParam('escid'));
            $subid = base64_decode($this->_getParam('subid'));
            $courseid = base64_decode($this->_getParam('courseid'));
            $curid = base64_decode($this->_getParam('curid'));
            $turno = base64_decode($this->_getParam('turno'));
            $eid = $this->sesion->eid;        
            $oid = $this->sesion->oid;

            $this->view->turno = $turno;

            $data = array(  'uid'      => $uid,
                            'pid'      => $pid,
                            'escid'    => $escid,
                            'subid'    => $subid,
                            'courseid' => $courseid,
                            'curid'    => $curid );
            $this->view->data = $data;
            //$regid=$where['uid'].$where['perid'];

            $where = array( 'eid'      => $eid,
                            'oid'      => $oid,
                            'perid'    => $perid,
                            'courseid' => $courseid,
                            'curid'    => $curid,
                            'escid'    => $escid,
                            'subid'    => $subid );
            $attrib = array('courseid', 'turno');
            $courses = $coursesPeriodsDb->_getFilter($where, $attrib);

            $turnos = count($courses);
            if ($turnos >= 2) {
                $c = 0;
                foreach ($courses as $course) {
                    $attrib = array('courseid', 'pid', 'uid');
                    $where = array( 'eid'=>$eid,
                                    'oid'=>$oid,
                                    'perid'=>$perid,
                                    'courseid'=>$course['courseid'],
                                    'curid'=>$curid,
                                    'escid'=>$escid,
                                    'subid'=>$subid,
                                    'turno'=>$course['turno'] );
                    $teacher = $coursesTeachersDb->_getFilter($where, $attrib);

                    $attrib = array('name');
                    $where = array( 'eid'=>$eid,
                                    'pid'=>$teacher[0]['pid'] );
                    $teachersInfo[$c] = $personDb->_getFilter($where);
                    $teachersInfo[$c]['turno'] = $course['turno'];

                    $c++;
                }
                $this->view->courses = $courses;
                $this->view->teachersInfo = $teachersInfo;
            }
        } catch (Exception $e) {
            print 'Error : '.$e->getMessage();            
        }
    }

    public function updatecourseperturnoAction(){
        try {
            $this->_helper->layout()->disableLayout();
            //DataBase
            $registercourseDb    = new Api_Model_DbTable_Registrationxcourse();
            $studentAssistanceDb = new Api_Model_DbTable_StudentAssistance();
            //_________________________________________________________

            $perid = $this->sesion->period->perid;
            $dataForm = $this->getRequest()->getPost();

            $eid = $this->sesion->eid;        
            $oid = $this->sesion->oid;


            $where = array( 'eid'      => $eid,
                            'oid'      => $oid, 
                            'uid'      => $dataForm['uid'],
                            'pid'      => $dataForm['pid'],
                            'escid'    => $dataForm['escid'],
                            'subid'    => $dataForm['subid'],
                            'courseid' => $dataForm['courseid'],
                            'curid'    => $dataForm['curid'],
                            'perid'    => $perid,
                            'regid'    => $dataForm['uid'].$perid );

            $data = array(  'modified' => $this->sesion->uid,
                            'turno'    => $dataForm['turno'],
                            'updated'  => date('Y-m-d h:m:s') );


            if ($registercourseDb->_update($data, $where)) {
                echo "true";
            }else {
                echo "false";
            }

        } catch (Exception $e) {
            print 'Error : '.$e->getMessage();
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

            $data = array(  'modified'=>$this->sesion->uid,
                            'updated'=>date('Y-m-d h:m:s'),
                            'state'=>'B' );
            $bdmatricula_curso = new Api_Model_DbTable_Registrationxcourse();  
            $bdmatricula_curso ->_update($data, $where);        

            //$this->_helper->_redirector("detail","registerstudent","register",array('uid' => base64_encode($where['uid']) ,'pid' => base64_encode($where['pid']),'escid' => base64_encode($where['escid']),'subid' => base64_encode($where['subid'])));

        }catch(Exception $ex ){
            print ("Error Controlador Mostrar Datos: ".$ex->getMessage());
        } 
    }

    public function validateAction(){

        $this->_helper->layout()->disableLayout();
        //Databases
        $registerDb = new Api_Model_DbTable_Registration();
        $registerxcourseDb = new Api_Model_DbTable_Registrationxcourse();
        //________________________

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $perid = $this->sesion->period->perid;
        $escid = base64_decode($this->_getParam('escid'));
        $subid = base64_decode($this->_getParam('subid'));
        $pid = base64_decode($this->_getParam('pid'));
        $uid = base64_decode($this->_getParam('uid'));
        $state = base64_decode($this->_getParam('state'));
        $regid = $uid.$perid;

        $where = array( 'eid'=>$eid,
                        'oid'=>$oid,
                        'escid'=>$escid, 
                        'subid'=>$subid, 
                        'perid'=>$perid, 
                        'regid'=>$regid,
                        'pid'=>$pid,
                        'uid'=>$uid,
                        'state'=>$state );

        $dataRegisterxCourse = array(   'modified'      => $this->sesion->uid,
                                        'approved'      => $this->sesion->uid,
                                        'approved_date' => date('Y-m-d h:m:s'),
                                        'updated'       => date('Y-m-d h:m:s'),
                                        'state'         => 'M' );
        if ($registerxcourseDb->_updatestateregister($dataRegisterxCourse, $where)) {
            $dataRegister = array(  'modified' => $this->sesion->uid,
                                    'updated'  => date('Y-m-d h:m:s'),
                                    'state'    => 'M' );
            if ($registerDb->_update($dataRegister, $where)){
                $json = array('status' => true );
            }else{
                $json = array('status' => false );
                $data = array(  'modified'=>'',
                                'updated'=>'',
                                'state'=>'I' );
                $registerxcourseDb->_updatestateregister($data, $where);
            }
        }else{
            $json = array('status' => false );
        }
        
        $json= Zend_Json::encode($json);
        $this->view->json=$json;
    }

    public function observedregisterAction(){

        $this->_helper->layout()->disableLayout();
        //Databases
        $registerDb = new Api_Model_DbTable_Registration();
        $registerxcourseDb = new Api_Model_DbTable_Registrationxcourse();
        //________________________

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $perid = $this->sesion->period->perid;
        $escid = base64_decode($this->getParam('escid'));
        $subid = base64_decode($this->getParam('subid'));
        $pid = base64_decode($this->getParam('pid'));
        $uid = base64_decode($this->getParam('uid'));
        $state = base64_decode($this->_getParam('state'));
        $regid = $uid.$perid;

        $where = array( 'eid'=>$eid,
                        'oid'=>$oid,
                        'escid'=>$escid, 
                        'subid'=>$subid, 
                        'perid'=>$perid, 
                        'regid'=>$regid,
                        'pid'=>$pid,
                        'uid'=>$uid,
                        'state'=>$state );

        $data = array(  'modified'=>$this->sesion->uid,
                        'updated'=>date('Y-m-d h:m:s'),
                        'state'=>'O' );


        if ($registerxcourseDb->_updatestateregister($data, $where)) {
            if ($registerDb->_update($data, $where)){
                echo 'true';
            }else{
               echo 'falso';
            }
        }else{
            echo 'falso';
        }
    }

    public function reservedregisterAction(){

        $this->_helper->layout()->disableLayout();
        //Databases
        $registerDb = new Api_Model_DbTable_Registration();
        $registerxcourseDb = new Api_Model_DbTable_Registrationxcourse();
        //________________________

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $perid = $this->sesion->period->perid;
        $escid = base64_decode($this->getParam('escid'));
        $subid = base64_decode($this->getParam('subid'));
        $pid = base64_decode($this->getParam('pid'));
        $uid = base64_decode($this->getParam('uid'));
        $state = base64_decode($this->_getParam('state'));

        $regid = $uid.$perid;

        $where = array( 'eid'=>$eid,
                        'oid'=>$oid,
                        'escid'=>$escid, 
                        'subid'=>$subid, 
                        'perid'=>$perid, 
                        'regid'=>$regid,
                        'pid'=>$pid,
                        'uid'=>$uid,
                        'state'=>$state );

        $data = array(  'modified'=>$this->sesion->uid,
                        'updated'=>date('Y-m-d h:m:s'),
                        'state'=>'R' );


        if ($registerxcourseDb->_updatestateregister($data, $where)) {
            if ($registerDb->_update($data, $where)){
                echo 'true';
            }else{
               echo 'falso';
            }
        }else{
            echo 'falso';
        }
    }

    public function coursespercurriculumAction(){
        $this->_helper->layout()->disableLayout();
        $coursesDb = new Api_Model_DbTable_Registrationxcourse();
        
        $pid   = $this->_getParam('pid');
        $uid   = $this->_getParam('uid');
        $escid = $this->_getParam('escid');
        $subid = $this->_getParam('subid');
        $curid = $this->_getParam('curid');

        $eid   = $this->sesion->eid;    
        $oid   = $this->sesion->oid;
        $perid = $this->sesion->period->perid;

        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'perid' => $perid,
                        'escid' => base64_decode($escid),
                        'subid' => base64_decode($subid),
                        'pid'   => base64_decode($pid),
                        'uid'   => base64_decode($uid) );
        $attrib = array('courseid');
        $courses = $coursesDb->_getFilter($where, $attrib);
        $this->view->courses = $courses;

        $request = array(   'eid' => base64_encode($eid),
                            'oid' => base64_encode($oid),
                            'perid' => base64_encode($perid),
                            'pid' => $pid,
                            'uid' => $uid,
                            'escid' => $escid,
                            'subid' => $subid,
                            'curid' => $curid );

        /*$server = new Eundac_Connect_Api('validate', $request);
        $data = $server->connectAuth();
        $this->view->data = $data;*/

        require_once 'Zend/Loader.php';
        Zend_Loader::loadClass('Zend_Rest_Client');

        $base_url = 'http://api.undac.edu.pe:8080/';
        $endpoint = '/'.base64_encode('s3lf.040c0c030$0$0').'/'.base64_encode('__999c0n$um3r999__').'/validate';
        $client = new Zend_Rest_Client($base_url);
        $httpClient = $client->getHttpClient();
        $httpClient->setConfig(array("timeout" => 30000));
        $response = $client->restget($endpoint,$request);
        $lista=$response->getBody();
        if ($lista){
            $data = Zend_Json::decode($lista);
            $this->view->data = $data;
        }
        
    }

    public function printAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $where['uid'] = base64_decode($this->_getParam('uid'));
            $where['pid'] = base64_decode($this->_getParam('pid'));
            $where['escid'] = base64_decode($this->_getParam('escid'));
            $where['subid'] = base64_decode($this->_getParam('subid'));
            $where['perid'] = $this->_getParam('perid',$this->sesion->period->perid);
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
               // $veces = new Api_Model_DbTable_Course();
                //$listusuario = $veces ->_getCoursesXStudentXV($cursomas); 
                //$cursomas['veces'] = intval($listusuario[0]['veces']);

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
                $listacurso1[]= $cursomas;
            }          
             $this->view->listacurso = $listacurso1;           

        }catch(Exception $ex ){
            print ("Error Controlador Mostrar Datos: ".$ex->getMessage());
        } 
    }

    public function printcopiafichaAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $where['uid'] = base64_decode($this->_getParam('uid'));
            $where['pid'] = base64_decode($this->_getParam('pid'));
            $where['escid'] = base64_decode($this->_getParam('escid'));
            $where['subid'] = base64_decode($this->_getParam('subid'));
            $where['perid'] = base64_decode($this->_getParam('perid'));
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
               // $veces = new Api_Model_DbTable_Course();
                //$listusuario = $veces ->_getCoursesXStudentXV($cursomas); 
                //$cursomas['veces'] = intval($listusuario[0]['veces']);

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
                $listacurso1[]= $cursomas;
            }          
             $this->view->listacurso = $listacurso1;           

        }catch(Exception $ex ){
            print ("Error Controlador Mostrar Datos: ".$ex->getMessage());
        } 
    }

    public function mobilityAction(){
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $uid = base64_decode($this->_getParam('uid'));
        $pid = base64_decode($this->_getParam('pid'));
        $escid = base64_decode($this->_getParam('escid'));
        $subid = base64_decode($this->_getParam('subid'));
        $perid = base64_decode($this->_getParam('perid'));
        $regid = $uid.$perid;
        $where = array(
            'eid' => $eid, 
            'oid' => $oid, 
            'uid' => $uid, 
            'pid' => $pid, 
            'escid' => $escid, 
            'subid' => $subid, 
            'perid' => $perid, 
            'regid' => $regid, 
            );
        $tb_registration_course = new Api_Model_DbTable_Registrationxcourse();
        $tb_assitance_course = new Api_Model_DbTable_StudentAssistance();
        $tb_periods_course = new Api_Model_DbTable_PeriodsCourses();
        $tb_user_infouser = new Api_Model_DbTable_UserInfoTeacher();
        $tb_course_teacher = new Api_Model_DbTable_Coursexteacher();
        $where_use = array(
                'eid'=>$eid,
                'oid'=>$oid,
                'escid'=>$escid,
                'is_director'=>'S'
                );
        $data_director =$tb_user_infouser->_getFilter($where_use); 
        $data_regiter_course = $tb_registration_course->_getFilter($where);
        if($data_regiter_course){
            foreach ($data_regiter_course as $key => $value) {
                $where_1 = array(
                        'eid'=>$value['eid'],
                        'oid'=>$value['oid'],
                        'perid'=>$value['perid'],
                        'courseid'=>$value['courseid'],
                        'curid'=>$value['curid'],
                        'escid'=>$value['escid'],
                        'subid'=>$value['subid'],
                        'turno'=>'M',
                    );
                $data_t = $where_1;
                $data_course = $tb_periods_course->_getOne($where_1);
                if (!$data_course) {
                    $where_1['turno'] =$value['turno']; 
                    $data_course = $tb_periods_course->_getOne($where_1);
                    $data_course['turno']='M';
                    $data_course['register']=$this->sesion->uid;
                    $data_course['modified']=$this->sesion->uid;
                    $data_course['state']='B';
                    unset($data_course['closure_date']);

                    if($tb_periods_course->_save($data_course)){
                        $where_1['turno']=$value['turno'];
                        $where_1['is_main']='S';
                        $data_course_teacher = $tb_course_teacher->_getFilter($where_1);
                        $data_course_teacher[0]['is_com']='S';
                        $data_course_teacher[0]['hours_total']= 0;
                        $data_course_teacher[0]['turno']= 'M';
                        $data_course_teacher[0]['pid']=$data_director[0]['pid'];
                        $data_course_teacher[0]['uid']=$data_director[0]['uid'];
                        if ($tb_course_teacher->_save($data_course_teacher[0])) {
                            $where_1['turno']=$value['turno'];
                            $where_1['coursoid']=$value['courseid'];
                            $where_1['pid']=$value['pid'];
                            $where_1['uid']=$value['uid'];
                            if ($tb_assitance_course->_delete($where_1)) {
                                $where_1['regid']=$value['regid'];
                                if ($tb_registration_course->_delete($where_1)) {
                                    unset($where_1['coursoid']);
                                    $data_t['turno']='M';
                                    $data_t['pid']=$value['pid'];
                                    $data_t['uid']=$value['uid'];
                                    $data_t['regid']=$value['regid'];
                                    $data_t['register']=$this->sesion->uid;
                                    $data_t['approved']=$this->sesion->uid;
                                    $data_t['modified']=$this->sesion->uid;
                                    $data_t['updated']=date('Y-m-d H:m:s');
                                    $data_t['state']='M';
                                    if ($tb_registration_course->_save($data_t)) {
                                        $json = array(
                                            'status' => true, 
                                            );
                                    }else{
                                        $json = array(
                                            'status' => false,
                                            'error'=>'Error al guardar curso nuevo'
                                        );
                                    }
                                }else{
                                    $json = array(
                                        'status' => false,
                                        'error'=>'Error al eliminar curso'
                                    );
                                }
                            }else{
                                $json = array(
                                    'status' => false,
                                    'error'=>'Error al eliminar asistencia'
                                );
                            }
                        }
                        else{
                            $json = array(
                            'status' => false,
                            'error'=>'Error al guardar docente'
                            );
                        }
                    }else{
                        $json = array(
                            'status' => false,
                            'error'=>'Error al guardar curso'
                        );
                    }
                }else{
                    $where_1['turno']=$value['turno'];
                    $where_1['coursoid']=$value['courseid'];
                    $where_1['pid']=$value['pid'];
                    $where_1['uid']=$value['uid'];
                    if ($tb_assitance_course->_delete($where_1)) {
                        $where_1['regid']=$value['regid'];
                        if ($tb_registration_course->_delete($where_1)) {
                            unset($where_1['coursoid']);
                            $where_1['turno']='M';
                            $where_1['register']=$this->sesion->uid;
                            $where_1['approved']=$this->sesion->uid;
                            $where_1['modified']=$this->sesion->uid;
                            $where_1['updated']=date('Y-m-d H:m:s');
                            $where_1['state']='M';
                            if ($tb_registration_course->_save($where_1)) {
                                $json = array(
                                    'status' => true, 
                                    );
                            }
                        }
                    }else{
                         unset($where_1['coursoid']);
                        $where_1['turno']='M';
                        $where_1['register']=$this->sesion->uid;
                        $where_1['approved']=$this->sesion->uid;
                        $where_1['modified']=$this->sesion->uid;
                        $where_1['updated']=date('Y-m-d H:m:s');
                        $where_1['regid']=$value['regid'];
                        $where_1['state']='M';
                        if ($tb_registration_course->_save($where_1)) {
                            $json = array(
                                'status' => true, 
                                );
                        }
                    }
                }
            }
        }
        else{
            $json = array(
                'status' => false,
                'error'=>'sin pre matricula', 
            );
        }
        exit();
        $this->_helper->layout()->disableLayout();
        $this->_response->setHeader('Content-Type', 'application/json');                   
        $this->view->json = Zend_Json::encode($json);

    }
}
