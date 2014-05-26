<?php

class Register_StudentController extends Zend_Controller_Action {

    public function init()
    {
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
    }
    
    public function indexAction()
    {
        // print_r($this->sesion);
        try {
            echo $this->sesion->uid.$this->sesion->period->perid;
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid=$this->sesion->uid;
            $pid=$this->sesion->infouser['pid'];
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $perid=$this->sesion->period->perid;

            $where=array(
                        'eid'=>$eid, 'oid'=>$oid, 
                        'escid'=>$escid,'subid'=>$subid,
                        'pid'=>$pid,'uid'=>$uid,
                        'regid'=>$uid.$perid,
                        'perid'=>$perid);
            $where_payment = array(
                    'eid'=>$eid, 'oid'=>$oid, 
                    'escid'=>$escid,'subid'=>$subid,
                    'pid'=>$pid,'uid'=>$uid,
                    'perid'=>$perid
                );
            $base_registration = new Api_Model_DbTable_Registration();
            $base_payment= new Api_Model_DbTable_Payments();
            $regid = base64_encode($uid.$perid);
            $data = array(
                        'eid'=>$eid, 
                        'oid'=>$oid, 
                        'regid'=>$uid.$perid,
                        'pid'=>$pid,
                        'uid'=>$uid,
                        'escid'=>$escid,
                        'subid'=>$subid,
                        'perid'=>$perid,
                        'semid'=>0,
                        'credits'=>0,
                        'date_register'=>date('Y-m-d H:m:s'),
                        'register'=>$uid,
                        'created'=>date('Y-m-d H:m:s'),
                        'state'=>'B',
                        'count'=>0,
                    );
            $data_1 = array(
                    'eid'=>$eid, 
                    'oid'=>$oid, 
                    'uid'=>$uid,
                    'pid'=>$pid,
                    'escid'=>$escid,
                    'subid'=>$subid,
                    'perid'=>$perid,
                    'ratid'=>20,
                    'amount'=>0,
                    'register'=>$uid,
                    'created'=>date('Y-m-d H:m:s'),
            );
            if (is_array($base_registration->_getOne($where)) && is_array($base_payment->_getOne($where_payment))) {
                $this->_redirect("/register/student/start/regid/".$regid);
            }
            elseif (!is_array($base_registration->_getOne($where)) && !is_array($base_payment->_getOne($where))) {
                if ($base_registration->_save($data) && $base_payment->_save($data_1)) {
                    $this->_redirect("/register/student/start/regid/".$regid);
                }
            }elseif (!is_array($base_registration->_getOne($where))) {
                if ($base_registration->_save($data)) {
                    $this->_redirect("/register/student/start/regid/".$regid);
                }
            }elseif (!is_array($base_payment->_getOne($where))) {
                print_r($data_1);

                if ($base_payment->_save($data_1)) {
                    $this->_redirect("/register/student/start/regid/".$regid);
                }
            }
        } catch (Exception $e) {
            print "Error index Registration ".$e->$getMessage();
        }
    }
    
    public function startAction(){
        try {

            $eid=$this->sesion->eid;
            $oid= $this->sesion->oid;
            $escid=$this->sesion->escid;
            $perid=$this->sesion->period->perid;
            $uid=$this->sesion->uid;
            $pid=$this->sesion->infouser['pid'];
            $subid=$this->sesion->subid;
            $this->view->perid=$perid;
            $this->view->escid=$escid;
            $this->view->subid=$subid;

            if ($escid !='2YP' && $escid !='2DE' && $escid !='3EF' && $escid='2ESTA' && $escid !='4AM' && $escid !='5AG-Y' && $uid !='8822283375' && $uid !='0122277076' && $uid !='1244207110' && $uid !='1024103023' ) {
                $this->_redirect("/alumno/");
            }

            $regid=base64_decode($this->_getParam('regid'));
            $this->view->regid=$regid;
            $where=array(
                        'eid'=>$eid, 'oid'=>$oid, 
                        'escid'=>$escid,'subid'=>$subid,
                        'regid'=>$regid,
                        'pid'=>$pid,'uid'=>$uid,
                        'perid'=>$perid);

            $base_registration= new Api_Model_DbTable_Registration();
            $data_register = $base_registration->_getOne($where);
            $state = trim($data_register['state']);
            $deleted = trim($data_register['count']);
            $this->view->deleted=$deleted;
            $this->view->state=$state;

            $created_resolu=1;
            if ($data_register) {
                unset($where['regid']);
                $base_condition= new Api_Model_DbTable_Condition();
                $data_condition= $base_condition->_getAll($where);

                $condition_register= 0;
                $condition_credits=0;
                $condition_semester='3';

                $cont_conmment=null;

                if ($data_condition) {

                    foreach ($data_condition  as $condition) {
                        
                        if ($condition['num_registration'] !='') {
                            $cont_conmment['num_registration']="Usted esta Permitido llevar 
                                        un curso por  ". $condition['num_registration'].
                                        "  con Resolicion   ".$condition['doc_authorize'];
                            $condition_register = 1; 
                        }

                        if ($condition['credits'] !='') {
                            $cont_conmment['credits']="Usted tiene asignado".$condition['credits'].
                                                    "con Resolicion".$condition['doc_authorize'];
                            $condition_credits=intVal($condition['credits']);
                        }

                        if ($condition['semester']!='') {
                            $cont_conmment['semester']="Usted tiene Permitido llevar ".
                                                        $condition['semester']." con Resolicion".
                                                        $condition['doc_authorize'];
                            $condition_semester= trim($condition['semester']);
                        }
                    }
                }

                if ($cont_conmment) {
                    $this->view->cont_conmment=$cont_conmment;
                }

                $this->view->condition_register=$condition_register;
                $this->view->condition_credits=$condition_credits;
                // echo $condition_semester;
                // print_r($cont_conmment['num_registration']);
                $base_student_condition = new Api_Model_DbTable_Studentcondition();
                $student_condidtion = $base_student_condition->_getAll($where);


                $base_student_curricula = new  Api_Model_DbTable_Studentxcurricula();
                $student_curid=$base_student_curricula->_getOne($where);


                if($student_curid){

                    $curid=trim($student_curid['curid']);


                    $subject= $this->load_subject($curid,$perid,$condition_semester);

                    if ($student_condidtion) {
                                 
                        foreach ($subject as $key =>  $delete_subject) {
                            $Num = count($student_condidtion);
                            // $i=0;   
                            for ($k=0; $k < $Num; $k++) {
                                if ($delete_subject['courseid']== $student_condidtion[$k]['courseid']) {

                                    unset($subject[$key]);
                                 } 
                            }                                
                        }
                    }

                   
                    $base_registration_subjet = new Api_Model_DbTable_Registrationxcourse();
                    $where['regid']=$uid.$perid;
                    $where['curid']=$curid;
                    // $order="s";
                    $course_reg=$base_registration_subjet->_getAll($where);

                    $veces = 1 ;
                    $veces_subject = false;
                    if ($course_reg) {

                        if ($subject) {
                             foreach ($subject as $key => $course) {

                            $subject[$key]['veces_cur'] = null;
                            $subject[$key]['register']=0;

                            // $subject[$key]['veces'];
                            $N=count($course_reg);

                            for ($i=0; $i < $N; $i++) {

                                if($course['courseid'] == $course_reg[$i]['courseid'] && $course['turno'] == $course_reg[$i]['turno']){
                                    $subject[$key]['register']=1;
                                }
                               
                            }

                            if($course['veces'] >= 2)
                            {
                                $subject[$key]['veces_cur']=1;
                                $veces_subject = array(
                                                'veces'=> $course['veces'],
                                                'courseid'=>$course['courseid'],);
                                $veces = $course['veces'];
                            }


                        }
                        }
                        $this->view->veces = $veces;
                        $this->view->veces_subject=$veces_subject;
                    }
                    else{
                        $cantidad =count($subject);

                        
                        foreach ($subject as $key => $courses) {

                            if($courses['veces'] >= 2)
                            {
                                $subject[$key]['veces_cur']=1;
                                $veces_subject = array(
                                                'veces'=> $course['veces'],);
                                 
                                $veces = $courses['veces'];
                            }
                        }
                      
                        $this->view->veces = $veces;
                        $this->view->veces_subject=$veces_subject;

                    }


                    if ($perid != '13N' ) {
                            if($veces < 2 ){
                            
                            if ($data_register['semid']==0) {
                               $this->view->assign_semester=0;
                               $this->view->assign_credist=0;
                               $this->view->total_credits=0;
                            }
                            else{
                                    $assign_credist =   $base_registration->_get_Credits_Asignated($escid,$curid,$perid,$data_register['semid']);
                                    $this->view->assign_semester=$data_register['semid'];
                                    $this->view->total_credits=$data_register['credits'];
                                    $this->view->assign_credist=intval($assign_credist[0]['semester_creditsz'])+$condition_credits+$created_resolu;
                            }
                        }
                        else{

                            $this->view->assign_semester=$data_register['semid'];
                            $this->view->total_credits=$data_register['credits'];
                            $this->view->assign_credist=11+$condition_credits;
                            
                        }
                    }
                    else
                    {
                        $this->view->assign_semester=$data_register['semid'];
                        $this->view->total_credits=$data_register['credits'];
                        $this->view->assign_credist=11+$condition_credits;
                    }

                    $this->view->subjects = $subject;
                    $this->view->curid = $curid;

                    // print_r($subject);
                }
                else{
                    $this->view->error_cur="No tiene Curricula Asignada";
                }
                // print_r($student_curid['curid']);

            }



            $base_payment = new Api_Model_DbTable_Payments();
            $data_payment=$base_payment->_getOne($where);

            unset($where['perid']);
            $register_paymnets = $base_payment->_getAll($where);

            $this->view->register_paymnets=$register_paymnets;

            if ($data_payment) {

                if ($data_payment['amount']==0) {


                }

                $ratid  =   $data_payment['ratid'];
                $amount_payment = $data_payment['amount'];
                $date_payments = $data_payment['date_payment'];
                $base_rates = new Api_Model_DbTable_Rates();
                $where_payment  =   array(
                                    'eid'=>$eid,'oid'=>$oid,
                                    'ratid'=>$ratid,'perid'=>$perid);
                $assign_payment =   $base_rates->_getOne($where_payment);

                if ($assign_payment) {

                    $t_normal   =   $assign_payment['t_normal'];

                    $date_payment=strtotime($date_payments);
                    $f_fin_tn  =   strtotime($assign_payment['f_fin_tnd'].'11:59:00');
                    $f_fin_ti1  =   strtotime($assign_payment['f_fin_ti1'].'11:59:00'); 
                    $f_fin_ti2  =   strtotime($assign_payment['f_fin_ti2'].'11:59:00');

                    switch ($date_payment) {

                        case $date_payment < $f_fin_tn:
                            $amount_assing = $t_normal;
                            break;
                        case ($f_fin_tn < $date_payment && $date_payment < $f_fin_ti1):
                            $amount_assing = $assign_payment['t_incremento1'];
                            break;
                        case ($f_fin_ti1 < $date_payment && $date_payment < $f_fin_ti2):
                            $amount_assing = $assign_payment['t_incremento2'];
                            break;
                        default:
                            $amount_assing = "Monto no Aceptado";
                            break;
                    }  
                }

                if ($amount_payment >= $amount_assing) {
                    # code...
                    $this->view->amount_payment =  $amount_payment;
                    $this->view->amount_assing =   $amount_assing;
                }
                else{

                    $this->view->amount_assing =   $amount_assing;
                    $date = date("d-m-Y",$date_payments);
                    $amount_payment=$amount_payment ;

                    if ($amount_payment == 0) {

                        $this->view->date_payment="";
                        $diferencia = $amount_assing-$amount_payment;
                        $this->view->amount_payment = $amount_payment;
                        $this->view->diferencia=$diferencia;
                        $this->view->message_paymnet="Si el Pago sale 0 Soles significa que usted todavia no realizo ningun pago, comuniquese al siguiente correo informatica@undac.edu.pe si ustde pago.";
                    }
                    else{

                        $this->view->date=$date;
                        $diferencia = $amount_assing - $amount_payment;
                        $this->view->amount_payment = $amount_payment;
                        $this->view->diferencia=$diferencia;
                        $this->view->message_paymnet="Caso contrario debe hacer el deposito de la diferencia " .$diferencia. " Soles a la Cuenta 00000072 del Banco de la Nacion";
                    }
                }

                $this->view->name_reates=$assign_payment['name'];
                //print_r($data_payment);
            }
            else
            {
                $this->view->message_paymnet = "Error Las Tasas no Existen";
            }
            



        } catch (Exception $e) {
            print "Error start Registration ".$e->$getMessage();
        }
    }

    public  function regiterdeletedAction()
    {      
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=$this->sesion->infouser['pid'];
            $uid=$this->sesion->uid;

            $params = $this->getRequest()->getParams();
            $paramsdecode = array();
            foreach ( $params as $key => $value ){
                if($key!="module" && $key!="controller" && $key!="action"){
                    $paramsdecode[base64_decode($key)] = base64_decode($value);
                }
            }

            $params = $paramsdecode;
            $escid=trim($params['escid']);
            $subid=trim($params['subid']);
            $perid  =   trim($params['perid']);
            $regid  =   trim($params['regid']);
            $deleted1 = intVal(trim($params['deleted']));

            if ($deleted1 < 5) {

            $where = array(
                'eid'=>$eid,'oid'=>$oid,
                'pid'=>$pid,'uid'=>$uid,
                'escid'=>$escid,'subid'=>$subid,
                'perid'=>$perid,'regid'=>$regid,);


                $deleted = $deleted1+1;

                $data=array('count'=>$deleted);


                $base_registration = new Api_Model_DbTable_Registration();

                if ($base_registration->_update($data,$where)) {

                    $n = $base_registration->_delete($where);
                        $json = array(
                            'status'=>true,
                            'de'=>$deleted,
                            'deleted'=>$deleted1,
                        );
                }

              
            }
            else{

                $json = array(
                        'status'=>false,
                        );
            }

            try {
                
            } catch (Exception $e) {
                $json = array(
                        'status'=>false,
                        'error' => $e,
                        );
            }
            $this->_helper->layout->disableLayout();
            $this->_response->setHeader('Content-Type', 'application/json');   
            $this->view->data = $json;


    }
    
    
    public function registartionAction(){
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=$this->sesion->infouser['pid'];
            $uid=$this->sesion->uid;
            $params = $this->getRequest()->getParams();
            $paramsdecode = array();
            foreach ( $params as $key => $value ){
                if($key!="module" && $key!="controller" && $key!="action"){
                    $paramsdecode[base64_decode($key)] = base64_decode($value);
                }
            }
            $params = $paramsdecode;
            $escid=trim($params['escid']);
            $subid=trim($params['subid']);
            $perid  =   trim($params['perid']);
            $regid  =   trim($params['regid']);
            $total = intVal(trim($params['total']));
            $pk= array(
                'eid'=>$eid,'oid'=>$oid,
                'pid'=>$pid,'uid'=>$uid,
                'escid'=>$escid,'subid'=>$subid,
                'perid'=>$perid,'regid'=>$regid,
                    'uid'=>$uid,'pid'=>$pid);
            $data=array('state'=>"I",'approved'=>$uid,);
            

            try {

                $base_registration =  new Api_Model_DbTable_Registration();
                $base_registration_subjet =new Api_Model_DbTable_Registrationxcourse();
                
                if ($total > 0) {

                    if($base_registration_subjet->_update_pre_registration($data,$pk)){

                        $data1 = array('state'=>'I');
                        if ($base_registration->_update($data1,$pk)) {

                                $json = array(
                                    'status'=> true,
                                    'total'=>$total);

                        }else{
                            $json = array(
                                    'status'=> false,
                                    'total'=>$total);
                        }
                    }else{
                        $json = array(
                                    'status'=> false,
                                    'error'=>"matri curso");
                    }

                }
                else{

                    $json = array(
                        'status'=>false,
                        'total'=>$total
                        );
                }


            } catch (Exception $e) {
                $json = array(
                    'status'=>false,
                    'error'=>$e);
            }

            $this->_helper->layout->disableLayout();
            $this->_response->setHeader('Content-Type', 'application/json');   
            $this->view->data = Zend_Json::encode($json);


    }

    public function load_subject($curid='',$perid='',$semester=''){
        try {

            if ($curid=='' || $perid=='' ) return array();
            
            // echo $curid; exit();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid=$this->sesion->uid;
            $pid=$this->sesion->infouser['pid'];
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;

            $name_cache='register'.$uid;
            $frontendOptions = array(
                'lifetime' => 3600, // tiempo de vida de caché de 2 horas
            'automatic_serialization' => true
            );

            $ruta = "/tmp/mat/";
            if (!is_dir($ruta)){
                if (!mkdir($ruta, 0777)){
                    $ruta=$ruta;
                }
            }

            $backendOptions = array(
                'cache_dir' => "$ruta" // Carpeta donde alojar los archivos de caché
            );

            $cache = Zend_Cache::factory('Core',
                'File',
            $frontendOptions,
            $backendOptions);

            $where = array(
                        'uid' => base64_encode($uid), 
                        'pid' => base64_encode($pid),
                        'escid' => base64_encode($escid),
                        'subid' =>base64_encode($subid),
                        'eid' =>base64_encode($eid),
                        'oid' =>base64_encode($oid),
                        'perid'=>base64_encode($perid),
                        'curid'=>base64_encode($curid),
                        'semestre'=>base64_encode($semester));
            if(!$subject = $cache->load("$name_cache")) {

                // $server = new Eundac_Connect_Api('pendig_absolute',$where);
                // $subject = $server->connectAuth();
                // $cache->save($subject,$name_cache);
                require_once 'Zend/Loader.php';
                Zend_Loader::loadClass('Zend_Rest_Client');

                $base_url = 'http://api.undac.edu.pe:8080/';
                $route = '/'.base64_encode('s3lf.040c0c030$0$0').'/'.base64_encode('__999c0n$um3r999__').'/pendig_absolute';
                $client = new Zend_Rest_Client($base_url);
                $httpClient = $client->getHttpClient();
                $httpClient->setConfig(array("timeout" => 680));
                $response = $client->restget($route,$where);
                $lista=$response->getBody();
                $subject = Zend_Json::decode($lista);
                $cache->save($subject,$name_cache);

            }

            return $subject;
            
        } catch (Exception $e) {
            print "Error: in load subject".$e->getMessage();
        }
    }


    public function createdregisterAction(){

            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=$this->sesion->infouser['pid'];
            $uid=$this->sesion->uid;

            $params = $this->getRequest()->getParams();
            $paramsdecode = array();
            foreach ( $params as $key => $value ){
                if($key!="module" && $key!="controller" && $key!="action"){
                    $paramsdecode[base64_decode($key)] = base64_decode($value);
                }
            }

            $params = $paramsdecode;
            $curid=trim($params['curid']);
            $courseid=trim($params['courseid']);
            $escid=trim($params['escid']);
            $subid=trim($params['subid']);
            $turno  = trim($params['turno']);
            $perid  =   trim($params['perid']);
            $regid  =   trim($params['regid']);
            $veces = trim($params['veces']);
            $credits_cur = trim($params['credits']);
            $condition_credits = intVal(trim($params['condition']));
            
            $created_resolu=1;
            try {

                $where=array(
                        'eid'=>$eid, 'oid'=>$oid, 
                        'escid'=>$escid,'subid'=>$subid,
                        'regid'=>$regid,
                        'pid'=>$pid,'uid'=>$uid,
                        'perid'=>$perid);
            

                $base_registration_subjet = new Api_Model_DbTable_Registrationxcourse();
                $base_registration = new Api_Model_DbTable_Registration();


                    $credits_register = $base_registration -> _getOne($where);

                    
                   if ($perid != '13N' ) {
                         if($veces >= 2 ){

                        $credits_assing[0]['semester_creditsz']=11+$condition_credits;
                        }
                        else{
                            if($credits_register['semid'] != 0 && $veces < 2){
                                $credits_assing =   $base_registration -> _get_Credits_Asignated($escid,$curid,$perid,$credits_register['semid']);
                                $credits_assing[0]['semester_creditsz']=intVal($credits_assing[0]['semester_creditsz'])+$condition_credits+$created_resolu;
                            }
                            if( $credits_register['semid'] == 0 || $credits_register['semid']=="" )
                            {
                                $credits_assing[0]['semester_creditsz']=26;
                                if($this->sesion->escid=='3OB' || $this->sesion->escid=='3OT')
                                    $credits_assing[0]['semester_creditsz']=26;
                                else
                                    $credits_assing[0]['semester_creditsz']=22;
                            }
                            if( $credits_register['semid'] == 10 )
                            {
                                if($this->sesion->escid=='2EP' )
                                    $credits_assing[0]['semester_creditsz']=22;
                            }
                        } 
                    }else{
                        $credits_assing[0]['semester_creditsz']=11+$condition_credits;

                    }               

                    
                    
                    $credits_asing= intval($credits_assing[0]['semester_creditsz']);
                    $credits_val = intval($credits_register['credits']) + intval($credits_cur);


                    if($credits_asing >= $credits_val)
                    {   
                        $data = array(
                            'eid'=>$eid,'oid'=>$oid,
                            'escid'=>$escid,'subid'=>$subid,
                            'courseid'=>$courseid,'curid'=>$curid,
                            'perid'=>$perid,'turno'=>$turno,
                            'regid'=>$regid,'pid'=>$pid,
                            'uid'=>$uid,'register'=>$uid,
                            'approved'=>$uid,
                            'created'=>date('Y-m-d H:m:s'),
                            'state'=>'B');


                        if ($base_registration_subjet->_save($data)) {

                            $credits_register = $base_registration -> _getOne($where);

                            if ($perid != '13N') {
                                 if($veces >= 2 ){

                                    $credits_assing[0]['semester_creditsz']=11+$condition_credits;
                                    }
                                    else{
                                        if($credits_register['semid'] != 0 && $veces < 2){
                                            $credits_assing =   $base_registration -> _get_Credits_Asignated($escid,$curid,$perid,$credits_register['semid']);
                                            $credits_assing[0]['semester_creditsz']=intVal($credits_assing[0]['semester_creditsz'])+$condition_credits+$created_resolu;
                                        }
                                        if( $credits_register['semid'] == 0 )
                                        {
                                            if ($this->sesion->escid=='3OB' || $this->sesion->escid=='3OT')
                                                $credits_assing[0]['semester_creditsz']=26;
                                            else
                                                $credits_assing[0]['semester_creditsz']=22;
                                        }
                                        if( $credits_register['semid'] == 10 )
                                        {
                                            if($this->sesion->escid=='2EP' )
                                                $credits_assing[0]['semester_creditsz']=22;
                                        }
                                }
                            }else{
                                $credits_assing[0]['semester_creditsz']=11+$condition_credits;
                            }


                                $json   =   array(  
                                    'status'=>true,
                                    'total_credits'=>$credits_register['credits'],
                                    'semester'=>$credits_register['semid'],
                                    'credits_assing'=>$credits_assing[0]['semester_creditsz'],
                                    'suma'=>$credits_val
                                );
                    }
                    }
                    else{

                        $json   =   array(  
                            'status'=>false,
                            );
                            
                    }
                
            } catch (Exception $e) {
                $json = array(
                    'status'=>false,
                    'error'=>$e);
            }
            
            $this->_helper->layout->disableLayout();
            $this->_response->setHeader('Content-Type', 'application/json');   
            $this->view->data = $json; 

    }

    public function removeregisterAction()
    {
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=$this->sesion->infouser['pid'];
            $uid=$this->sesion->uid;

            $params = $this->getRequest()->getParams();
            $paramsdecode = array();
            foreach ( $params as $key => $value ){
                if($key!="module" && $key!="controller" && $key!="action"){
                    $paramsdecode[base64_decode($key)] = base64_decode($value);
                }
            }

            $params = $paramsdecode;
            $curid  =   trim($params['curid']);
            $courseid= trim($params['courseid']);
            $escid=     trim($params['escid']);
            $subid=     trim($params['subid']);
            $turno  = trim($params['turno']);
            $perid  =   trim($params['perid']);
            $regid  =   trim($params['regid']);
            $veces  = trim($params['veces']);
            $credits = trim($params['credits']);
            $condition_credits = intval(trim($params['condition']));

            $where_subjet = array(
                        'eid'=>$eid,'oid'=>$oid,
                        'escid'=>$escid,'subid'=>$subid,
                        'courseid'=>$courseid,'curid'=>$curid,
                        'perid'=>$perid,'turno'=>$turno,
                        'regid'=>$regid,'pid'=>$pid,
                        'uid'=>$uid,
                        );
            $created_resolu=1;



            try {

                $where=array(
                        'eid'=>$eid, 'oid'=>$oid, 
                        'escid'=>$escid,'subid'=>$subid,
                        'regid'=>$regid,
                        'pid'=>$pid,'uid'=>$uid,
                        'perid'=>$perid);

                $base_registration_subjet = new Api_Model_DbTable_Registrationxcourse();
                $base_registration = new Api_Model_DbTable_Registration();


                if ($base_registration_subjet->_delete($where_subjet)) {
                    
                            $credits_register = $base_registration -> _getOne($where);

                            if($credits_register['semid']!= 0 && $veces < 2){

                                $credits_assing =   $base_registration -> _get_Credits_Asignated($escid,$curid,$perid,$credits_register['semid']);

                                $credits_assing[0]['semester_creditsz']=intVal($credits_assing[0]['semester_creditsz'])+$condition_credits+$created_resolu;
                            }
                            else{

                                if($credits_register['semid']==0){
                                        $credits_assing[0]['semester_creditsz']=0;
                                    }
                                elseif($veces >= 2)
                                {
                                    $credits_assing[0]['semester_creditsz']=11+$condition_credits; 
                                }

                            }

                            $json   =   array(  
                                'status'=>true,
                                'total_credits'=>$credits_register['credits'],
                                'semester'=>$credits_register['semid'],
                                'credits_assing'=>$credits_assing[0]['semester_creditsz']
                                );
                }

             
                
            } catch (Exception $e) {
                $json = array(
                    'status'=>false,
                    'error'=>$e);
                
            }

            $this->_helper->layout->disableLayout();
            $this->_response->setHeader('Content-Type', 'application/json');   
            $this->view->data = $json;
        
    }

    public function printregisterAction()
    {
        try {
                $eid = $this->sesion->eid;
                $oid = $this->sesion->oid;
                $pid = $this->sesion->infouser['pid'];
                $uid = $this->sesion->uid;
                
                $namef = strtoupper($this->sesion->faculty->name);
                $fullname = $this->sesion->infouser['fullname'];

                $this->view->fullname   = $fullname;
                $this->view->uid = $uid;

                $escid = base64_decode($this->_getParam('escid'));
                $subid = base64_decode($this->_getParam('subid'));
                $regid = base64_decode($this->_getParam('regid'));
                $perid = base64_decode($this->_getParam('perid'));
                $curid = base64_decode($this->_getParam('curid'));
                $this->view->perid = $perid;

                $wheres=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
                $dbspeciality = new Api_Model_DbTable_Speciality();
                $speciality = $dbspeciality ->_getOne($wheres);
                $parent=$speciality['parent'];
                $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
                $parentesc= $dbspeciality->_getOne($wher);
                if ($parentesc) {
                    $pala='ESPECIALIDAD DE ';
                    $spe['esc']=$parentesc['name'];
                    $spe['parent']=$pala.$speciality['name'];
                }
                else{
                    $spe['esc']=$speciality['name'];
                    $spe['parent']='';  
                }
                $names=strtoupper($spe['esc']);
                $namep=strtoupper($spe['parent']);
                $namefinal=$names." <br> ".$namep;

                $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";
                
                $where = array(
                    'eid'=>$eid,'oid'=>$oid,
                    'escid'=>$escid,'subid'=>$subid,
                    'pid'=>$pid,'uid'=>$uid,
                    'regid'=>$regid,'perid'=>$perid,
                    'curid'=>$curid,
                    );
                $order = "courseid ASC";
                $base_registration_subjet = new Api_Model_DbTable_Registrationxcourse();
                $base_subjets_teacher = new Api_Model_DbTable_Coursexteacher();
                $base_subjets = new Api_Model_DbTable_Course();
                $base_person = new Api_Model_DbTable_Person();

                $data_subjects = $base_registration_subjet->_getAll($where,$order);
                $matricula = new Api_Model_DbTable_Registration();
                $wheremat = array(
                        'eid'=>$eid,'oid'=>$oid,
                        'escid'=>$escid,'subid'=>$subid,
                        'pid'=>$pid,'uid'=>$uid,
                        'regid'=>$regid,'perid'=>$perid,
                );
                $regmatr = $matricula->_getRegister($wheremat);

                if ($regmatr) $this->view->regmatr = $regmatr;

                // $attrib =array('pid','last_name0');

                foreach ($data_subjects as $key => $value) {
                    $where = array(
                        'eid'=>$eid,'oid'=>$oid,
                        'curid'=>$value['curid'],
                        'escid'=>$value['escid'],
                        'subid'=>$value['subid'],
                        'courseid'=>$value['courseid'],
                        'turno' =>$value['turno'],
                        'perid' => $perid,);

                    $info_subjects =  $base_subjets->_getOne($where);
                    $data_subjects [$key]['name'] = $info_subjects['name'];
                    $data_subjects [$key]['type'] = $info_subjects['type'];
                    $data_subjects  [$key]['credits'] = $info_subjects['credits'];
                    $data_subjects [$key]['semid'] = $info_subjects['semid'];

                    $data_pid_teacher = $base_subjets_teacher ->_getinfoDoc($where);

                   $data_subjects [$key]['name_t']  = $data_pid_teacher[0]['nameteacher'];

                } 
                $this->view->data_subjects  =   $data_subjects;

                $dbimpression = new Api_Model_DbTable_Countimpressionall();
                
                $uidim=$this->sesion->pid;

                $data = array(
                    'eid'=>$eid,
                    'oid'=>$oid,
                    'uid'=>$uid,
                    'escid'=>$escid,
                    'subid'=>$subid,
                    'pid'=>$pid,
                    'type_impression'=>'prematricula',
                    'date_impression'=>date('Y-m-d h:m:s'),
                    'pid_print'=>$uidim
                    );
                $dbimpression->_save($data);

                $wheri = array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'prematricula');
                $dataim = $dbimpression->_getFilter($wheri);
                
                $co=count($dataim);
                $codigo=$co." - ".$uidim;
                
                $header=$this->sesion->org['header_print'];
                $footer=$this->sesion->org['footer_print'];
                $header = str_replace("?facultad",$namef,$header);
                $header = str_replace("?escuela",$namefinal,$header);
                $header = str_replace("?logo", $namelogo, $header);
                $header = str_replace("?codigo", $codigo, $header);

                $this->view->codigo=$codigo;
                $this->view->header=$header;
                $this->view->footer=$footer;
                $this->_helper->layout->disableLayout();

        } catch (Exception $e) {
            print "Error: print register".$e->getMessage();
        }
    }

    

}
