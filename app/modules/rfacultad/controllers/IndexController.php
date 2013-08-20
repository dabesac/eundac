<?php

class Rfacultad_IndexController extends Zend_Controller_Action {

    public function init()
    {
       
    }
    public function indexAction()
    {
    

    }


        public function graphicschoolAction()
    {
        try
        {

            $this->_helper->layout()->disableLayout();
 
            $where['eid'] ="20154605046";//$this->sesion->eid;         
            $where['oid'] = "1";//$this->sesion->oid; 
            $where['facid'] = "4";//$this->sesion->facid;
            $where['perid']='13A';//$this->sesion->perid;
            $this->view->facid=$facid;
            $this->view->eid=$eid;
            $this->view->oid=$oid;
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
