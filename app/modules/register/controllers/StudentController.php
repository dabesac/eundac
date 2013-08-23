<?php

class Register_StudentController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
   //  	if (!$login->modulo=="alumno"){
   // 		$this->_helper->redirector('index','index','default');
  	// }
    	$this->sesion = $login;
    }
    public function indexAction()
    {
        // print_r($this->sesion);
        try {
            // print_r($this->sesion->infouser['pid']);
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
            $base_payment= new Api_Model_DbTable_Payments();
        

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

            unset($where['regid']);
            if (!$base_payment->_getOne($where)) {

                $where['ratid']=39;
                $where['amount']=0;
                $where['register']=$uid;
                $where['created']=date("Y-m-d");
                if ($base_payment->_save($where))
                    $regid=base64_encode($uid.$perid);
            }
            
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

            $regid=base64_decode($this->_getParam('regid'));
            $where=array(
                        'eid'=>$eid, 'oid'=>$oid, 
                        'escid'=>$escid,'subid'=>$subid,
                        'regid'=>$regid,
                        'pid'=>$pid,'uid'=>$uid,
                        'perid'=>$perid);
            $base_registration= new Api_Model_DbTable_Registration();
            $data_register = $base_registration->_getOne($where);

            if ($data_register) {
                $this->view->state=trim($date['state']);
                unset($where['regid']);
                $base_condition= new Api_Model_DbTable_Condition();
                $data_condition= $base_condition->_getAll($where);
                if ($data_condition) {
                    # code...
                }
                $base_student_condition = new Api_Model_DbTable_Studentcondition();
                $data_student_condidtion = $base_student_condition->_getAll($where);
                if ($data_student_condidtion) {
                    print_r($data_student_condidtion);
                }
            }

            $base_payment = new Api_Model_DbTable_Payments();
            $data_payment=$base_payment->_getOne($where);
            if ($data_payment) {
                if ($data_payment['amount']=='0') {

                }
                

            }
            // $attib=array('eid','eetete');
            // print_r($data_payment);

        } catch (Exception $e) {
            print "Error start Registration ".$e->$getMessage();
        }
    }

    public function _subject(){
        
    }
    
}
