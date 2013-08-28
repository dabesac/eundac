<?php

class Syllabus_DirectorController extends Zend_Controller_Action {

    public function init()
    {
    	// $sesion  = Zend_Auth::getInstance();
    	// if(!$sesion->hasIdentity() ){
    	// 	$this->_helper->redirector('index',"index",'default');
    	// }
    	// $login = $sesion->getStorage()->read();
    	// if (!$login->modulo=="bienestar"){
    	// 	$this->_helper->redirector('index','index','default');
    	// }
    	//$this->sesion = $login;
        $this->eid="20154605046";
        $this->oid="1";
        $this->escid="4SI";
        $this->subid="1901";

    }
    public function indexAction()
    {
    
    	

    }

    public function listsyllabusAction()
    {
        try{
            
        }catch (exception $e){
            print "Error :".$e->getMessage();
        }
    }

    public function listperiodsAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->eid;
            $oid=$this->oid;
            $year=$this->_getParam("year");

            //print_r($where);
            $dbperiods=new Api_Model_DbTable_Periods();
            $where=array("eid"=>$eid,"oid"=>$oid,"year"=>substr($year,2,4));
            $periods=$dbperiods->_getPeriodsxYears($where);
            //print_r($periods);
            $this->view->periods=$periods;
        }catch (exception $e){
            print "Error :".$e->getMessage();
        }
    }

    public function listsemesterAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->eid;
            $oid=$this->oid;
            $escid=$this->escid;
            $perid=$this->_getParam("perid");

            $dbsemesters=new Api_Model_DbTable_Semester();
            $where=array("eid"=>$eid,"oid"=>$oid,"escid"=>$escid,"perid"=>$perid);
            $semesters=$dbsemesters->_getSemesterXPeriodsXEscid($where);
            //print_r($semesters);
            $this->view->semesters=$semesters;

        }catch (exception $e){
            print "Error :".$e->getMessage();
        }
    }

    public function listcoursesAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->eid;
            $oid=$this->oid;
            $escid=$this->escid;
            $subid=$this->subid;
            $year=$this->_getParam("year");
            $perid=$this->_getParam("perid");
            $semid=$this->_getParam("semid");
            $dbcourses=new Api_Model_DbTable_PeriodsCourses();
            $dbsyllabus=new Api_Model_DbTable_Syllabus();

            if($year<>"" && $perid=="" && $semid==""){
                $dbperiods=new Api_Model_DbTable_Periods();
                $where=array("eid"=>$eid,"oid"=>$oid,"year"=>substr($year,2,4));
                $periods=$dbperiods->_getPeriodsxYears($where);
                $cc=0;
                $cp=0;
                foreach ($periods as $period) {
                    $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "perid"=>$period['perid']);
                    $attrib=array("courseid","perid","semid","turno");
                    $order=array("semid");
                    $coursesperyear[$cc]=$dbcourses->_getFilter($where,$attrib,$order);
                    foreach ($coursesperyear[$cc] as $cxy) {
                        $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "courseid"=>$cxy['courseid']);
                        $attrib=array("name","courseid");
                        //print_r($where);
                        $coursesname[$cp]=$dbcourses->_getinfoCourse($where,$attrib);
                        $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "courseid"=>$cxy['courseid'],"turno"=>$cxy['turno'],"subid"=>$subid,"perid"=>$period['perid']);
                        $coursesstate[$cp]=$dbsyllabus->_getAll($where);
                        $cp++;
                    }
                    //print_r($where);
                    $cc++;
                }
                $allcourses=$coursesperyear;
            }elseif($year<>"" && $perid<>"" && $semid==""){
                $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "perid"=>$perid);
                $attrib=array("courseid","perid","semid","turno");
                $order=array("semid");
                $coursesperperiod=$dbcourses->_getFilter($where,$attrib,$order);
                $cp=0;
                foreach ($coursesperperiod as $cxy) {
                        $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "courseid"=>$cxy['courseid']);
                        $attrib=array("name","courseid");
                        //print_r($where);
                        $coursesname[$cp]=$dbcourses->_getinfoCourse($where,$attrib);
                        $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "courseid"=>$cxy['courseid'],"turno"=>$cxy['turno'],"subid"=>$subid,"perid"=>$perid);
                        $coursesstate[$cp]=$dbsyllabus->_getAll($where);
                        $cp++;
                    }
                $filcourses=$coursesperperiod;
            }elseif($year<>"" && $perid<>"" && $semid<>""){
                $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "perid"=>$perid,"semid"=>$semid);
                $attrib=array("courseid","perid","semid","turno");
                $order=array("semid");
                $coursespersemester=$dbcourses->_getFilter($where,$attrib,$order);
                $cp=0;
                foreach ($coursespersemester as $cxy) {
                        $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "courseid"=>$cxy['courseid']);
                        $attrib=array("name","courseid");
                        //print_r($where);
                        $coursesname[$cp]=$dbcourses->_getinfoCourse($where,$attrib);
                        $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "courseid"=>$cxy['courseid'],"turno"=>$cxy['turno'],"subid"=>$subid,"perid"=>$perid);
                        $coursesstate[$cp]=$dbsyllabus->_getAll($where);
                        $cp++;
                    }
                $filcourses=$coursespersemester;
            }

                //print_r($coursesstate);
                //print_r($coursesname);
                $this->view->allcourses=$allcourses;
                $this->view->filcourses=$filcourses;
                $this->view->coursesname=$coursesname;
                $this->view->coursesstate=$coursesstate;
        
        }catch(exception $e){
            print "Error :".$e->getMessage();
        }
    }
}
