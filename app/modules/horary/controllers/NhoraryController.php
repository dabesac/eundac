<?php

class Horary_NhoraryController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		//$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (!$login->rol['module']=="horary"){
    		//$this->_helper->redirector('index','index','default');
    	}
    	$this->sesion = $login;
    
    }
    public function listteacherAction()
    {
        try {
            
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid=$this->sesion->period->perid;
            $escid=$this->sesion->escid;
             $d=new Api_Model_DbTable_PeriodsCourses();
             $where['eid']=$eid;
             $where['oid']=$oid;
             $where['perid']=$perid;
             $where['escid']=$escid;

            $cur=$d->_getTeacherXPeridXEscid($eid,$oid,$escid,$perid);
            //$cur=$d->_getTeacherXPeridXEscid1($where);
            //print_r($cur);
            $this->view->docente=$cur;
            

            $this->view->cursos=$datcur;
        } catch (Exception $ex) {
            print $ex->getMessage();
        }
    }
    
   
}