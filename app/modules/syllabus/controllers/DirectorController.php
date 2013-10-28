<?php

class Syllabus_DirectorController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	$this->sesion = $login;

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
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
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
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $escid=$this->sesion->escid;
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
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $year=$this->_getParam("year");
            $perid=$this->_getParam("perid");
            $semid=$this->_getParam("semid");
            $dbcourses=new Api_Model_DbTable_PeriodsCourses();
            $dbsyllabus=new Api_Model_DbTable_Syllabus();
            $dbismain=new Api_Model_DbTable_Coursexteacher();
            $dbteachers=new Api_Model_DbTable_Person();

            if($year<>"" && $perid=="" && $semid==""){
                $dbperiods=new Api_Model_DbTable_Periods();
                $where=array("eid"=>$eid,"oid"=>$oid,"year"=>substr($year,2,4));
                $periods=$dbperiods->_getPeriodsxYears($where);
                $cc=0;
                $cp=0;
                $cont=0;
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
                        $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "courseid"=>$cxy['courseid'],"perid"=>$period['perid'],"is_main"=>"S");
                        $attrib=array("courseid","pid","is_main");
                        $ismain=$dbismain->_getFilter($where,$attrib);
                        if($ismain[0]['is_main']=="S"){
                            //print_r($ismain);
                            $where=array("eid"=>$eid,"pid"=>$ismain[0]['pid']);
                            $attrib=array("last_name0","last_name1","first_name");
                            $teachers[$cp]=$dbteachers->_getFilter($where, $attrib);
                            $cont++;
                            //print_r($teachers[$cp]);
                            
                        }
                        $cp++;
                    }
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
                        $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "courseid"=>$cxy['courseid'],"perid"=>$perid,"is_main"=>"S");
                        $attrib=array("pid","is_main");
                        $ismain=$dbismain->_getFilter($where,$attrib);
                        if($ismain[0]['is_main']=="S"){
                            //print_r($ismain);
                            $where=array("eid"=>$eid,"pid"=>$ismain[0]['pid']);
                            $attrib=array("last_name0","last_name1","first_name");
                            $teachers[$cp]=$dbteachers->_getFilter($where, $attrib);
                            //print_r($teachers[$cp]);
                            
                        }
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
                        $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "courseid"=>$cxy['courseid'],"perid"=>$perid,"is_main"=>"S");
                        $attrib=array("pid","is_main");
                        $ismain=$dbismain->_getFilter($where,$attrib);
                        if($ismain[0]['is_main']=="S"){
                            $where=array("eid"=>$eid,"pid"=>$ismain[0]['pid']);
                            $attrib=array("last_name0","last_name1","first_name");
                            $teachers[$cp]=$dbteachers->_getFilter($where, $attrib);
                            //print_r($teachers[$cp]);
                            
                        }
                        $cp++;
                    }
                $filcourses=$coursespersemester;
            }

                //print_r($coursesstate);
                //print_r($coursesname);
                $this->view->teachers=$teachers;
                $this->view->allcourses=$allcourses;
                $this->view->filcourses=$filcourses;
                $this->view->coursesname=$coursesname;
                $this->view->coursesstate=$coursesstate;
        }catch(exception $e){
            print "Error :".$e->getMessage();
        }
    }
}
