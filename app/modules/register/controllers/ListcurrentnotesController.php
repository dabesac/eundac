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
    }

    public function indexAction()
    {
    	try{
            $this->_helper->layout()->disableLayout();

    		$eid=$this->sesion->eid;
    		$oid=$this->sesion->oid;
    		$escid=$this->sesion->escid;
    		$subid=$this->sesion->subid;
    		$pid=$this->sesion->pid;
    		$uid=$this->sesion->uid;
    		$perid=$this->sesion->period->perid;
    		$dbcoursescp=new Api_Model_DbTable_Registrationxcourse();
            $dbtyperate=new Api_Model_DbTable_PeriodsCourses();
    		$where=array("eid"=>$eid, "state"=>"M",
    					"oid"=>$oid,"escid"=>$escid,"subid"=>$subid,
    					"pid"=>$pid,"uid"=>$uid,"perid"=>$perid);
            $coursescp=$dbcoursescp->_getFilter($where);
    		$nc=0;
    		$c=0;
    		$nm=0;
            $attrib=array("type_rate","courseid");
    		foreach ($coursescp as $coid) {
			    $where=array("eid"=>$eid,"oid"=>$oid,"escid"=>$escid,"subid"=>$subid,"curid"=>$coid['curid'],"courseid"=>$coid['courseid']);
    			$courses[$nc]=$dbcoursescp->_getInfoCourse($where);
                $where=array("eid"=>$eid,"oid"=>$oid,"escid"=>$escid,"subid"=>$subid,"curid"=>$coid['curid'],"courseid"=>$coid['courseid'],"perid"=>$perid, "turno"=>$coid['turno']);
                $typerate[$nc]=$dbtyperate->_getFilter($where,$attrib);
                $c=0;
                $nc++;
            }
          	$notes['nm']=$nm;
            $this->view->typerate=$typerate;
            $this->view->perid=$perid;
    		$this->view->notes=$coursescp;
    		$this->view->courses=$courses;
    	}catch(Exception $ex){
            print "Error: Cargar ".$ex->getMessage();

    	}
    }
}
