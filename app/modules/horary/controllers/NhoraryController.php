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
    public function indexAction()
    {    
     $this->_helper->redirector("listteacher");   

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

    public function fillhoraryAction()
    {
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $perid=$this->sesion->period->perid;
        $escid=$this->sesion->escid;
        $subid=$this->sesion->subid;
        $pid=base64_decode($this->_getParam('pid'));
        $uid=base64_decode($this->_getParam('uid'));
        $where['eid']=$eid;
        $where['oid']=$oid;
        $where['perid']=$perid;
        $where['escid']=$escid;
        $where['subid']=$subid;
        $where['pid']=$pid;
        $curso=new Api_Model_DbTable_Coursexteacher();
        $datcur=$curso->_getFilter($where);
        $this->view->cursos=$datcur;
        $usu=new Api_Model_DbTable_Users();
        $data['eid']=$eid;
        $data['oid']=$oid;
        $data['uid']=$uid;
        $dusu=$usu->_getUserXUid($data);
        $this->view->usuario=$dusu;
        $hora=new Api_Model_DbTable_Horary();
        $wher['eid']=$eid;
        $wher['oid']=$oid;
        $wher['perid']=$perid;
        $wher['subid']=$subid;
        $wher['teach_pid']=$pid;
        $wher['teach_uid']=$uid;

        $dathora=$hora->_getFilter($wher);
        if ($dathora) $this->view->dathora=1;
    }

    public function horaryteacherAction()
    {    
       

    }
    
   
}