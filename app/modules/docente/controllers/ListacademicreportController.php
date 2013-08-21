<?php

class Docente_ListacademicreportController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (!$login->rol['module']=="docente"){
    		$this->_helper->redirector('index','index','default');
    	}
    	$this->sesion = $login;
    
    }
    
    public function indexAction()
    {
   	    print_r($this->sesion);
    }

     public function listperiodsAction()
    {
    	try{
    		$this->_helper->layout()->disableLayout();
    		$eid=$this->sesion->eid;
    		$oid=$this->sesion->oid;
    		$where['eid']=$eid;
    		$where['oid']=$oid;
	        $anio = $this->_getParam("anio");
	        if ($eid=="" || $oid==""||$anio=="") return false;
    		$anior = substr($anio, 2, 4);
    		$where['year']=$anior;
	        $periods = new Api_Model_DbTable_Periods();
	        $per=$periods->_getPeriodsxYears($where);
    		//print_r($per);
	        $this->view->listper=$per;
		}catch(exception $ex){
    		print "Error en listar Periodo";	
    	}
    }

    public function listteachersAction()
    {
    	try{
    		//$this->_helper->layout()->disableLayout();
    		$eid=$this->sesion->eid;
    		$oid=$this->sesion->oid;
    		$where['eid']=$eid;
    		$where['oid']=$oid;
    		$perid=$this->_getParam("perid");
    		$where['perid']=$perid;

    		$dbteachers= new Api_Model_DbTable_Coursexteacher();
    		$where=array("eid"=>$eid,"oid"=>$oid,"perid"=>$perid,"is_main"=>"S");
    		$attrib=array('pid','uid','escid','subid');
    		$order=array('uid');
    		$teachers=$dbteachers->_getFilter($where,$attrib,$order);
    		//print_r($teachers);
    	    $escid=$teachers[0]['escid'];
    	    $pid=$teachers[0]['pid'];
    	    $subid=$teachers[0]['subid'];
    	    

    		$uidtchr[0]=$teachers[0]['uid'];
    		$c=0;
    		$c2=0;
    		foreach ($teachers as $tec) {
    			if($uidtchr[$c]<>$teachers[$c2]['uid']){
    				$uidtchr[$c+1]=$teachers[$c2]['uid'];
    				$pidtchr[$c+1]=$teachers[$c2]['pid'];
    				$where = array("eid"=>$eid,"oid"=>$oid,"escid"=>$escid,"subid"=>$subid,"pid"=>$pidtchr[$c+1],"uid"=>$uidtchr[$c+1]);
 					$packdatat[$c]=$dbteachers->_getinfoTeacher($where,$attrib);
 					$c++;
    			}
    			$c2++;
    		}  
    		//print_r($packdatat);  
    		$this->view->packdatat=$packdatat;		
    		
    	}catch(exception $ex){
    		print "Error en listar Cursos";		
    	}
    }
}