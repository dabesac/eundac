<?php

class Register_ListcurrentnotesController extends Zend_Controller_Action {

    public function init()
    {
		$sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
      	/*$this->sesion->eid="20154605046";
      	$this->oid="1";
      	$this->escid="4SI";
      	$this->subid="1901";
      	$this->pid="0924403185";
      	$this->uid="0924403185";*/
    }

    public function indexAction()
    {
    	try{
    		$eid=$this->sesion->eid;
    		$oid=$this->sesion->oid;
    		$escid=$this->sesion->escid;
    		$subid=$this->sesion->subid;
    		$pid=$this->sesion->pid;
    		$uid=$this->sesion->uid;
    		$perid="13A";

    		$dbcoursescp=new Api_Model_DbTable_Registrationxcourse();
            $dbtyperate=new Api_Model_DbTable_PeriodsCourses();
            

    		$where=array("eid"=>$eid,"oid"=>$oid,"escid"=>$escid,"subid"=>$subid,"pid"=>$pid,"uid"=>$uid,"perid"=>$perid);
    		//print_r($where);
            $coursescp=$dbcoursescp->_getFilter($where);
    		//print_r($coursescp);
    		
    		$nc=0;
    		$c=0;
    		$nm=0;
            $attrib=array("type_rate","courseid");
    		foreach ($coursescp as $coid) {
			    $where=array("eid"=>$eid,"oid"=>$oid,"escid"=>$escid,"subid"=>$subid,"curid"=>$coid['curid'],"courseid"=>$coid['courseid']);
    			//print_r($where);
    			$courses[$nc]=$dbcoursescp->_getInfoCourse($where);
                $where=array("eid"=>$eid,"oid"=>$oid,"escid"=>$escid,"subid"=>$subid,"curid"=>$coid['curid'],"courseid"=>$coid['courseid'],"perid"=>$perid, "turno"=>$coid['turno']);
                $typerate[$nc]=$dbtyperate->_getFilter($where,$attrib);
                //print_r($courses);
                $c=0;
                $nc++;
            }
          	$notes['nm']=$nm;
    		//print_r($typerate);
    		
            $this->view->typerate=$typerate;
    		$this->view->notes=$coursescp;
    		//print_r($notes);
    		$this->view->courses=$courses;

    	}catch(Exception $ex){
            print "Error: Cargar ".$ex->getMessage();

    	}
    }
}
