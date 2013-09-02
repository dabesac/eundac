<?php

class Alumno_IndexController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (!$login->rol['module']=="alumno"){
    		$this->_helper->redirector('index','index','default');
    	}
    	$this->sesion = $login;
        // print_r($this->sesion);
    }
    public function indexAction()
    {

    }


    public function graphicsperformanceAction()
    {
        try
        {
        $this->_helper->layout()->disableLayout();         
            $where['uid']=$this->sesion->uid;
            $where['eid']=$this->sesion->eid;
            $where['oid']=$this->sesion->oid;
            $where['pid']=$this->sesion->pid;
            $where['escid']=$this->sesion->escid;
            $where['subid']=$this->sesion->subid;
        $perid = $this->sesion->period->perid;

            $this->view->escid = $escid;
            $this->view->perid = $perid;
            $this->view->uid = $uid;
            $this->view->oid = $oid;
            $this->view->eid = $eid;
            $dbcurricula=new Api_Model_DbTable_Studentxcurricula();
            $datcur=$dbcurricula->_getOne($where);
            $where['curid']=$datcur['curid'];
            $this->view->curid = $curid;
            $dbcursos=new Api_Model_DbTable_Course();
            $datcursos=$dbcursos->_getCountCoursesxSemester($where);
            // print_r($datcursos);
            $this->view->data=$datcursos;
            $cur=$dbcursos->_getCountCoursesxApproved($where);
            // print_r($cur);
            $this->view->cursos=$cur;     
        }
        catch(Exception $ex)
        {
              print $ex->getMessage();
        }                  
                
    }


}
