<?php

class Register_StudentController extends Zend_Controller_Action {

    public function init()
    {
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
   //   if (!$login->modulo=="alumno"){
   //       $this->_helper->redirector('index','index','default');
    // }
        $this->sesion = $login;
        
    }
    public function indexAction()
    {
        // print_r($this->sesion);
        try {
            print_r($this->sesion->infouser['pid']);
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
                        'regid'=>$uid.$perid,
                        'pid'=>$pid,'uid'=>$uid,
                        'perid'=>$perid);
            
            $base_registration = new Api_Model_DbTable_Registration();
            // $base_payment= new Api_Model_DbTable_Payments();
        

            if (!$base_registration->_getOne($where)) {
                $where['semid']=0;
                $where['credits']=0;
                $where['register']=$uid;
                $where['created']=date("Y-m-d H:m:s");
                $where['state']='B';
                $where['date_register']=date("Y-m-d H:m:s");
                if ($base_registration->_save($where)) 
                    $regid=base64_encode($uid.$perid);
            }

            // unset($where['regid']);
            // if (!$base_payment->_getOne($where)) {

            //     $where['ratid']=39;
            //     $where['amount']=0;
            //     $where['register']=$uid;
            //     $where['created']=date("Y-m-d");
            //     if ($base_payment->_save($where))
            //         $regid=base64_encode($uid.$perid);
            // }
            
            $regid=base64_encode($uid.$perid);

            $this->_redirect("/register/Student/start/regid/".$regid);
            
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

            if ($data_register) {
                $this->view->state=base64_encode(trim($data_register['state']));
                unset($where['regid']);
                $base_condition= new Api_Model_DbTable_Condition();
                $data_condition= $base_condition->_getAll($where);

                if ($data_condition) {
                    foreach ($data_condition  as $condition) {
                        if ($condition['num_registration'] !='') {
                            $cont_conmment['num_registration']="Usted esta Permitido llevar 
                                        un curso por ".$condition['num_registration'].
                                        "con Resolicion".$condition['doc_authorize'];
                        }
                        if ($condition['credits'] !='') {
                            $cont_conmment['credits']="Usted tiene asignado".$condition['credits'].
                                                    "con Resolicion".$condition['doc_authorize'];
                        }
                        if ($condition['semester']!='') {
                            $cont_conmment['semester']="Usted tiene Permitido llevar ".
                                                        $condition['semester']."con Resolicion".
                                                        $condition['doc_authorize'];
                        }
                    }
                }

                $base_student_condition = new Api_Model_DbTable_Studentcondition();
                $data_student_condidtion = $base_student_condition->_getAll($where);
                if ($data_student_condidtion) {

                    foreach ($data_student_condidtion as $key =>  $delete_subject) {
                            $cont_conmment[$key]['subject']="Algunos curso eliminar del listado con Resolicion".$delete_subject['doc_authorize'];                            
                    }
                }

                $base_student_curricula = new  Api_Model_DbTable_Studentxcurricula();
                $student_curid=$base_student_curricula->_getOne($where);

                if($student_curid){

                    $curid=trim($student_curid['curid']);

                    $subject= $this->load_subject($curid,$perid,'3');

                    $base_registration_subjet = new Api_Model_DbTable_Registrationxcourse();
                    $where['regid']=$uid.$perid;
                    $where['curid']=$curid;
                    // $order="s";
                    $course_reg=$base_registration_subjet->_getAll($where);
                    $veces = null ;

                    if ($course_reg) {
                        foreach ($subject as $key => $course) {

                            $N=count($course_reg);
                            for ($i=0; $i < $N; $i++) { 
                                if($course['courseid']==$course_reg[$i]['courseid']){
                                    $subject[$key]['register']=1;
                                }
                            }

                            if($course['veces'] >= 2)
                            {
                                $veces_cur = array(
                                    'veces'=>$course['veces'],
                                    'courseid'=>$course['courseid'],
                                    );
                                $veces = $course['veces'];
                            }

                        }

                        $this->view->veces = $veces;
                        $this->view->veces_cur = $veces_cur;


                    }
                    else{

                        foreach ($subject as $key => $courses) {
                            if($courses['veces'] >= 2)
                            {
                                $veces_cur = array(
                                    'veces'=>$courses['veces'],
                                    'courseid'=>$courses['courseid'],
                                    );
                                $veces = $courses['veces'];

                            }
                        }
                        $this->view->veces = $veces;
                        $this->view->veces_cur = $veces_cur;
                    }

                    if($veces =='' ){
                        if ($data_register['semid']==0) {
                           $this->view->assign_semester=0;
                           $this->view->assign_credist=0;
                           $this->view->total_credits=0;
                        }
                        else{
                                $assign_credist =   $base_registration->_get_Credits_Asignated($escid,$curid,$perid,$data_register['semid']);
                                $this->view->assign_semester=$data_register['semid'];
                                $this->view->total_credits=$data_register['credits'];
                                $this->view->assign_credist=$assign_credist[0]['semester_credits'];
                        }
                    }
                    else{

                        $this->view->assign_semester=$data_register['semid'];
                        $this->view->total_credits=$data_register['credits'];
                        $this->view->assign_credist=11;
                        
                    }

                    $this->view->subjects=$subject;

                    // print_r($subject);
                }
                else{
                    $this->view->error="No tiene Curricula Asignada";
                }
                // print_r($student_curid['curid']);

            }

            $base_payment = new Api_Model_DbTable_Payments();
            $data_payment=$base_payment->_getOne($where);
            if ($data_payment) {
                if ($data_payment['amount']=='0') {



                }
            }


        } catch (Exception $e) {
            print "Error start Registration ".$e->$getMessage();
        }
    }

    public function _credits(){
        try {
            
        } catch (Exception $e) {
            print "Error: aassign credits".$e->getMessage();
        }


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


            $where = array(
                        'uid' => $uid, 'pid' => $pid,
                        'escid' => $escid,'subid' =>$subid,
                        'eid' =>$eid,'oid' =>$oid,
                        'perid'=>$perid,'curid'=>$curid,
                        'semestre'=>$semester);
            
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass('Zend_Rest_Client');
            $base_url = 'http://localhost:8080/';
            $route = '/s1st3m4s/und4c/pendig_absolute';
            $client = new Zend_Rest_Client($base_url);
            $httpClient = $client->getHttpClient();
            $httpClient->setConfig(array("timeout" => 680));
            $response = $client->restget($route,$where);
            $lista=$response->getBody();
            $subject = Zend_Json::decode($lista);

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

            $data = array(
                        'eid'=>$eid,'oid'=>$oid,
                        'escid'=>$escid,'subid'=>$subid,
                        'courseid'=>$courseid,'curid'=>$curid,
                        'perid'=>$perid,'turno'=>$turno,
                        'regid'=>$regid,'pid'=>$pid,
                        'uid'=>$uid,'register'=>$uid,
                        'created'=>date('Y-m-d H:m:s'),
                        'state'=>'B');
            // // print_r($data);exit();
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

                    if($veces >= 2 ){

                        $credits_assing[0]['semester_credits']=11;
                    }
                    else{

                        if($credits_register['semid']!=0 && $veces < 2){
                            $credits_assing =   $base_registration -> _get_Credits_Asignated($escid,$curid,$perid,$credits_register['semid']);
                        }

                        elseif ($credits_register==0) {
                                $credits_assing[0]['semester_credits']=0;
                        }
                    }
                    
                    $credits_asing= intval($credits_assing[0]['semester_credits']);
                    $credits_val = intval($credits_register['credits']) + intval($credits_cur);

                    if($credits_val >= $credits_asing)
                    {
                        $json   =   array(  
                                    'status'=>true,
                                    'total_credits'=>$credits_register['credits'],
                                    'semester'=>$credits_register['semid'],
                                    'credits_assing'=>$credits_assing[0]['semester_credits'],
                                    'credits'=>$credits_asing
                                    );

                    }
            //     //         // if ($base_registration_subjet->_save($data)) {

            //     //         //     $credits_register = $base_registration -> _getOne($where);

            //     //         //         if($credits_register['semid']!=0 && $veces < 2)
            //     //         //             $credits_assing =   $base_registration -> _get_Credits_Asignated($escid,$curid,$perid,$credits_register['semid']);
            //     //         //         else{
            //     //         //             if($credits_register['semid']==0){
            //     //         //                 $credits_assing[0]['semester_credits']='0';
            //     //         //             }
            //     //         //             elseif ($veces >= 2) {
            //     //         //                 $credits_assing[0]['semester_credits']=11;
            //     //         //             }
            //     //         //         }

                                
            //     //         // }
            //     //     }
                
            //     // else{

            //     //     $credits = $base_registration -> _getOne($where);
            //     //     if($credits['semid']!=0)
            //     //         $credits_assing =   $base_registration -> _get_Credits_Asignated($escid,$curid,$perid,$credits['semid']);
            //     //     else
            //     //         $credits_assing[0]['semester_credits']='0';

            //     //     $json   =   array(  
            //     //                         'status'=>false,
            //     //                         'total_credits'=>$credits['credits'],
            //     //                         'semester'=>$credits['semid'],
            //     //                         'credits_assing'=>$credits_assing[0]['semester_credits'],
            //     //                         'credits'=>$credits_asing);
            //     // }
                
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

            $where_subjet = array(
                        'eid'=>$eid,'oid'=>$oid,
                        'escid'=>$escid,'subid'=>$subid,
                        'courseid'=>$courseid,'curid'=>$curid,
                        'perid'=>$perid,'turno'=>$turno,
                        'regid'=>$regid,'pid'=>$pid,
                        'uid'=>$uid,
                        );

            
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
                    if($credits_register['semid']!=0)
                        $credits_assing =   $base_registration -> _get_Credits_Asignated($escid,$curid,$perid,$credits_register['semid']);
                    else
                        $credits_assing[0]['semester_credits']='0';

                        $json   =   array(  
                                        'status'=>true,
                                        'credits'=>$credits_register['credits'],
                                        'semester'=>$credits_register['semid'],
                                        'credits_assing'=>$credits_assing[0]['semester_credits']);
                }
                else{

                    $credits_register = $base_registration -> _getOne($where);
                    if($credits_register['semid']!=0)
                        $credits_assing =   $base_registration -> _get_Credits_Asignated($escid,$curid,$perid,$credits_register['semid']);
                    else
                        $credits_assing[0]['semester_credits']='0';

                        $json   =   array(  
                                        'status'=>true,
                                        'credits'=>$credits_register['credits'],
                                        'semester'=>$credits_register['semid'],
                                        'credits_assing'=>$credits_assing[0]['semester_credits']);
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

    

}
