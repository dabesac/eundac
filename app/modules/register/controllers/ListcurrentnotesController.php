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
    		$perid = $this->sesion->period->perid;
    		$dbcoursescp=new Api_Model_DbTable_Registrationxcourse();
            $dbtyperate=new Api_Model_DbTable_PeriodsCourses();
    		$where=array("eid"=>$eid, "state"=>"M",
    					"oid"=>$oid,"escid"=>$escid,"subid"=>$subid,
    					"pid"=>$pid,"uid"=>$uid,"perid"=>$perid);
            $coursescp=$dbcoursescp->_getFilter($where);
    		$nc=0;
    		$c=0;
    		$nm=0;
            $coursesD = 0;
            $attrib=array("type_rate","courseid");
            $typerate=null;
            $courses=null;
            $notasAplazados=null;
            $coursesDis = null;
            if ($coursescp) {
				foreach ($coursescp as $coid) {
					$where=array("eid"=>$eid,"oid"=>$oid,"escid"=>$escid,"subid"=>$subid,"curid"=>$coid['curid'],"courseid"=>$coid['courseid']);
					$courses[$nc]=$dbcoursescp->_getInfoCourse($where);
					$where=array("eid"=>$eid,"oid"=>$oid,"escid"=>$escid,"subid"=>$subid,"curid"=>$coid['curid'],"courseid"=>$coid['courseid'],"perid"=>$perid, "turno"=>$coid['turno']);
					$typerate[$nc]=$dbtyperate->_getFilter($where,$attrib);
					$c=0;
					$nc++;

					if ($coid['notafinal'] and $coid['notafinal']<11) {
						$coursesDis[$coursesD]['courseid'] = $coid['courseid'];
						$coursesDis[$coursesD]['curid'] = $coid['curid'];
						$coursesD++;
					}
				}
			}
            if ($coursesD < 3) {
                $number = $rest = substr($perid, 0, 2);
                $letter = substr($perid, -1);
                if ($letter == 'A') {
                    $letter = 'D';
                    $perid = $number.$letter;
                }else{
                    $letter = 'E';
                    $perid = $number.$letter;
                }
                $c = 0;
                $attrib = array('notafinal', 'courseid');
                if ($coursesDis){
					foreach ($coursesDis as $courseDis) {
					   $where = array('eid'=>$eid,
									'oid'=>$oid,
									'escid'=>$escid,
									'subid'=>$subid,
									'courseid'=>$courseDis['courseid'],
									'curid'=>$courseDis['curid'],
									'perid'=>$perid);
					   $notasAplazados[$c]=$dbcoursescp->_getFilter($where, $attrib);
					   $c++;
					}
				}
            }

            //print_r($notasAplazados);
          	$notes['nm']=$nm;
            $this->view->typerate=$typerate;
            $this->view->perid=$perid;
    		$this->view->notes=$coursescp;
    		$this->view->courses=$courses;
            $this->view->notasAplazados = $notasAplazados;

    	}catch(Exception $ex){
            print "Error: Cargar ".$ex->getMessage();

    	}
    }
}
