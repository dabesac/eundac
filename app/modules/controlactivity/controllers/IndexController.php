<?php

class Controlactivity_IndexController extends Zend_Controller_Action {

    public function init()
    {
      $sesion  = Zend_Auth::getInstance();
      if(!$sesion->hasIdentity() ){
        $this->_helper->redirector('index',"index",'default');
      }
      $login = $sesion->getStorage()->read();
      if (!$login->rol['module']=="docente"){
        $this->_helper->redirector('index','index','default');
      }
      $this->sesion = $login;   
    }
    
    public function indexAction()
    {
        try{

        }
            catch (Exception $e) {           
        }
    }
    public function datajsonAction(){

        $tb_horary = new Api_Model_DbTable_Horary();
        $where = array(
            'eid'=>$this->sesion->eid,
            'oid'=>$this->sesion->oid,
            'uid'=>$this->sesion->uid,
            'pid'=>$this->sesion->pid,
            'perid'=>$this->sesion->period->perid
            ); 
        $horary_syllabus = $tb_horary->_get_horary_x_syllabus($where);
        $where_1 = array(   
            'eid'=>$this->sesion->eid,
            'oid'=>$this->sesion->oid,
            'perid'=>$this->sesion->period->perid,
            );
        $tb_periods = new Api_Model_DbTable_Periods();
        $data_period = $tb_periods->_getOnePeriod($where_1);
        $data = array(
            'content'=>$horary_syllabus,
            'period'=>$data_period
            );
        $this->_helper->layout()->disableLayout();
        $this->_response->setHeader('Content-Type', 'application/json'); 
        $this->view->data = Zend_Json::encode($data);
    }
    public function saveAction()
    {
       
            $this->_helper->layout()->disableLayout();

            
    }


}