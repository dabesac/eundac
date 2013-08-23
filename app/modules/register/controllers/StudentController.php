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
        print_r($this->sesion);
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $uid=$this->sesion->uid;
        $pid=$this->sesion->infouser->pid;
        $escid=$this->sesion->escid;
        $subid=$this->sesion->subid;
        $perid=$this->sesion->period->perid;

        $where=array(
                    'eid'=>$eid, 'oid'=>$oid, 
                    'escid'=>$escid,'subid'=>$subid,
                    'regid'=>$uid.$perid,
                    'pid'=>$pid,'uid'=>$uid,
                    'perid'=>$perid);
        
        

    }
}
