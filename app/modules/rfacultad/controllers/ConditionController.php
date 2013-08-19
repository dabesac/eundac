<?php

class Rfacultad_ConditionController extends Zend_Controller_Action {

    public function init()
    {
       
    }

   	public function indexAction()
    {
    	try
        {
        	$eid="20154605046";
        	$oid="1";
        	$facid="4";
            $subid="1901";
            $perid="13A";
        	$where['eid']=$eid;
        	$where['oid']=$oid;
        	$where['facid']=$facid;
            $where['state']="A";
        	$where['subid']=$subid;
        	$this->view->perid=$perid;
            $this->view->subid=$subid;
            $this->view->facid=$facid;
           	$esp = new Api_Model_DbTable_Speciality();
            $gesp = $esp->_getFilter($where);
            if ($gesp ) $this->view->getEsp=$gesp;

            $form=new Rfacultad_Form_Getcond();
           	$form->send->setLabel("Buscar");
            $this->view->form=$form;


        }  
        catch (Exception $ex)
        {
            print "Error: Cargar Periodos".$ex->getMessage();
        }

    }

    //  public function getspecAction()
    // {
    // 	try
    //     {
    //         $this->_helper->layout()->disableLayout();
    //         $eid="20154605046";
    //     	$oid="1";
    //     	$subid = "1901";
    //     	$facid="4";
    //     	$state="A";
    //     	$where['eid']=$eid;
    //     	$where['oid']=$oid;
    //     	$where['facid']=$facid;
    //     	$where['state']=$state;
    //         $db_esp = new Api_Model_DbTable_Especiality();
    //         if ($subid == '1901')
    //         {
    //             $esp = $db_esp->_getFilter($where);
    //             $this->view->esp = $esp;
    //         }
    //         else
    //         {
    //             $escuelas = $db_esc->_getEscuelaXSede($eid,$oid,$sedid);
    //             $this->view->escuelas = $escuelas;
    //         }

    //     }
    //     catch (Exception $ex)
    //     {
    //         print "Error : ".$ex->getMessage();
    //     }

    // }

    // public function getstudentAction()
    // {
    // 	try
    //     {
    //     	$this->_helper->getHelper('layout')->disableLayout();
    //     	$name = trim(strtoupper($this->_getParam('name')));
    //         $ap = trim(strtoupper($this->_getParam('ap')));        
    //         $am = trim(strtoupper($this->_getParam('am')));        
    //         $code = $this->_getParam('uid');        
    //         $escid=$this->_getParam('escid');   
    //         $cond=$this->_getParam('cond');
    //         //print_r ($escid);

    //         $this->view->name=$name;
    //         $this->view->ap=$ap;
    //         $this->view->am=$am;
    //         $this->view->code=$code;
    //         $this->view->escid=$escid; 
			        	
    //     }  
    //     catch (Exception $ex)
    //     {
    //         print "Error: Al listar Alumno".$ex->getMessage();
    //     }

    // }
}