<?php

class Admin_RateregisterController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		//$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (!$login->rol['module']=="admin"){
    		//$this->_helper->redirector('index','index','default');
    	}
    	$this->sesion = $login;
    
    }
    
    public function indexAction()
    {
   	    $eid= $this->sesion->eid;
        $oid= $this->sesion->oid;
    }

    public function lperiodsAction()
        {
            $this->_helper->layout()->disableLayout();
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $anio = $this->_getParam("anio");
            
            if ($eid=="" || $oid=="" || $anio=="") return false;
            $p = substr($anio, 2, 3);
            
            
            $this->view->anio = $anio;
            $periodos = new Api_Model_DbTable_Periods();
            $data['eid']=$eid;
            $data['oid']=$oid;
            $data['year']=$p;

            // = $periodos->_getPeriodoXAnio($eid, $oid, $p1, $p2);
            $rr = $periodos->_getPeriodsxYears($data);
            IF ($rr) $this->view->lper= $rr;
               
        }
    public function listAction()
        {
          $this->_helper->layout()->disableLayout();                     
          $eid= $this->sesion->eid;                    
          $oid= $this->sesion->oid;  
          $perid = $this->_getParam("perid");
          $state = $this->_getParam("state");
                            
           $this->view->state=$state;
           $dbtasas=new Api_Model_DbTable_Rates();
           $data['eid']=$eid;
           $data['oid']=$oid;
           $data['perid']=$perid;
           $datatasas=$dbtasas->_getFilter($data);
           $this->view->tasas=$datatasas;
        }
    public function newAction()
        {
          try
        {
                $eid= $this->sesion->eid;
                $oid= $this->sesion->oid;   
                $perid= $this->_getParam("perid"); 
                $this->view->perid=$perid;
                $fm=new Admin_Form_Ratesnew ();
                $fm->guardar->setLabel("Guardar");
                if ($this->getRequest()->isPost())
                 {
                   $formData = $this->getRequest()->getPost();
                       if ($fm->isValid($formData))
                          {     
                                 unset($formData["guardar"]);
                                 $formData['eid']=$eid;
                                 $formData['oid']=$oid;
                                 $nf=new Api_Model_DbTable_Rates();
                                 $nf->_save($formData); 
                                 $this->_helper->redirector("index");
                          }
                       else
                          {
                                 $fm->populate($formData);
                           }
                 }
                         $this->view->fm=$fm;
        }
         catch (Exception $ex) 
        {
          print "Error listar TasasController: ".$ex->getMessage();
          
        }
        }
    public function deleteAction()
        {
           try 
        {            
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $ratid=($this->_getParam("ratid"));
            $perid=($this->_getParam("perid"));
            $del=new Api_Model_DbTable_Rates();
                $where['eid']=$eid;
                $where['oid']=$oid;
                $where['ratid']=$ratid;
                $where['perid']=$perid;

            if ($del->_delete($where))
            {
                $this->_helper->_redirector("index");
            }
            else
            {
                echo "error al eliminar";
            }
        }
        catch (Exception $ex)
        {
            print "Error al Eliminar la tasa".$ex->getMessage();
        }
    
        }
    public function updateAction()
    {
      try
      { 
              //  $this->_helper->layout()->disableLayout();                     
                $eid=$this->sesion->eid;
                $oid=$this->sesion->oid;
                $ratid=($this->_getParam("ratid"));
                $perid=($this->_getParam("perid"));
                $dbtasas=new Api_Model_DbTable_Rates();
                $where['eid']=$eid;
                $where['oid']=$oid;
                $where['ratid']=$ratid;
                $where['perid']=$perid;
                $tasas=$dbtasas ->_getOne($where);
                //print_r($tasas);
                $form=new Admin_Form_Rates();
                  if ($this->getRequest()->isPost())
                   {
                        $frmdata=$this->getRequest()->getPost();
                        unset($frmdata['guardar']);
                        trim($frmdata['resolucion']);
                        trim($frmdata['name']);
                        trim($frmdata['t_normal']);
                        $frmdata['f_ini_tn'];
                        $frmdata['f_fin_tnd'];
                        trim($frmdata['t_incremento1']);
                        trim($frmdata['v_t_incremento1']);
                        $frmdata['f_fin_ti1'];
                        trim($frmdata['t_incremento2']);
                        trim($frmdata['v_t_incremento2']);
                        $frmdata['f_fin_ti2'];
                        trim($frmdata['t_incremento3']);
                        trim($frmdata['v_t_incremento3']);
                        $frmdata['f_fin_ti3'];
                        $frmdata['eid']=$eid;
                        $frmdata['oid']=$oid;
                        $frmdata['ratid']=$ratid;
                        $str=array();
                        $str="eid='$eid' and oid='$oid' and ratid='$ratid'";
                        $dbper=new Api_Model_DbTable_Rates();
                        $per=$dbper->_update($frmdata,$str);
                        $this->_helper->redirector("index");
                    }
               $form->populate($tasas);
               $this->view->form = $form;
      }
       catch (Exception $ex)
       {
        print "Error listar TasasController: ".$ex->getMessage();
       }
     }

}