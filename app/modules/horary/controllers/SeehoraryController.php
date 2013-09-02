<?php

class Horary_SeehoraryController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
			$this->_helper->redirector('index',"index",'default');
    	}
    	
    	$this->sesion = $login;
    
    }
    public function indexAction()
    {
        try {
          //print_r($this->sesion);
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid=$this->sesion->period->perid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $pid=$this->sesion->pid;
            $uid=$this->sesion->uid;
            $hora=new Api_Model_DbTable_Horary();
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['perid']=$perid;
            $where['subid']=$subid;
            $where['teach_uid']=$uid;
            $where['teach_pid']=$pid;
            //print_r($where);
            $dathora=$hora->_getFilter($where);
            //print_r($dathora);
            $this->view->horarios=$dathora;
            $curso=new Api_Model_DbTable_Coursexteacher();
            $where1['eid']=$eid;
            $where1['oid']=$oid;
            $where1['escid']=$escid;
            $where1['perid']=$perid;
            $where1['subid']=$subid;
            $where1['pid']=$pid;
            $datcur=$curso->_getFilter($where1);
            //print_r($datcur);

            $this->view->cursos=$datcur;
        } catch (Exception $ex) {
            print $ex->getMessage();
        }
    }
    
   
}
