<?php

class Distribution_DistributionController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	// if (($login->rol['module']<>"docente") && ($login->infouser['teacher']['is_director']=="S")){
    	// 	$this->_helper->redirector('index','index','default');
    	// }
    	$this->sesion = $login;
    }
    
    public function indexAction()
    {
   		$distribution = new Distribution_Model_DbTable_Distribution();
   		$data['eid']=$this->sesion->eid;
   		$data['oid']=$this->sesion->oid;
   		$data['escid']=$this->sesion->escid;
   		$data['subid']=$this->sesion->subid;
   		$campos = array("eid","oid","escid","subid","perid","dateaccept","number","state","distid");
   		$rows_distribution =$distribution->_getFilter($data,$campos);
   		if ($rows_distribution) $this->view->ldistribution=$rows_distribution ;
    }
    
    public function newAction()
    {    	
    	$form = new Distribution_Form_Distribution();
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		if ($form->isValid($formData)) {
    			unset($formData['save']);
    			$formData['eid'] = base64_decode($formData['eid']);
    			$formData['oid'] = base64_decode($formData['oid']);
    			$formData['escid'] = base64_decode($formData['escid']);
    			$formData['subid'] = base64_decode($formData['subid']);
    			$formData['perid'] = base64_decode($formData['perid']);
    			$formData['register'] = $this->sesion->uid;
    			$distr = new Distribution_Model_DbTable_Distribution();
    			$formData['distid'] = time();
    			$distr->_save($formData);
    			$this->_helper->redirector('index','distribution','distribution');
    		}else{
    			$form->populate($formData);
    		}
    	}else{
    		$form->number->setValue($this->sesion->period->perid."-".time());
    	}
    	$this->view->form = $form;
    }
    
    public function editAction()
    {
    	$distid = base64_decode($this->_getParam("distid"));
    	$perid = base64_decode($this->_getParam("perid"));
    	$form = new Distribution_Form_Distribution();
    	$form->setAction("/distribution/distribution/edit/");
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		if ($form->isValid($formData)) {
    			unset($formData['save']);
    			$formData['eid'] = base64_decode($formData['eid']);
    			$formData['oid'] = base64_decode($formData['oid']);
    			$formData['escid'] = base64_decode($formData['escid']);
    			$formData['subid'] = base64_decode($formData['subid']);
    			$formData['perid'] = base64_decode($formData['perid']);
    			$formData['distid'] = trim($formData['distid']);
    			$pk =$formData;
    			$formData['modified'] = $this->sesion->uid;
    			$formData['updated'] = date('Y-m-d H:i:s');
    			$distr = new Distribution_Model_DbTable_Distribution();
    			$distr->_update($formData,$pk);
    			$this->_helper->redirector('index','distribution','distribution');
    		}else{
    			$form->populate($formData);
    		}
    	}else{
	    	 
	    	$data['eid']=$this->sesion->eid;
	    	$data['oid']=$this->sesion->oid;
	    	$data['escid']=$this->sesion->escid;
	    	$data['subid']=$this->sesion->subid;
	    	$data['distid']=$distid;
	    	$data['perid']=$perid;
	    	$distr_ = new Distribution_Model_DbTable_Distribution();
	    	$r = $distr_->_getOne($data);
	    	if ($r) {
	    		$r['perid'] = base64_encode($r['perid']);
	    		$r['eid'] = base64_encode($r['eid']);
	    		$r['oid'] = base64_encode($r['oid']);
	    		$r['escid'] = base64_encode($r['escid']);
	    		$r['subid'] = base64_encode($r['subid']);
	    		$form->populate($r);
	    	}else{
	    		$this->_helper->redirector('index','distribution','distribution');	
	    	}
    	}
    	$form->perid->setAttrib("readonly", "");
    	$this->view->form = $form;    	
    }

    public function deleteAction()
    {
    	$distid = base64_decode($this->_getParam("distid"));
    	$perid = base64_decode($this->_getParam("perid"));
    	$data['eid']=$this->sesion->eid;
    	$data['oid']=$this->sesion->oid;
    	$data['escid']=$this->sesion->escid;
    	$data['subid']=$this->sesion->subid;
    	$data['distid']=$distid;
    	$data['perid']=$perid;
    	$distr_ = new Distribution_Model_DbTable_Distribution();
    	$r = $distr_->_delete($data);
    	$this->_helper->redirector('index','distribution','distribution');
    }

    public function academicloadAction(){
        try {
            $distid = base64_decode($this->_getParam("distid"));
            $perid = base64_decode($this->_getParam("perid"));
            $escid = base64_decode($this->_getParam("escid"));
            $subid = base64_decode($this->_getParam("subid"));
            $this->view->perid=$perid;
            $this->view->escid=$escid;
            $this->view->subid=$subid;
            $this->view->distid=$distid;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function coursesAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid = $this->_getParam("perid"); 
            $escid = $this->_getParam("escid"); 
            $subid = $this->_getParam("subid"); 
            $distid = $this->_getParam("distid");
            $this->view->escid=$escid;
            $this->view->subid=$subid;
            $this->view->perid=$perid;
            $this->view->distid=$distid;

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['subid']=$subid;
            $where['perid']=$perid;
            $order = array('curid  ASC','semid asc','courseid  ASC','turno asc');
            $percourses = new Api_Model_DbTable_PeriodsCourses();
            $courses=$percourses->_getFilter($where,$attrib=null,$order);
            if ($courses) {
                $tam=count($courses);
                $wherecourse['eid']=$eid;
                $wherecourse['oid']=$oid;
                $wherecourse['escid']=$escid;
                $wherecourse['subid']=$subid;
                $cours= new Api_Model_DbTable_Course();
                for ($i=0; $i < $tam; $i++) { 
                    $wherecourse['curid']=$courses[$i]['curid'];
                    $wherecourse['courseid']=$courses[$i]['courseid'];
                    $dbcourse=$cours->_getOne($wherecourse);
                    $courses[$i]['name']=$dbcourse['name'];
                    $courses[$i]['type']=$dbcourse['type'];
                    $courses[$i]['credits']=$dbcourse['credits'];
                }
            }
            $this->view->courses=$courses;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function modifytypecourseAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $subid = $this->_getParam("subid"); 
            $escid = $this->_getParam("escid");
            $courseid = $this->_getParam("courseid");
            $curid = $this->_getParam("curid");
            $turno = $this->_getParam("turno");
            $perid = $this->_getParam("perid");
            $type = $this->_getParam("type");
            $pk["eid"]=$eid;
            $pk["oid"]=$oid;
            $pk["perid"]=$perid;
            $pk["courseid"]=$courseid;
            $pk["escid"]=$escid;
            $pk["subid"]=$subid;
            $pk["curid"]=$curid;
            $pk["turno"]=$turno;
            $data['type_rate']=$type;
            $percourses = new Api_Model_DbTable_PeriodsCourses();
            $percourses->_update($data,$pk);
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function addcoursesAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid = $this->_getParam("perid"); 
            $escid = $this->_getParam("escid"); 
            $subid = $this->_getParam("subid"); 
            $distid = $this->_getParam("distid");
            $this->view->perid=$perid;
            $this->view->distid=$distid;

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['state']="A";
            $cur = new Api_Model_DbTable_Curricula();
            $datacur = $cur->_getFilter($where,$attrib=null,$orders=null);
            $where['state']="T";
            $datacur1 = $cur->_getFilter($where,$attrib=null,$orders=null);
            if (!$datacur) $curriculas=$datacur1;
            else {
                if ($datacur1) $curriculas=array_merge($datacur,$datacur1);
                else $curriculas=$datacur;
            }
            $this->view->curriculas=$curriculas;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function listcoursesAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $curid = $this->_getParam("curid"); 
            $escid = $this->_getParam("escid"); 
            $semid = $this->_getParam("semid");
            $perid = $this->_getParam("perid");
            $distid = $this->_getParam("distid");
            $this->view->escid=$escid;
            $this->view->perid=$perid;
            $this->view->semid=$semid;
            $this->view->distid=$distid;

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['curid']=$curid;
            $where['escid']=$escid;
            $where['semid']=$semid;
            $where['state']="A";
            $order = array('courseid  ASC');
            $cour = new Api_Model_DbTable_Course();
            $courses = $cour->_getFilter($where,$attrib=null,$order);
            $this->view->courses=$courses;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function savecourseAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid=$this->sesion->uid;
            $subid=$this->_getParam("subid");
            $escid=$this->_getParam("escid");
            $perid=$this->_getParam("perid");
            $courseid=$this->_getParam("courseid");
            $curid=$this->_getParam("curid");
            $semid=$this->_getParam("semid");
            $distid=$this->_getParam("distid");
            $turno_ = $this->_getParam("turno");
            $turno1_ = $this->_getParam("turno1");
            $turno2_ = $this->_getParam("turno2");
            $turno3_ = $this->_getParam("turno3");
            $turno4_ = $this->_getParam("turno4");
            $turno5_ = $this->_getParam("turno5");
            $this->view->subid=$subid;
            $this->view->escid=$escid;
            $this->view->perid=$perid;
            $this->view->distid=$distid;
            $tam=count($courseid);
            
            $reg = new Api_Model_DbTable_PeriodsCourses();
            $where['eid'] = $data['eid'] = $eid;
            $where['oid'] = $data['oid'] = $oid;
            $where['perid'] = $data['perid'] = $perid;
            $where['curid'] = $data['curid'] = $curid;
            $where['escid'] = $data['escid'] = $escid;
            $where['subid'] = $data['subid'] = $subid;
            $data['semid'] = $semid;
            $data['distid'] = $distid;
            for($i=0; $i<$tam; $i++){
                $where['courseid'] = $data['courseid'] = $courseid[$i];
                $data['state_record'] = 'A';
                $data['state'] = 'A';
                $data['receipt'] = 'N';
                $data['register'] = $uid;
                $data['type_rate'] = '';
                $data['created'] = date("Y-m-d h:m:s");
                
                if($turno_[$i]=="A"){
                    $where['turno']= $turno_[$i];
                    $data['turno'] = $turno_[$i];
                    $datpercour=$reg->_getOne($where);
                    if (!$datpercour) $reg-> _save($data);
                }

                if($turno1_[$i]=="B"){
                    $where['turno']= $turno1_[$i];
                    $data['turno'] = $turno1_[$i];
                    $datpercour=$reg->_getOne($where);
                    if (!$datpercour) $reg-> _save($data);
                }

                if($turno2_[$i]=="C"){
                    $where['turno']= $turno2_[$i];
                    $data['turno'] = $turno2_[$i];
                    $datpercour=$reg->_getOne($where);
                    if (!$datpercour) $reg-> _save($data);
                }

                if($turno3_[$i]=="D"){
                    $where['turno']= $turno3_[$i];
                    $data['turno'] = $turno3_[$i];
                    $datpercour=$reg->_getOne($where);
                    if (!$datpercour) $reg-> _save($data);
                }

                if($turno4_[$i]=="E"){
                    $where['turno']= $turno4_[$i];
                    $data['turno'] = $turno4_[$i];
                    $datpercour=$reg->_getOne($where);
                    if (!$datpercour) $reg-> _save($data);
                }

                if($turno5_[$i]=="F"){
                    $where['turno']= $turno5_[$i];
                    $data['turno'] = $turno5_[$i];
                    $datpercour=$reg->_getOne($where);
                    if (!$datpercour) $reg-> _save($data);
                }
                if ($i==($tam-1)) $this->view->msg=1;
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function deletecourseAction(){
        try {
            $this->_helper->layout()->disablelayout();          
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $courseid=$this->_getParam("courseid");
            $semid=$this->_getParam("semid");
            $curid=$this->_getParam("curid");
            $escid=$this->_getParam("escid");
            $perid=$this->_getParam("perid");
            $turno=$this->_getParam("turno");
            $subid=$this->_getParam("subid");
            $this->view->subid=$subid;
            $this->view->escid=$escid;
            $this->view->perid=$perid;

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['perid']=$perid;
            $where['curid']=$curid;
            $where['courseid']=$courseid;
            $where['subid']=$subid;
            $where['turno']=$turno;
            $percourses = new Api_Model_DbTable_PeriodsCourses();
            $datpercour=$percourses->_getOne($where);
            $doccour= new Api_Model_DbTable_Coursexteacher();
            $datadoccour=$doccour->_getFilter($where,$attrib=null,$orders=null);
            if (!$datadoccour) {
                $percourses->_delete($where);
                $datpercourse=$percourses->_getOne($where);
                if($datpercourse) $this->view->exis=1;
            }else $this->view->exis=1;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function teachersAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid = $this->_getParam("perid"); 
            $escid = $this->_getParam("escid"); 
            $subid = $this->_getParam("subid"); 
            $distid = $this->_getParam("distid");
            $this->view->escid=$escid;
            $this->view->subid=$subid;
            $this->view->perid=$perid;
            $this->view->distid=$distid;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function primaryteacherAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid = $this->_getParam("perid"); 
            $escid = $this->_getParam("escid"); 
            $subid = $this->_getParam("subid"); 
            $distid = $this->_getParam("distid");
            $this->view->perid=$perid;
            $this->view->subid=$subid;
            $this->view->distid=$distid;
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['rid']='DC';
            $where['state']='A';            
            $doc = new Api_Model_DbTable_Users();
            $teacher = $doc->_getUsersXEscidXRidXState($where);
            $where['state']='I';
            $teacher1 = $doc->_getUsersXEscidXRidXState($where);
            if ($teacher1) $datateacher=array_merge($teacher,$teacher1);
            else $datateacher=$teacher;
            $tam=count($datateacher);
            $whereinfo['eid']=$eid;
            $whereinfo['oid']=$oid;
            $whereinfo['escid']=$escid;
            $whereinfo['subid']=$subid;
            $info= new Api_Model_DbTable_UserInfoTeacher();
            for ($i=0; $i < $tam; $i++) {
                $whereinfo['pid']=$datateacher[$i]['pid'];
                $whereinfo['uid']=$datateacher[$i]['uid'];
                $datainfo=$info->_getOne($whereinfo);
                $datateacher[$i]['category']=$datainfo['category'];
                $datateacher[$i]['condision']=$datainfo['condision'];
                $datateacher[$i]['dedication']=$datainfo['dedication'];
                $datateacher[$i]['charge']=$datainfo['charge'];
                $datateacher[$i]['contract']=$datainfo['contract'];
            }
            $this->view->teacher=$datateacher;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function modifyteacherAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid = $this->_getParam("uid"); 
            $pid = $this->_getParam("pid"); 
            $subid = $this->_getParam("subid"); 
            $escid = $this->_getParam("escid");
            $content = $this->_getParam("content");
            $option = $this->_getParam("option");
            $this->view->escid=$escid;
            $this->view->subid=$subid;
            $this->view->perid=$perid;
            $this->view->distid=$distid;
            $pk['eid']=$eid;
            $pk['oid']=$oid;
            $pk['escid']=$escid;
            $pk['uid']=$uid;
            $pk['pid']=$pid;
            $pk['subid']=$subid;
            if ($option=="CAT") $data['category']=$content;
            if ($option=="CON") $data['condision']=$content;
            if ($option=="DED") $data['dedication']=$content;
            if ($option=="STA") {
                $data['state']=$content;
                $user = new Api_Model_DbTable_Users();
                if ($user->_update($data,$pk)) $this->view->state=1;
            }else{
                $info = new Api_Model_DbTable_UserInfoTeacher();
                $info->_update($data,$pk);
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function practiceteacherAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid = $this->_getParam("perid"); 
            $escid = $this->_getParam("escid"); 
            $subid = $this->_getParam("subid"); 
            $distid = $this->_getParam("distid");
            $this->view->perid=$perid;
            $this->view->distid=$distid;
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['rid']='JP';
            $where['state']='A';            
            $doc = new Api_Model_DbTable_Users();
            $teacher = $doc->_getUsersXEscidXRidXState($where);
            $where['state']='I';
            $teacher1 = $doc->_getUsersXEscidXRidXState($where);
            if (!$teacher) $datateacher=$teacher1;
            else{
                if ($teacher1) $datateacher=array_merge($teacher,$teacher1);
                else $datateacher=$teacher;
            }
            $tam=count($datateacher);
            $whereinfo['eid']=$eid;
            $whereinfo['oid']=$oid;
            $whereinfo['escid']=$escid;
            $whereinfo['subid']=$subid;
            $info= new Api_Model_DbTable_UserInfoTeacher();
            for ($i=0; $i < $tam; $i++) {
                $whereinfo['pid']=$datateacher[$i]['pid'];
                $whereinfo['uid']=$datateacher[$i]['uid'];
                $datainfo=$info->_getOne($whereinfo);
                $datateacher[$i]['category']=$datainfo['category'];
                $datateacher[$i]['condision']=$datainfo['condision'];
                $datateacher[$i]['dedication']=$datainfo['dedication'];
                $datateacher[$i]['charge']=$datainfo['charge'];
                $datateacher[$i]['contract']=$datainfo['contract'];
            }
            $this->view->teachers=$datateacher;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function supportteacherAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid = $this->_getParam("perid"); 
            $escid = $this->_getParam("escid"); 
            $subid = $this->_getParam("subid"); 
            $distid = $this->_getParam("distid");
            $this->view->perid=$perid;
            $this->view->distid=$distid;
            $this->view->escid=$escid;
            $this->view->subid=$subid;

            // $fm=new Distribucion_Form_Buscar();
            // $fm->guardar->setLabel("Buscar");
            // $this->view->fm=$fm;
            // $escid=$this->sesion->escid;         
            // $sedid=$this->sesion->sedid;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function assigncoursesAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $escid=$this->sesion->escid;
            $perid = base64_decode($this->_getParam("perid"));
            $esciddoc = base64_decode($this->_getParam("escid"));
            $subid = base64_decode($this->_getParam("subid"));
            $distid = base64_decode($this->_getParam("distid"));
            $uid = base64_decode($this->_getParam("uid"));
            $pid = base64_decode($this->_getParam("pid"));
            $this->view->uid=$uid;
            $this->view->escid=$escid;
            $this->view->perid=$perid;

            $wherepers['eid']=$eid;
            $wherepers['pid']=$pid;
            $pers = new Api_Model_DbTable_Person();
            $dataperson=$pers->_getOne($wherepers);
            $this->view->person=$dataperson;
            $wherecurr['eid']=$eid;
            $wherecurr['oid']=$oid;
            $wherecurr['escid']=$escid;
            $wherecurr['state']="A";
            $cur = new Api_Model_DbTable_Curricula();
            $datacur = $cur->_getFilter($wherecurr,$attrib=null,$orders=null);
            $wherecurr['state']="T";
            $datacur1 = $cur->_getFilter($wherecurr,$attrib=null,$orders=null);
            if (!$datacur) $curriculas=$datacur1;
            else {
                if ($datacur1) $curriculas=array_merge($datacur,$datacur1);
                else $curriculas=$datacur;
            }
            $this->view->curriculas=$curriculas;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function coursesxcurriculaAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
               
            $curid= $this->_getParam("curid");
            $escid= $this->_getParam("escid");
            $perid= $this->_getParam("perid");

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['perid']=$perid;
            $where['curid']=$curid;
            $order = array('semid asc','courseid  ASC','turno asc');
            $percourses = new Api_Model_DbTable_PeriodsCourses();
            $courses=$percourses->_getFilter($where,$attrib=null,$order);
            if ($courses) {
                $tam=count($courses);
                $wherecourse['eid']=$eid;
                $wherecourse['oid']=$oid;
                $wherecourse['escid']=$escid;
                $wherecourse['subid']=$subid;
                $cours= new Api_Model_DbTable_Course();
                for ($i=0; $i < $tam; $i++) { 
                    $wherecourse['curid']=$courses[$i]['curid'];
                    $wherecourse['courseid']=$courses[$i]['courseid'];
                    $dbcourse=$cours->_getOne($wherecourse);
                    $courses[$i]['name']=$dbcourse['name'];
                    $courses[$i]['type']=$dbcourse['type'];
                    $courses[$i]['credits']=$dbcourse['credits'];
                }
            }
            $this->view->courses=$courses;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}

