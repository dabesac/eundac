<?php

class Rfacultad_IndexController extends Zend_Controller_Action {

    public function init()
    {
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        if (!$login->rol['module']=="rfacultad"){
            $this->_helper->redirector('index','index','default');
        }
        $this->sesion = $login; 
            }
    public function indexAction()
    {
        $perid=$this->sesion->period->perid;
        $this->view->perid=$perid;
    }


        public function graphicschoolAction()
    {
        try
        {

            $this->_helper->layout()->disableLayout();
 
            $where['eid'] =$this->sesion->eid;        
            $where['oid'] = $this->sesion->oid;
            $where['facid'] = $this->sesion->faculty->facid;
            $where['perid']= $this->sesion->period->perid;
            $this->view->facid=$where['facid'];
            $this->view->eid=$where['eid'];
            $this->view->oid=$where['oid'];
            $db_esc = new Api_Model_DbTable_Registration();
            $lschool= $db_esc->_totalSchoolEnrollment($where);
            $this->view->lschool=$lescuelas;  
        }
        catch(Exception $ex)
        {
              print $ex->getMessage();
        }                  
                
    }
}
