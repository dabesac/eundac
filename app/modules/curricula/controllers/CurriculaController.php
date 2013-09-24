<?php
class Curricula_CurriculaController extends Zend_Controller_Action
{
	public function init(){

           $sesion  = Zend_Auth::getInstance();
            if(!$sesion->hasIdentity() ){
                  $this->_helper->redirector('index',"index",'default');
            }
            $login = $sesion->getStorage()->read();
            // if (!$login->rol['module']=="docente"){
            //       $this->_helper->redirector('index','index','default');
            // }
            $this->sesion = $login;
	}

	public function indexAction(){
		try {
			$eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $is_director=$this->sesion->infouser['teacher']['is_director'];
            if ($is_director=="S") {
                $facid = $this->sesion->faculty->facid;
                $escid = $this->sesion->escid;
                $subid = $this->sesion->subid;
                $this->view->facid = $facid;
                $this->view->escid = $escid;
                $this->view->subid = $subid;
                $this->view->isdirector=$is_director;
            }
            $where=array('eid'=>$eid,'oid'=>$oid);
            $fac= new Api_Model_DbTable_Faculty();
            $facultad=$fac->_getAll($where);
            $this->view->facultad=$facultad;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function schoolsAction(){
		try {
			$this->_helper->layout()->disableLayout();
            $facid=base64_decode($this->_getParam('facid'));
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $where= array('eid'=>$eid,'oid'=>$oid,'facid'=>$facid,'state'=>'A');
            $attrib=array('escid','name','subid');
            $esc= new Api_Model_DbTable_Speciality();
            $escuelas=$esc->_getFilter($where,$attrib);
            $this->view->escuelas=$escuelas;
            $is_director=$this->sesion->infouser['teacher']['is_director'];
            if ($is_director=="S") {
                $escid = $this->sesion->escid;
                $subid = $this->sesion->subid;
                $this->view->escid = $escid;
                $this->view->subid = $subid;
            }
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function curriculasAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $get=split('--', base64_decode($this->_getParam('escid')));
            $escid=$get[0];
            $subid=$get[1];
            $this->view->escid=$escid;
            $this->view->subid=$subid;
            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
            $curr= new Api_Model_DbTable_Curricula();
            $curriculas=$curr->_getFilter($where);
            $this->view->curriculas=$curriculas;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

    public function newcurriculaAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $escid = base64_decode($this->_getParam('escid'));
            $subid = base64_decode($this->_getParam('subid')); 
            $form = new Rcentral_Form_Curricula();
            $form->subid->setvalue($subid);
            $form->escid_cur->setvalue($escid);
            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                unset($formData['type_periods']);
                if ($form->isValid($this->getRequest()->getPost())){
                    $formData['eid']=$eid;
                    $formData['oid']=$oid;
                    $formData['created']=date('Y-m-d h:m:s');
                    $formData['register']=$this->sesion->uid;
                    $formData['escid']=$formData['escid_cur'];
                    unset($formData['escid_cur']);
                    $base_curricula = new Api_Model_DbTable_Curricula();
                    if ($base_curricula->_save($formData)) {
                        $this->view->escid=$formData['escid'];
                        $this->view->subid=$formData['subid'];
                        $this->view->act=1;
                    }
                }else{
                    $this->view->form=$form;
                }
            }else{
                $this->view->form=$form;
            }
        } catch (Exception $e) {
            print "Error:".$e->getMessage();
        }
    }

	public function modifycurriculaAction(){
		try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $curid = base64_decode($this->_getParam('curid'));
            $escid = base64_decode($this->_getParam('escid'));
            $subid = base64_decode($this->_getParam('subid'));
            $accion = base64_decode($this->_getParam('opti'));
            $where = array('eid'=>$eid, 'oid'=>$oid, 'escid'=>$escid,
                        'curid'=>$curid, 'subid'=>$subid);
            $curr= new Api_Model_DbTable_Curricula();
            $curricula=$curr->_getOne($where);

            if ($accion=="V") {
                $this->view->option=$accion;
                $this->view->curricula=$curricula;
            }else{
                $form = new Rcentral_Form_Curricula();
                $form->year->setAttrib("disabled",'disabled');
                if ($this->getRequest()->isPost()) {
                    $formData = $this->getRequest()->getPost();
                    if ($form->isValid($formData)) {
                        $pk=array('eid'=>$this->sesion->eid, 'oid'=>$this->sesion->oid,
                                'escid'=>$formData['escid_cur'], 'curid'=>$formData['curid'],
                                'subid'=>$formData['subid']);
                        $formData['updated']=date('Y-m-d h:m:s');
                        $formData['modified']=$this->sesion->uid;
                        unset($formData['escid_cur']);
                        $base_curricula = new Api_Model_DbTable_Curricula();
                        if ($base_curricula->_update($formData,$pk)) {
                            $this->view->escid=$pk['escid'];
                            $this->view->subid=$pk['subid'];
                            $this->view->act=1;
                        }
                    }else{
                        $form->populate($formData);
                        $this->view->form=$form;
                    }
                }else{
                    $form->escid_cur->setvalue($curricula['escid']);
                    $form->populate($curricula);
                    $this->view->form=$form;
                }
            }
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}
      public function listcoursesAction(){
            try {
                $eid=$this->sesion->eid;
                $oid=$this->sesion->oid;
                $curid=base64_decode($this->_getParam('curid'));
                $escid=base64_decode($this->_getParam('escid'));
                $subid=base64_decode($this->_getParam('subid'));
                $action=$this->_getParam('accion');
                $this->view->action=$action;
                $this->view->eid=$eid;
                $this->view->oid=$oid;
                $this->view->escid=$escid;
                $this->view->curid=$curid;
                $this->view->subid=$subid;
                $where=array('eid'=>$eid,'oid'=>$oid,
                              'curid'=>$curid,'escid'=>$escid,
                              'subid'=>$subid);
                $base_course= new Api_Model_DbTable_Curricula();
                $data_course = $base_course->_getAmountCourses($curid,$subid,$escid,$oid,$eid);
                $this->view->data_course=$data_course;
            } catch (Exception $e) {
                  print "Error: listcourses ".$e->getMessage();
            }
      }

      public function addcoursesAction(){
            try {
                $this->_helper->layout()->disableLayout();
                $eid=$this->sesion->eid;
                $oid=$this->sesion->oid;
                $curid=$this->_getParam('curid');
                $escid=$this->_getParam('escid');
                $subid=$this->_getParam('subid');
                $this->view->escid=$escid;
                $this->view->curid=$curid;
                $this->view->subid=$subid;
                if ($escid) {
                    $base_cur= new Api_Model_DbTable_Curricula();
                    $curant=$base_cur->_getCurriculaAnterior($curid,$escid);
                    $wherecour=array('eid'=>$eid,'oid'=>$oid,
                                  'curid'=>$curant[0]['curricula_ant'],'escid'=>$escid,
                                  'subid'=>$subid, 'state'=>"A");
                    $course = new Api_Model_DbTable_Course();
                    $datacourses_ant=$course->_getFilter($wherecour,$attrib=null,$orders=array('semid','courseid asc'));
                    $wherecour['curid']=$curid;
                    $datacourses=$course->_getFilter($wherecour,$attrib=null,$orders=array('semid','courseid asc'));
                }
                $form = new Rcentral_Form_Course();
                foreach ($datacourses_ant as $datacourses_ant) {
                    $form->course_equivalence->addMultiOption($datacourses_ant['courseid'],$datacourses_ant['courseid']." | ".$datacourses_ant['name']);
                    $form->course_equivalence_2->addMultiOption($datacourses_ant['courseid'],$datacourses_ant['courseid']." | ".$datacourses_ant['name']);
                }
                foreach ($datacourses as $datacourses) {
                    $form->req_1->addMultiOption($datacourses['courseid'],$datacourses['courseid']." | ".$datacourses['name']);
                    $form->req_2->addMultiOption($datacourses['courseid'],$datacourses['courseid']." | ".$datacourses['name']);
                    $form->req_3->addMultiOption($datacourses['courseid'],$datacourses['courseid']." | ".$datacourses['name']);
                }
                if ($this->getRequest()->isPost()) {
                    $formData = $this->getRequest()->getPost();
                    if ($form->isValid($formData)) {
                        $formData['eid']=$eid;
                        $formData['oid']=$oid;
                        $formData['created']=date('Y-m-d h:m:s');
                        $formData['register']=$this->sesion->uid;
                        $base_course = new Api_Model_DbTable_Course();
                        $base_course->_save($formData);
                        $this->view->msg=1;
                    }else{
                        $this->view->form=$form;
                    }
                }else{
                    $this->view->form=$form;
                }
            } catch (Exception $e) {
                  print "Error : in addcourse".$e->getMessage();
            }
      }

      public function modifycoursesAction(){
        try {
                $this->_helper->layout()->disableLayout();
                $eid=$this->sesion->eid;
                $oid=$this->sesion->oid;
                $curid=$this->_getParam('curid');
                $escid=$this->_getParam('escid');
                $subid=$this->_getParam('subid');
                $courseid=$this->_getParam('courseid');
                $this->view->escid=$escid;
                $this->view->curid=$curid;
                $this->view->subid=$subid;
                $this->view->courseid=$courseid;
                if ($courseid) {
                    $base_cur= new Api_Model_DbTable_Curricula();
                    $curant=$base_cur->_getCurriculaAnterior($curid,$escid);
                    $wherecour=array('eid'=>$eid,'oid'=>$oid,
                                  'curid'=>$curant[0]['curricula_ant'],'escid'=>$escid,
                                  'subid'=>$subid, 'state'=>"A");

                    $course = new Api_Model_DbTable_Course();
                    $wherecourse=array('eid'=>$eid,'oid'=>$oid,
                                  'curid'=>$curid,'escid'=>$escid,
                                  'subid'=>$subid,'courseid'=>$courseid);
                    $coursedata=$course->_getOne($wherecourse);

                    $datacourses_ant=$course->_getFilter($wherecour,$attrib=null,$orders=array('semid','courseid asc'));
                    $wherecour['curid']=$curid;
                    $datacourses=$course->_getFilter($wherecour,$attrib=null,$orders=array('semid','courseid asc'));
                }
                $form= new Rcentral_Form_Course();
                foreach ($datacourses_ant as $datacourses_ant) {
                    $form->course_equivalence->addMultiOption($datacourses_ant['courseid'],$datacourses_ant['courseid']." | ".$datacourses_ant['name']);
                    $form->course_equivalence_2->addMultiOption($datacourses_ant['courseid'],$datacourses_ant['courseid']." | ".$datacourses_ant['name']);
                }
                foreach ($datacourses as $datacourses) {
                    $form->req_1->addMultiOption($datacourses['courseid'],$datacourses['courseid']." | ".$datacourses['name']);
                    $form->req_2->addMultiOption($datacourses['courseid'],$datacourses['courseid']." | ".$datacourses['name']);
                    $form->req_3->addMultiOption($datacourses['courseid'],$datacourses['courseid']." | ".$datacourses['name']);
                }
                $form->courseid->setAttrib('readonly','readonly');
                if ($this->getRequest()->isPost()) {
                    $formData = $this->getRequest()->getPost();
                    if ($form->isValid($formData)) {
                        $pk=array('eid'=>$this->sesion->eid, 'oid'=>$this->sesion->oid,
                                'escid'=>$formData['escid'], 'curid'=>$formData['curid'],
                                'subid'=>$formData['subid'], 'courseid'=>$formData['courseid']);
                        $formData['updated']=date('Y-m-d h:m:s');
                        $formData['modified']=$this->sesion->uid;
                        $base_cour = new Api_Model_DbTable_Course();
                        if ($base_cour->_update($formData,$pk)) {
                            $this->view->msg=1;
                        }
                    }else{
                        $form->populate($formData);
                        $this->view->form=$form;
                    }
                }else{
                    $form->populate($coursedata);
                    $this->view->form=$form;
                }
        } catch (Exception $e) {
              print "Error : in modifycourse".$e->getMessage();
        }
      }
}