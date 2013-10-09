<?php
class Report_RegistrationController extends Zend_Controller_Action 
{
    public function init(){
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
    }

    public function indexAction()
    {
        try
        {       
            $form = new Report_Form_Filter();   
            $this->view->fm=$form;   
            $rid=$this->sesion->rid; 
            $this->view->rid=$rid;
	    }
        catch(Exception $ex )
        {
		          print ("Error Controlador Mostrar Datos: ".$ex->getMessage());
	    } 
    }

    public function periodsAction()
    {
        try
        {     
            $this->_helper->layout()->disableLayout();  
            $where['eid']=$this->sesion->eid;        
            $where['oid']=$this->sesion->oid; 
            $where['year'] = $this->_getParam("anio");
            $bdperiods = new Api_Model_DbTable_Periods();
            $periods = $bdperiods->_getPeriodsxYears($where);   
            $this->view->periods = $periods; 
        }
        catch(Exception $ex )
        {
                  print ("Error:".$ex->getMessage());
        } 
    }

    public function listreportAction()
    {
        try
        {       
            $this->_helper->layout()->disableLayout();
            $where['eid']=$this->sesion->eid;        
            $where['oid']=$this->sesion->oid; 
            $rid=$this->sesion->rid;    
            $facid=$this->sesion->faculty->facid;
            $where['perid'] = $this->_getParam("perid");
            $busqx = $this->_getParam("x");
            $mat = $this->_getParam("mat");
            $premat = $this->_getParam("premat");
            $npremat = $this->_getParam("npremat");
            $obs = $this->_getParam("obs");
            $res = $this->_getParam("res");

            $this->view->perid=$where['perid'];
            $this->view->busqx=$busqx;
            $this->view->mat=$mat;
            $this->view->premat=$premat;
            $this->view->npremat=$npremat;
            $this->view->obs=$obs;
            $this->view->res=$res;
            $this->view->eid=$where['eid'];
            $this->view->oid=$where['oid'];
            $this->view->rid=$rid;
            $this->view->facid=$facid;

            $bdperiodo = new Api_Model_DbTable_Registration();
            if ($busqx == 'xesc')
            {
                $reporte = $bdperiodo->_getSpecialityXPeriodsXMat($where);
               
            }

            if ($busqx == 'xfac')
            {
                $reporte = $bdperiodo->_getFacultyXPeriodsXMat($where);   
            }

            if ($busqx == 'xsed')
            {
                $reporte = $bdperiodo->_getSubsidiaryXPeriodsXMat($where);
            }
            $this->view->reporte = $reporte;
        }
        catch(Exception $ex )
        {
                  print ("Error:".$ex->getMessage());
        } 
    }

    public function printAction()
    {
        try
        {       
            $this->_helper->layout()->disableLayout();
            $where['eid']=$this->sesion->eid;        
            $where['oid']=$this->sesion->oid; 
            $rid=$this->sesion->rid;    
            $facid=$this->sesion->faculty->facid;
            $where['perid'] = $this->_getParam("perid");
            $busqx = $this->_getParam("x");
            $mat = $this->_getParam("mat");
            $premat = $this->_getParam("premat");
            $npremat = $this->_getParam("npremat");
            $obs = $this->_getParam("obs");
            $res = $this->_getParam("res");

            $this->view->perid=$where['perid'];
            $this->view->busqx=$busqx;
            $this->view->mat=$mat;
            $this->view->premat=$premat;
            $this->view->npremat=$npremat;
            $this->view->obs=$obs;
            $this->view->res=$res;
            $this->view->eid=$where['eid'];
            $this->view->oid=$where['oid'];
            $this->view->rid=$rid;
            $this->view->facid=$facid;

            $bdperiodo = new Api_Model_DbTable_Registration();
            if ($busqx == 'xesc')
            {
                $reporte = $bdperiodo->_getSpecialityXPeriodsXMat($where);
               
            }

            if ($busqx == 'xfac')
            {
                $reporte = $bdperiodo->_getFacultyXPeriodsXMat($where);   
            }

            if ($busqx == 'xsed')
            {
                $reporte = $bdperiodo->_getSubsidiaryXPeriodsXMat($where);
            }
            $this->view->reporte = $reporte;
        }
        catch(Exception $ex )
        {
                  print ("Error:".$ex->getMessage());
        } 
    }
}