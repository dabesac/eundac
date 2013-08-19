<?php

class RecordController extends Zend_Controller_Action {

    public function init(){
        $this->eid= "20154605046";
       	$this->oid= "1";
      	$this->perid="13A";
      	$this->rid="RC";        
    }
		
    public function indexAction()
    {
    	try{
    		$where['eid']=$this->eid;
    		$where['oid']=$this->oid;
    		$perid=$this->perid;
    		$this->view->perid=$this->perid;
    		$spec = new Api_Model_DbTable_Speciality();
			if ($this->rid=="RC" || $this->rid=="IN"){
				$listspec = $spec->_getAll($where);
			}/*
			if ($this->sesion->rid=="RF"){
				$lesc = $escuelas->_getTodasEscuelasXFacultad($eid, $oid, $facid,$sedid);	
			}
			if ($this->sesion->rid=="DC"){
				$lesc = $escuelas->_getEscuelaXFacultadXSede($eid, $oid, $escid);	
    	        $this->view->rid=$rid;
			}*/
      		if ($listspec ) $this->view->listspec=$listspec;	
    	}catch(exception $ex){
    		print "Error en mostrar las escuelas";
    	}
    }

    public function listperiodAction()
    {
    	try{
    		$this->_helper->layout()->disableLayout();
    		$eid=$this->eid;
    		$oid=$this->oid;
	        $where['eid']=$this->eid;
    		$where['oid']=$this->oid;
	        $anio = $this->_getParam("anio");
	        if ($eid=="" || $oid==""||$anio=="") return false;
	        $anior = substr($anio, 2, 4);
    		$where['year']=$anior;
	        $periods = new Api_Model_DbTable_Periods();
	        $per=$periods->_getPeriodsxYears($where);
    		//print_r($per);
	        $this->view->listper=$per;
			//$this->view->perid=$this->perid;
    	}catch(exception $ex){
    		print "Error en listar Periodo";	
    	}
    }

    public function getcoursesAction()
    {   
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->eid;
            $oid=$this->oid;
            $escid = $this->_getParam("escid");
            $perid = $this->_getParam("perid");
            //print_r($perid);
            $curso = new Api_Model_DbTable_PeriodsCourses();
            $where= array("eid"=>$eid,"oid"=>$oid,"escid"=>$escid,"perid"=>$perid);
            $atrib = array('courseid','turno','semid','closure_date','curid','type_rate','state');
            $order = array('courseid  ASC','turno asc','semid asc');
            $data= $curso->_getFilter($where,$atrib,$order);
            //print_r($data);
            foreach ($data as $key => $curs) {
                $where = array('eid'=>$eid,"oid"=>$oid,'courseid'=>$curs['courseid']);
                $attrib=array('name','courseid');
                $pack_couse=$curso ->_getinfoCourse($where,$attrib);
                $info_curso[$key]=$pack_couse[0];
                $info_curso[$key]['turno']=$curs['turno'];
                $info_curso[$key]['semid']=$curs['semid'];
                $info_curso[$key]['curid']=$curs['curid'];
                $info_curso[$key]['type_rate']=$curs['type_rate'];
                $info_curso[$key]['closure_date']=$curs['closure_date'];
                $info_curso[$key]['state']=$curs['state'];
            }
            $this->view->courses=$info_curso;
        }catch(exception $ex){
            print "Error en listar Periodo";    
        }
    }


}