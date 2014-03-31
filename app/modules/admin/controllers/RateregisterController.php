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
            $this->view->perid=$perid;
            $dbtasas=new Api_Model_DbTable_Rates();
            $data['eid']=$eid;
            $data['oid']=$oid;
            $data['perid']=$perid;
            $order=array('ratid');
            $datatasas=$dbtasas->_getFilter($data,null,$order);
            $this->view->tasas=$datatasas;
        }
    public function newAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;   
            $perid= $this->_getParam("perid"); 
            $state= $this->_getParam("state"); 
            $this->view->perid=$perid;
            $this->view->state=$state;
            $fm=new Admin_Form_Ratesnew ();
            $fm->guardar->setLabel("Guardar");

            if ($this->getRequest()->isPost())
             {
                $formData = $this->getRequest()->getPost();
                if ($fm->isValid($formData))
                    {     
                    unset($formData["guardar"]);
                    unset($formData['state']);
                    $formData['eid']=$eid;
                    $formData['oid']=$oid;
                    $nf=new Api_Model_DbTable_Rates();
                    
                    if ($nf->_save($formData)) {
                        $this->view->valor=1;                        
                    }
                    // $this->_helper->redirector("index");
                }
                else{
                    $fm->populate($formData);
               }
            }
            $this->view->fm=$fm;
        }
        catch (Exception $ex){
          print "Error listar Tasas Controller: ".$ex->getMessage();          
        }
    }

    public function deleteAction(){
        try{            
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $ratid=($this->_getParam("ratid"));
            $perid=($this->_getParam("perid"));
            $state=($this->_getParam("state"));
            
            $del=new Api_Model_DbTable_Rates();
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['ratid']=$ratid;
            $where['perid']=$perid;
            $where['state']=$state;
        
            if ($del->_delete($where)){
                $this->_helper->_redirector("index");
                // $params = array('perid'=>$perid,'state'=>$state);
                // $this->_helper->redirector('list','rateregister','admin', $params);
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

    public function updatetasaAction(){

    try{ 
            $this->_helper->layout()->disableLayout();                     
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $ratid=($this->_getParam("ratid"));
            $perid=($this->_getParam("perid"));
            $state=($this->_getParam("state"));

            $this->view->ratid=$ratid;
            $this->view->perid=$perid;
            $this->view->state=$state;
            
            $dbtasas=new Api_Model_DbTable_Rates();
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['ratid']=$ratid;
            $where['perid']=$perid;
            $tasas=$dbtasas ->_getOne($where);
            $form=new Admin_Form_Rates();
            $form->populate($tasas);                
        
            if ($this->getRequest()->isPost()){
                
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)){                            
                    unset($formData['guardar']);
                    unset($formData['state']);
                    trim($formData['resolucion']);
                    trim($formData['name']);
                    trim($formData['t_normal']);
                    $formData['f_ini_tn'];
                    $formData['f_fin_tnd'];
                    trim($formData['t_incremento1']);
                    trim($formData['v_t_incremento1']);
                    $formData['f_fin_ti1'];
                    trim($formData['t_incremento2']);
                    trim($formData['v_t_incremento2']);
                    $formData['f_fin_ti2'];
                    trim($formData['t_incremento3']);
                    trim($formData['v_t_incremento3']);
                    $formData['f_fin_ti3'];
                    $formData['eid']=$eid;
                    $formData['oid']=$oid;
                    $formData['ratid']=$ratid;
                    // $str=array();
                    // $str="eid='$eid' and oid='$oid' and ratid='$ratid'";
                    $pk=array('eid'=>$eid,'oid'=>$oid,'ratid'=>$ratid,'perid'=>$perid);                    
                    $dbper=new Api_Model_DbTable_Rates();
                    
                    if ($dbper->_update($formData,$pk)) {
                        $this->view->valor=1;
                    }
                }
                else{
                    $form->populate($formData);
                }    
            }
            $this->view->form = $form;        
    }
    catch (Exception $ex){
        print "Error listar TasasController: ".$ex->getMessage();
    }
}
}