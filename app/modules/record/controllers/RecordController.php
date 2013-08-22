<?php

class Record_RecordController extends Zend_Controller_Action {

    public function init(){
        $this->eid= "20154605046";
       	$this->oid= "1";
      	$this->perid="13A";
      	$this->rid="RC";        
        $this->subid="1901";
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
            $atrib = array('courseid','turno','semid','closure_date','curid','type_rate','state','subid');
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
                $info_curso[$key]['subid']=$curs['subid'];                
            }
            $this->view->courses=$info_curso;
            $this->view->perid=$perid;
            $this->view->escid=$escid;
        }catch(exception $ex){
            print "Error en listar Periodo".$ex;    
        }
    }

    public function courseteacherAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->eid;
            $oid=$this->oid;
            $subid=$this->subid;
            $perid=base64_decode($this->_getParam("perid"));
            $curid=base64_decode($this->_getParam("curid"));
            $courseid=base64_decode($this->_getParam("courseid"));
            $turno=base64_decode($this->_getParam("turno"));
            $escid=base64_decode($this->_getParam("escid"));

            $dbesp = new Api_Model_DbTable_Speciality();
            $where = array('eid'=>$eid,"oid"=>$oid,'escid'=>$escid,'subid'=>$subid);
            $esp = $dbesp->_getOne($where);

            $dbfac = new Api_Model_DbTable_Faculty();
            $where = array('eid'=>$eid,"oid"=>$oid,'facid'=>$esp['facid']);
            $fac = $dbfac->_getOne($where);
                      
            $this->view->esp=$esp['name'];
            $this->view->fac=$fac['name'];

            $dbinfocrs=new Api_Model_DbTable_PeriodsCourses();
            $where = array('eid'=>$eid,"oid"=>$oid,'courseid'=>$courseid);
            $attrib=array('name','courseid');
            $infocrs1=$dbinfocrs->_getinfoCourse($where,$attrib);

            $where = array("eid"=>$eid,"oid"=>$oid,"escid"=>$escid,"perid"=>$perid,"courseid"=>$courseid);
            $attrib=array('state','closure_date','state','type_rate');
            $infocrs2=$dbinfocrs->_getFilter($where,$attrib);
            
            $infocrs2['perid']=$perid;
            $infocrs2['curid']=$curid;
            $infocrs2['turno']=$turno;
            //print_r($infocrs);
            $this->view->infocrs1=$infocrs1;
            $this->view->infocrs2=$infocrs2;


            $dbinfotchr = new Api_Model_DbTable_Coursexteacher();
            $where = array("eid"=>$eid,"oid"=>$oid,"courseid"=>$courseid,"curid"=>$curid,"escid"=>$escid,"turno"=>$turno);
            $attrib=array('uid','pid','state','is_main');
            $infotchr=$dbinfotchr->_getFilter($where,$attrib);
            //print_r($infotchr);
            $this->view->infotchr=$infotchr;

            $c=0;
            foreach ($infotchr as $infot) {
                $where = array("eid"=>$eid,"oid"=>$oid,"escid"=>$escid,"subid"=>$subid,"pid"=>$infotchr[$c]['pid'],"uid"=>$infotchr[$c]['uid']);
                $infotchr2[$c]=$dbinfotchr->_getinfoTeacher($where,$attrib);
                $c++;
            }
            print_r($infotchr2);
            $this->view->infotchr2=$infotchr2;

        }catch(exception $ex){
            print "Error en listar Cursos".$ex; 
        }       
    }


}