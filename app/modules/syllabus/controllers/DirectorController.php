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
            $eid=$this->eid;
            $oid=$this->oid;
            $escid=$this->escid;
            $year=$this->_getParam("year");

            $dbcourses=new Api_Model_DbTable_PeriodsCourses();

            if($year){
                $dbperiods=new Api_Model_DbTable_Periods();
                $where=array("eid"=>$eid,"oid"=>$oid,"year"=>substr($year,2,4));
                $periods=$dbperiods->_getPeriodsxYears($where);
                $cc=0;
                $cp=0;
                foreach ($periods as $period) {
                    print_r($period['curid']);
                    $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "perid"=>$period['perid']);
                    $attrib=array("courseid","perid","semid","turno");
                    $order=array("semid");
                    $coursesperyear[$cc]=$dbcourses->_getFilter($where,$attrib,$order);
                    foreach ($coursesperyear[$cc] as $cxy) {
                        $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "courseid"=>$cxy['courseid']);
                        $attrib=array("name","courseid");
                        //print_r($where);
                        $coursesname[$cp]=$dbcourses->_getinfoCourse($where,$attrib);
                        $cp++;
                    }
                    //print_r($where);
                    $cc++;
                }
                //print_r($coursesperyear);
                //print_r($coursesname);
                $this->view->coursesname=$coursesname;
                $this->view->coursesperyear=$coursesperyear;
            }
        
        }catch(exception $e){
            print "Error :".$e->getMessage();
        }
    }
}
