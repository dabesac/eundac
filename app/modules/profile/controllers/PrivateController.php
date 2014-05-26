<?php

class Profile_PrivateController extends Zend_Controller_Action {

	public function init()
	{
		$sesion = Zend_Auth::getInstance();
		if(!$sesion->hasIdentity() ){
			$this->_helper->redirector('index', "index", 'default');
		}
		$login = $sesion->getStorage()->read();
		$this->sesion=$login;
	}

	public function indexAction()
	{
		
	}

	public function studentAction()
    {
        try{
        	$uid=base64_decode($this->getParam("uid"));
        	$pid=base64_decode($this->getParam("pid"));
        	$escid=base64_decode($this->getParam("escid"));
        	$eid=base64_decode($this->getParam("eid"));
            $oid=base64_decode($this->getParam("oid"));

        	//print_r($uid." ".$pid." ".$escid." ".$eid);

        	$dbperson=new Api_Model_DbTable_Person();
        	$where=array("eid"=>$eid, "pid"=>$pid);
        	$attrib=array("last_name0","last_name1","first_name","pid");
        	$person=$dbperson->_getFilter($where,$attrib);
            $person['uid']=$uid;
            $person['eid']=$eid;
            $person['oid']=$oid;
        	//print_r($person);
            
            $dbfacesp=new Api_Model_DbTable_Speciality();
            $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid);
            $facesp=$dbfacesp->_getFacspeciality($where);

            //print_r($facesp);

            $this->view->facesp=$facesp;
            $this->view->person=$person;

    	}catch(exception $e){
    		print "Error : ".$e->getMessage();
    	}
    }

    public function studentinfoAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->getParam("eid");
            $oid=$this->getParam("oid");
            $pid=$this->getParam("pid");
            $uid=$this->getParam("uid");
            $escid=$this->getParam("escid");
            $subid=$this->getParam("subid");

            $data=array("eid"=>$eid,"pid"=>$pid);

            $dbperson=new Api_Model_DbTable_Person();
            $where=array("eid"=>$eid, "pid"=>$pid);
            $datos[3]=$dbperson->_getOne($where);
           
            $dbdetingreso=new Api_Model_DbTable_Studentsignin();
            $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "pid"=>$pid, "uid"=>$uid);
            $datos[4]=$dbdetingreso->_getOne($where);
            //print_r($datos);

            $this->view->data=$data;
            $this->view->datos=$datos;
        }catch(exception $e){
            print "Error! ".$e->getMessage();
        }
    }

    public function studentfamilyAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->getParam("eid");
            $pid=$this->getParam("pid");


            $famdata=$famrel= array();
            $dbfam=new Api_Model_DbTable_Relationship();
            $where=array("eid"=>$eid,"pid"=>$pid);
            $famrel=$dbfam->_getFilter($where);
            $c=0;
            if ($famrel){
	            foreach ($famrel as $f) {
	                $where=array("eid"=>$eid, "famid"=>$f['famid']);
	                $famdata[$c]=$dbfam->_getInfoFamiliars($where);
	                $c++;
	            }
            }
            //print_r($famdata);
            $this->view->famrel=$famrel;
            $this->view->famdata=$famdata;

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentacademicAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->getParam("eid");
            $pid=$this->getParam("pid");

            $dbacadata=new Api_Model_DbTable_Academicrecord();
            $where=array("eid"=>$eid,"pid"=>$pid);
            $acadata=$dbacadata->_getFilter($where);
            //print_r($acadata);
            $this->view->acadata=$acadata;

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentstatisticAction()
    {
        try{
            $this->_helper->layout()->disableLayout();

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentlaboralAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->getParam("eid");
            $pid=$this->getParam("pid");


            $dblaboral=new Api_Model_DbTable_Jobs();
            $where=array("eid"=>$eid,"pid"=>$pid);
            $laboral=$dblaboral->_getFilter($where);
            // print_r($laboral);
            $this->view->laboral=$laboral;
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

     public function studentinterestAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->getParam("eid");
            $pid=$this->getParam("pid");

            $dbinteres=new Api_Model_DbTable_Interes();
            $where=array("eid"=>$eid,"pid"=>$pid);
            $interes=$dbinteres->_getFilter($where);
            //print_r($interes);
            $this->view->interes=$interes;

        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }


    public function studentsigncurrentAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->getParam("eid");
            $oid=$this->getParam("oid");
            $pid=$this->getParam("pid");
            $uid=$this->getParam("uid");
            $escid=$this->getParam("escid");
            $subid=$this->getParam("subid");
            $rid=$this->getParam("rid");
            $perid=$this->sesion->period->perid;

            $data=array("eid"=>$eid,"oid"=>$oid,"pid"=>$pid,"uid"=>$uid,"escid"=>$escid,"subid"=>$subid, "perid"=>$perid,"rid"=>$rid);

            $dbcuract=new Api_Model_DbTable_Registrationxcourse();
            $dbtyperate=new Api_Model_DbTable_PeriodsCourses();
            $where=array("eid"=>$eid, "oid"=>$oid, "pid"=>$pid, "uid"=>$uid, "perid"=>$perid);
            $curact=$dbcuract->_getFilter($where);
            $nc=0;
            $typerate= $name= array();
            if ($curact){
	            foreach ($curact as $cur) {
	                $where=array("eid"=>$eid, "oid"=>$oid, "perid"=>$perid, "courseid"=>$cur['courseid'], "turno"=>$cur['turno'], "curid"=>$cur['curid']);
	                $attrib=array("type_rate");
	                //print_r($where);
	                $typerate[$nc]=$dbtyperate->_getFilter($where,$attrib);
	                $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "courseid"=>$cur['courseid']);
	                $attrib=array("name");
	                $name[$nc]=$dbcuract->_getInfoCourse($where,$attrib);
	                $nc++;
	            }
            }
            //print_r($data);
            $this->view->typerate=$typerate;
            $this->view->name=$name;
            $this->view->curact=$curact;
            $this->view->data=$data;
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentsignrealizedAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }

    public function studentsignpercurAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            
            $pid=$this->getParam("pid");
            $uid=$this->getParam("uid");
            $escid=$this->getParam("escid");
            $subid=$this->getParam("subid");
         
            $eid=$this->getParam("eid");
            $oid=$this->getParam("oid");            
            $perid=$this->sesion->period->perid;
            

            $dbcur=new Api_Model_DbTable_Studentxcurricula();
            $dbcourxcur=new Api_Model_DbTable_Course();
            $dbcourlle=new Api_Model_DbTable_Registrationxcourse();


            $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "uid"=>$uid, "pid"=>$pid);
            //print_r($where);
            $cur=$dbcur->_getOne($where);
            //print_r($cur);
            $courpercur=$dbcourxcur->_getCoursesXCurriculaXShool($eid,$oid,$cur['curid'],$escid);
            $courlle=array();
            $c=0;
            if ($courpercur){
	            foreach ($courpercur as $cour) {
	                $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid, "courseid"=>$cour['courseid'], "curid"=>$cur['curid'],"pid"=>$pid,"uid"=>$uid);
	                $attrib=array("courseid","notafinal","perid", 'turno');
	                //print_r($where);
	                $courlle[$c]=$dbcourlle->_getFilter($where, $attrib);
	                $c++;
	            }
            }
            //print_r($courpercur);
            $where=array("eid"=>$eid, "oid"=>$oid, "escid"=>$escid, "subid"=>$subid,"pid"=>$pid,"uid"=>$uid,"perid"=>$perid);
            $attrib=array("courseid","state", 'turno', 'perid');
            $courlleact=$dbcourlle->_getFilter($where,$attrib);
            //print_r($courlle);

            $this->view->courpercur=$courpercur;
            $this->view->courlleact=$courlleact;
            $this->view->courlle=$courlle;
        }catch(exception $e){
            print "Error : ".$e->getMessage();
        }
    }
}