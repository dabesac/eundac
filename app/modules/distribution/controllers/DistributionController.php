<?php

class Distribution_DistributionController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (($login->infouser['teacher']['is_director']<>"S")){
    		$this->_helper->redirector('index','error','default');
    	}
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
            $where['subid']=$subid;
            $where['rid']='DC';
            $where['state']='A';  
            $doc = new Api_Model_DbTable_Users();
            $teacher = $doc->_getUserXRidXEscidAll($where);
            $where['state']='I';
            $teacher1 = $doc->_getUserXRidXEscidAll($where);
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
            $this->view->subid=$subid;
            $this->view->distid=$distid;
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['subid']=$subid;
            $where['rid']='JP';
            $where['state']='A';            
            $doc = new Api_Model_DbTable_Users();
            $teacher = $doc->_getUserXRidXEscidAll($where);
            $where['state']='I';
            $teacher1 = $doc->_getUserXRidXEscidAll($where);
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

            $form = new Distribution_Form_Search();            
            $this->view->form = $form;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function listsupportteacherAction(){
        try {
            $this->_helper->layout()->disablelayout();           
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $name=$this->_getParam("name");
            $pid =$this->_getParam("pid");
            $distid =$this->_getParam("distid"); 
            $perid =$this->_getParam("perid"); 
            $this->view->distid=$distid;
            $this->view->perid=$perid;
            $this->view->escid=$escid;
            $this->view->subid=$subid;

            $where['eid']=$eid;
            $user = new Api_Model_DbTable_Users();
            if($pid<>''){
                $where['oid']=$oid;
                $where['pid']=$pid;
                $datauser = $user->_getUserXPid($where);
            }else{
                $where['rid']='DC';
                $where['nom']=strtoupper($name);
                $datauser = $user->_getUsuarioXNombre($where);
            }
            $this->view->usuarios=$datauser;
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
            $subid=$this->sesion->subid;
            $perid = base64_decode($this->_getParam("perid"));
            $esciddoc = base64_decode($this->_getParam("escid"));
            $subiddoc = base64_decode($this->_getParam("subid"));
            $distid = base64_decode($this->_getParam("distid"));
            $uid = base64_decode($this->_getParam("uid"));
            $pid = base64_decode($this->_getParam("pid"));
            $this->view->eid=$eid;
            $this->view->oid=$oid;
            $this->view->uid=$uid;
            $this->view->pid=$pid;
            $this->view->escid=$escid;
            $this->view->subiddoc=$subiddoc;
            $this->view->esciddoc=$esciddoc;
            $this->view->perid=$perid;
            $this->view->subid=$subid;
            $this->view->distid=$distid;

            if ($esciddoc<>$escid) {
                $this->view->support=1;
                $pkdist['eid'] = $eid;
                $pkdist['oid'] = $oid;
                $pkdist['escid'] = $esciddoc;
                $pkdist['perid'] = $perid;
                $dist = new Distribution_Model_DbTable_Distribution();
                $datadist = $dist->_getFilter($pkdist,$atrib=array());
                if(!$datadist){ ?>
                    <script>  
                        alert("No se encuentra disponible los docentes de apoyo de esa Escuela\nIntentelo mas tarde.");
                        window.close();
                    </script>
                    <?php
                    break;
                }
            }

            $wherepers['eid']=$eid;
            $wherepers['pid']=$pid;
            $pers = new Api_Model_DbTable_Person();
            $dataperson=$pers->_getOne($wherepers);
            $this->view->person=$dataperson;

            $form= new Distribution_Form_DistributionAdmin();
            $this->view->form=$form;

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

            $wheredoc['eid']=$eid;
            $wheredoc['oid']=$oid;
            $wheredoc['uid']=$uid;
            $wheredoc['pid']=$pid;
            $wheredoc['perid']=$perid;
            $wheredoc['distid']=$distid;
            $courdoc = new Api_Model_DbTable_Coursexteacher();
            $courasig = $courdoc->_getFilter($wheredoc,$attrib=null,$orders=null);
            if ($courasig) {
                $tam=count($courasig);
                $wherecourse['eid']=$eid;
                $wherecourse['oid']=$oid;
                $cours= new Api_Model_DbTable_Course();
                for ($i=0; $i < $tam; $i++) { 
                    $wherecourse['subid']=$courasig[$i]['subid'];
                    $wherecourse['escid']=$courasig[$i]['escid'];
                    $wherecourse['curid']=$courasig[$i]['curid'];
                    $wherecourse['courseid']=$courasig[$i]['courseid'];
                    $dbcourse=$cours->_getOne($wherecourse);
                    $courasig[$i]['name']=$dbcourse['name'];
                    $courasig[$i]['credits']=$dbcourse['credits'];
                }
            }
            $this->view->cursosasignados=$courasig;
            
            $pk['eid']=$eid;
            $pk['oid']=$oid;
            $pk['escid']=$esciddoc;
            $pk['perid']=$perid;
            $pk['uid']=$uid;
            $pk['pid']=$pid;
            $distadm = new Distribution_Model_DbTable_DistributionAdmin();
            $labor=$distadm->_getFilter($pk,$atrib=array());
            $this->view->administrativas=$labor;

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
            $order = array('semid ASC','courseid ASC','turno asc');
            $percourses = new Api_Model_DbTable_PeriodsCourses();
            $courses=$percourses->_getFilter($where,$attrib=null,$order);
            if ($courses) {
                $tam=count($courses);
                $wherecourse['eid']=$eid;
                $wherecourse['oid']=$oid;
                $wherecourse['escid']=$escid;
                $wherecourse['curid']=$curid;
                $cours= new Api_Model_DbTable_Course();
                for ($i=0; $i < $tam; $i++) { 
                    $wherecourse['subid']=$courses[$i]['subid'];
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

    public function saveasigncourseAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $distid =$this->_getParam("distid"); 
            $semid =$this->_getParam("semid"); 
            $curid =$this->_getParam("curid"); 
            $perid =$this->_getParam("perid"); 
            $courseid =$this->_getParam("courseid"); 
            $grupos =$this->_getParam("grupos"); 
            $hteoria =$this->_getParam("hteoria");
            $hpractica =$this->_getParam("hpractica");
            $totalhoras =$this->_getParam("totalhoras");
            $compromiso =$this->_getParam("compromiso");
            $uid =$this->_getParam("uid");
            $pid =$this->_getParam("pid");
            $turno =$this->_getParam("turno");
            $principal =$this->_getParam("principal");
            $esciddoc =$this->_getParam("esciddoc");
            $subiddoc =$this->_getParam("subiddoc");
            $estado =$this->_getParam("estado");
       
            $this->view->escid=$escid;
            $this->view->subid=$subid;
            $this->view->subiddoc=$subiddoc;
            $this->view->esciddoc=$esciddoc;
            $this->view->uid=$uid;
            $this->view->pid=$pid;
            $this->view->eid=$eid;
            $this->view->oid=$oid;
            $this->view->distid=$distid;
            $this->view->perid=$perid; 

            $wheredoc['eid'] = $datadoccour['eid'] = $eid;
            $wheredoc['oid'] = $datadoccour['oid'] = $oid;
            $wheredoc['escid'] = $datadoccour['escid'] = $escid;
            $wheredoc['subid'] = $datadoccour['subid'] = $subid;
            $wheredoc['courseid'] = $datadoccour['courseid'] = $courseid;
            $wheredoc['curid'] = $datadoccour['curid'] = $curid;
            $wheredoc['turno'] = $datadoccour['turno'] = $turno;
            $wheredoc['perid'] = $datadoccour['perid'] = $perid;
            $datadoccour['uid'] = $uid;
            $datadoccour['pid'] = $pid;
            $datadoccour['hours_t'] = $hteoria;
            $datadoccour['hours_p'] = $hpractica;
            $datadoccour['is_com'] = $compromiso;
            $datadoccour['distid'] = $distid;
            $datadoccour['hours_total'] = $totalhoras;
            $datadoccour['groups'] = $grupos;
            $datadoccour['state'] = $estado;
            $datadoccour['semid'] = $semid;
            $datadoccour['is_main'] = $principal;
            $doccourse = new Api_Model_DbTable_Coursexteacher();
            $exiscour=$doccourse->_getFilter($wheredoc,$attrib=null,$orders=null);
            if($exiscour and $principal=="S"){ ?>
                <script>
                    alert('La Asignatura ya ha sido asignada a otro Docente : Elija otra Asignatura');
                </script>
                <?php   
            }else{
                $wheredoc['uid']=$uid;
                $wheredoc['pid']=$pid;
                $exisdoccour=$doccourse->_getFilter($wheredoc,$attrib=null,$orders=null);
                if ($exisdoccour) { ?>
                    <script type="text/javascript">
                        alert("El Docente ya esta asignado a esta Asignatura.");
                    </script>
                    <?php
                }else{
                    $doccourse->_save($datadoccour);
                    $pkdistdoc['eid'] = $datadistdoc['eid'] = $eid;
                    $pkdistdoc['oid'] = $datadistdoc['oid'] = $oid;
                    $pkdistdoc['perid'] = $datadistdoc['perid'] = $perid;
                    $pkdistdoc['uid'] = $datadistdoc['uid'] = $uid;
                    $pkdistdoc['pid'] = $datadistdoc['pid'] = $pid;
                    $datadistdoc['total_hour_admin'] = 0;
                    $distdoc = new Distribution_Model_DbTable_DistributionTeacher();
                    if($esciddoc==$escid){
                        $pkdistdoc['distid'] = $datadistdoc['distid'] = $distid;
                        $pkdistdoc['subid'] = $datadistdoc['subid'] = $subid;
                        $pkdistdoc['escid'] = $datadistdoc['escid'] = $escid;
                        $exisdistdoc=$distdoc->_getOne($pkdistdoc);
                        if(!$exisdistdoc){
                            $datadistdoc['total_hour_acad'] = $totalhoras;
                            $datadistdoc['register'] = $this->sesion->uid;
                            $datadistdoc['created'] = date('Y-m-d');
                            $distdoc->_save($datadistdoc);
                        }
                    }else{
                        $wheredis['eid']=$eid;
                        $wheredis['oid']=$oid;
                        $wheredis['escid']=$esciddoc;
                        $wheredis['perid']=$perid;
                        $dist = new Distribution_Model_DbTable_Distribution();
                        $distiddoc=$dist->_getFilter($wheredis,$atrib=array());
                        
                        $pkdistdoc['distid'] = $datadistdoc['distid'] = $distiddoc[0]['distid'];
                        $pkdistdoc['subid'] = $datadistdoc['subid'] = $subiddoc;
                        $pkdistdoc['escid'] = $datadistdoc['escid'] = $esciddoc;
                        $exisdistdoc=$distdoc->_getOne($pkdistdoc);
                        if(!$exisdistdoc){
                            $datadistdoc['total_hour_acad'] = $totalhoras;
                            $datadistdoc['register'] = $this->sesion->uid;
                            $datadistdoc['created'] = date('Y-m-d');
                            $distdoc->_save($datadistdoc);
                        }
                    }                    
                }
            }

            $wheretea['eid']=$eid;
            $wheretea['oid']=$oid;
            $wheretea['uid']=$uid;
            $wheretea['pid']=$pid;
            $wheretea['perid']=$perid;
            $wheretea['distid']=$distid;
            $courasig = $doccourse->_getFilter($wheretea,$attrib=null,$orders=null);
            if ($courasig) {
                $tam=count($courasig);
                $wherecourse['eid']=$eid;
                $wherecourse['oid']=$oid;
                $cours= new Api_Model_DbTable_Course();
                for ($i=0; $i < $tam; $i++) { 
                    $wherecourse['subid']=$courasig[$i]['subid'];
                    $wherecourse['escid']=$courasig[$i]['escid'];
                    $wherecourse['curid']=$courasig[$i]['curid'];
                    $wherecourse['courseid']=$courasig[$i]['courseid'];
                    $dbcourse=$cours->_getOne($wherecourse);
                    $courasig[$i]['name']=$dbcourse['name'];
                    $courasig[$i]['credits']=$dbcourse['credits'];
                }
            }
            $this->view->cursosasignados=$courasig;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function deleteasigncourseAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $subiddoc  = base64_decode($this->_getParam("subiddoc")); 
            $esciddoc = base64_decode($this->_getParam('esciddoc'));
            $courseid = base64_decode($this->_getParam("courseid")); 
            $distid = base64_decode($this->_getParam("distid"));
            $turno = base64_decode($this->_getParam("turno")); 
            $curid = base64_decode($this->_getParam("curid"));
            $perid = base64_decode($this->_getParam("perid"));
            $uid = base64_decode($this->_getParam("uid"));
            $pid = base64_decode($this->_getParam("pid"));
            
            $pk['eid']=$eid;
            $pk['oid']=$oid;
            $pk['escid']=$escid;
            $pk['subid']=$subid;
            $pk['courseid']=$courseid;
            $pk['curid']=$curid;
            $pk['turno']=$turno;
            $pk['perid']=$perid;
            $pk['uid']=$uid;
            $pk['pid']=$pid;
            $doccour = new Api_Model_DbTable_Coursexteacher();
            $doccour->_delete($pk);
            $this->_redirect("/distribution/distribution/assigncourses/uid/".base64_encode($uid).
                "/pid/".base64_encode($pid)."/distid/".base64_encode($distid)."/perid/".base64_encode($perid).
                "/subid/".base64_encode($subiddoc)."/escid/".base64_encode($esciddoc));
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function savedistributionadminAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $escid = $this->sesion->escid;
            $subid = $this->sesion->subid;
            $perid = $this->_getParam("perid");
            $uid = $this->_getParam("uid");
            $pid = $this->_getParam("pid");
            $distid = $this->_getParam("distid");
            $subiddoc = $this->_getParam("subiddoc"); 
            $esciddoc = $this->_getParam("esciddoc");
            $work = $this->_getParam("work");            
            $hours = $this->_getParam("hours");
            $this->view->escid=$escid;
            $this->view->subid=$subid;
            $this->view->esciddoc=$esciddoc;
            $this->view->subiddoc=$subiddoc;
            $this->view->uid=$uid;
            $this->view->pid=$pid;
            $this->view->eid=$eid;
            $this->view->oid=$oid;
            $this->view->distid=$distid;
            $this->view->perid=$perid; 

            if($esciddoc<>$escid){
                $wheredis['eid']=$eid;
                $wheredis['oid']=$oid;
                $wheredis['escid']=$esciddoc;
                $wheredis['perid']=$perid;
                $dist = new Distribution_Model_DbTable_Distribution();
                $distiddoc=$dist->_getFilter($wheredis,$atrib=array());
                $dist = $dis->_getDistribucionEscuela($eid,$oid,'2ES',$perid);
                $distid = $distiddoc[0]['distid'];
            }

            $datadistadm['eid'] = $pk['eid'] = $eid;
            $datadistadm['oid'] = $pk['oid'] = $oid;
            $datadistadm['uid'] = $pk['uid'] = $uid;
            $datadistadm['pid'] = $pk['pid'] = $pid;
            $datadistadm['escid'] = $pk['escid'] = $esciddoc;
            $datadistadm['perid'] = $pk['perid'] = $perid;            
            $datadistadm['subid'] = $subiddoc;
            $datadistadm['distid'] = $distid;
            $datadistadm['work'] = $work;
            $datadistadm['hours'] = $hours;
            
            $distadm = new Distribution_Model_DbTable_DistributionAdmin();
            $distadm->_save($datadistadm);
            $labor = $distadm->_getFilter($pk,$atrib=array());
            $this->view->administrativas=$labor;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function deletedistributionadminAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $admdistid = base64_decode($this->_getParam("admdistid"));
            $esciddoc = base64_decode($this->_getParam("esciddoc"));
            $subiddoc = base64_decode($this->_getParam("subiddoc"));
            $distid = base64_decode($this->_getParam("distid"));
            $uid = base64_decode($this->_getParam("uid"));
            $pid = base64_decode($this->_getParam("pid"));
            $perid = base64_decode($this->_getParam("perid"));

            $pk['eid']=$eid;
            $pk['oid']=$oid;
            $pk['perid']=$perid;
            $pk['uid']=$uid;
            $pk['pid']=$pid;
            $pk['admdistid']=$admdistid;
            $distadmin = new Distribution_Model_DbTable_DistributionAdmin();
            if($esciddoc<>$escid){
                $pkdist['eid']=$eid;
                $pkdist['oid']=$oid;
                $pkdist['escid']=$esciddoc;
                $pkdist['perid']=$perid;
                $dist = new Distribution_Model_DbTable_Distribution();
                $datadist = $dist->_getFilter($pkdist,$atrib=array());

                $pk['escid']=$esciddoc;
                $pk['distid']=$datadist[0]['distid'];
                $pk['subid']=$datadist[0]['subid'];
                $distadmin->_delete($pk);
            }else{
                $pk['escid']=$escid;
                $pk['distid']=$distid;
                $pk['subid']=$subid;
                $distadmin->_delete($pk);
            }
            $this->_redirect("/distribution/distribution/assigncourses/uid/".base64_encode($uid).
                "/pid/".base64_encode($pid)."/distid/".base64_encode($distid)."/perid/".base64_encode($perid).
                "/subid/".base64_encode($subiddoc)."/escid/".base64_encode($esciddoc));
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}