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
            $header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
            $eid=$this->sesion->eid;        
            $oid=$this->sesion->oid; 
            $rid=$this->sesion->rid;    
            $facid=$this->sesion->faculty->facid;
            $perid = $this->_getParam("perid");
            $where = array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid);
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

            if ($facid=="TODO") {
                $header = str_replace("FACULTAD DE "," ", $header); 
            }
            
            $fac = array('eid'=>$eid,'oid'=>$oid,'facid'=>$facid);
            $base_fac =  new Api_Model_DbTable_Faculty();        
            $datafa= $base_fac->_getOne($fac);
            $namef = strtoupper($datafa['name']);

            $dbimpression = new Api_Model_DbTable_Countimpressionall();
            
            $uid=$this->sesion->uid;
            $uidim=$this->sesion->pid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $pid=$uidim;
            if ($busqx == 'xesc'){
                $code = 'reporte_matriculados_escuelas_'.$perid;               
            }

            if ($busqx == 'xfac'){
                $code = 'reporte_matriculados_facultades_'.$perid;
            }

            if ($busqx == 'xsed'){
                $code = 'reporte_matriculados_sedes_'.$perid;
            }

            $data = array(
                'eid'=>$eid,
                'oid'=>$oid,
                'uid'=>$uid,
                'escid'=>$escid,
                'subid'=>$subid,
                'pid'=>$pid,
                'type_impression'=>$code,
                'date_impression'=>date('Y-m-d H:i:s'),
                'pid_print'=>$uidim
                );
            // print_r($data);exit();
            $dbimpression->_save($data);            

            $wheri = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,
                'subid'=>$subid,'type_impression'=>$code);
            $dataim = $dbimpression->_getFilter($wheri);
            
            $co=count($dataim);            
            $codigo=$co." - ".$uidim;


            $header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", "blanco", $header);
            $header = str_replace("?codigo", $codigo, $header);
            $header = str_replace("h3", "h2", $header);
            $header = str_replace("11%", "9%", $header);
            $header = str_replace("ESCUELA DE FORMACIÓN PROFESIONAL DE"," ", $header);
            
            $this->view->header=$header;
            $this->view->footer=$footer;
        }
        catch(Exception $ex )
        {
                  print ("Error:".$ex->getMessage());
        } 
    }
}