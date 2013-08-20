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
    //  try
    //     {
    //         $this->_helper->layout()->disableLayout();
    //         $eid="20154605046";
    //      $oid="1";
    //      $subid = "1901";
    //      $facid="4";
    //      $state="A";
    //      $where['eid']=$eid;
    //      $where['oid']=$oid;
    //      $where['facid']=$facid;
    //      $where['state']=$state;
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

    public function getstudentAction()
    {
        try
        {
            $this->_helper->getHelper('layout')->disableLayout();
            $perid="13A";//$this->sesion->perid;
            $eid = "20154605046";//$this->sesion->eid;        
            $oid = "1";//$this->sesion->oid; 
            $name = trim(strtoupper($this->_getParam('name')));
            $ap = trim(strtoupper($this->_getParam('ap')));        
            $am = trim(strtoupper($this->_getParam('am')));        
            $code = $this->_getParam('uid');        
            $escid = $this->_getParam('escid');   
            $condi = $this->_getParam('cond');
            $this->view->name=$name;
            $this->view->ap=$ap;
            $this->view->am=$am;
            $this->view->code=$code;
            $this->view->escid=$escid; 
            $this->view->perid= $perid;
            $where['oid']=$oid;
            $where['eid']=$eid;
            $where['escid']=$oid;
            $where['perid']=$oid;
            $where['name']=$name;
            $where['ap']=$ap;
            $where['am']=$am;




            $bdu = new Api_Model_DbTable_Condition();        
            if ($condi=='C')
            {
                $str = " and (p.last_name0 like '%$ap%' and p.last_name1 like '%$am%' and ca.uid like '$code%' and upper(p.first_name) like '%$name%' )";            
                $datos= $bdu->_getUsercCondition($eid,$oid,$str,$escid,$perid);

            }

            if ($condi=='S')
            {
                // $str = " and (p.ape_pat like '%$ap%' and p.ape_mat like '%$am%' and u.uid like '$codigo%' and upper(p.nombres) like '%$nombre%' ) and uid not in (select uid from condicion_alumno
                // where perid='$perid' AND eid='$eid' 
                // and oid='$oid' and escid='$escid')";
                $datos= $bdu->_getUsersCondition($where);
            }

            $this->view->bdu=$bdu;
            $this->view->condicion=$condi;          
            $this->view->datos=$datos;
                        
        }  
        catch (Exception $ex)
        {
            print "Error: Al listar Alumno".$ex->getMessage();
        }

    }
}