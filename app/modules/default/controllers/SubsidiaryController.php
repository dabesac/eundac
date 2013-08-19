<?php

class SubsidiaryController extends Zend_Controller_Action 
{
   public function indexAction() 
    {
        try
        {
            $eid="20154605046";
            $oid="1";
            $sub= new Admin_Model_DbTable_Subsidiary();
            $datasub=$sub->_listAllSub($eid,$oid);
            //print_r($datosede);
            //$this->view->datasub=$datasub;
        }
        catch (Exception $ex) 
        {
          print "Error al Listar ! ";
        }
    }

 }