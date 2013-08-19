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
 
            $eid ="20154605046";//$this->sesion->eid;         
            $oid = "1";//$this->sesion->oid; 
            $facid = "4";//$this->sesion->facid;
            $perid='13A';//$this->sesion->perid;
            $this->view->facid=$facid;
            $this->view->eid=$eid;
            $this->view->oid=$oid;
            $db_esc = new Api_Model_DbTable_Registration();
            $lescuelas= $db_esc->_getTodasEscuelasconunt($eid,$oid,$perid,$facid);
            // print_r($lescuelas);  
            $this->view->lescuelas=$lescuelas;  
        }
        catch(Exception $ex)
        {
              print $ex->getMessage();
        }                  
                
    }
}
