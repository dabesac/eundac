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
		$eid=$this->sesion->eid;
        $oid=$this->sesion->oid;

        //dataBases
        $facultyDb = new Api_Model_DbTable_Faculty();

        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'state' => 'A');
        $attrib = array('facid', 'name');
        $pdFaculties = $facultyDb->_getFilter($where, $attrib);

        foreach ($pdFaculties as $c => $faculty) {
            if ($faculty['facid'] != 'TODO') {
                $dataFaculty[$c] = array(
                                            'facid' => base64_encode($faculty['facid']),
                                            'name'  => $faculty['name'] );
            }
        }
        $this->view->dataFaculty = $dataFaculty;
        //print_r($pdFaculties);
	}

    public function listcurriculumsAction(){
        $this->_helper->layout()->disableLayout();

        //DataBases
        $curriculumDb = new Api_Model_DbTable_Curricula();

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        $dataGet = base64_decode($this->_getParam('id'));
        $dataGet = explode('|', $dataGet);
        $escid = $dataGet[0];
        $subid = $dataGet[1];

        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'escid' => $escid,
                        'subid' => $subid );
        $attrib = '';
        $order = array('year DESC');
        $pdCurriculums = $curriculumDb->_getFilter($where, $attrib, $order);

        $dataCurriculums = array();
        if ($pdCurriculums) {
            $cTemp  = 0;
            $cClose = 0;
            foreach ($pdCurriculums as $curriculum) {
                if ($curriculum['state'] == 'A') {
                    $dataCurriculums['active'] = array( 'curid' => $curriculum['curid'],
                                                        'name'  => $curriculum['name'],
                                                        'year'  => $curriculum['year'] );
                }elseif ($curriculum['state'] == 'T'){
                    $dataCurriculums['temporary'][$cTemp] = array(  'curid' => $curriculum['curid'],
                                                                    'name'  => $curriculum['name'],
                                                                    'year'  => $curriculum['year'] );
                    $cTemp++;
                }elseif ($curriculum['state'] == 'C'){
                    $dataCurriculums['close'][$cClose] = array( 'curid' => $curriculum['curid'],
                                                                'name'  => $curriculum['name'],
                                                                'year'  => $curriculum['year'] );
                    $cClose++;
                }
            }
        }
        $this->view->dataCurriculums = $dataCurriculums;
        print_r($pdCurriculums);
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
                        $formData['req_1'] = (!empty($formData['req_1']))?$formData['req_1']:null;
                        $formData['req_2'] = (!empty($formData['req_2']))?$formData['req_2']:null;
                        $formData['req_3'] = (!empty($formData['req_3']))?$formData['req_3']:null;
                        $formData['course_equivalence'] = (!empty($formData['course_equivalence']))?$formData['course_equivalence']:null;
                        $formData['course_equivalence_2'] = (!empty($formData['course_equivalence_2']))?$formData['course_equivalence_2']:null;
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

                    if (!$curant[0]['curricula_ant']) {
                        $dbschool = new Api_Model_DbTable_Speciality();
                        $dataschool= $dbschool->_getSchoolnewFaculty($where=array('escid'=>$escid));

                        $curant=$base_cur->_getCurriculaAnterior($curid,$dataschool[0]['escid']);
                        $wherecour=array('eid'=>$eid,'oid'=>$oid,
                                  'curid'=>$curant[0]['curricula_ant'],'escid'=>$dataschool[0]['escid'],
                                  'subid'=>$subid, 'state'=>"A");

                        $course = new Api_Model_DbTable_Course();
                        $wherecourse=array('eid'=>$eid,'oid'=>$oid,
                                      'curid'=>$curid,'escid'=>$escid,
                                      'subid'=>$subid,'courseid'=>$courseid);
                        $coursedata=$course->_getOne($wherecourse);

                        $datacourses_ant=$course->_getFilter($wherecour,$attrib=null,$orders=array('semid','courseid asc'));
                        $wherecour['curid']=$curid;
                        $wherecour['escid']=$escid;
                        $datacourses=$course->_getFilter($wherecour,$attrib=null,$orders=array('semid','courseid asc'));
                    }
                    else{
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
                }
                $form= new Rcentral_Form_Course();
                if ($datacourses_ant) {
                    foreach ($datacourses_ant as $datacourses_ant) {
                        $form->course_equivalence->addMultiOption($datacourses_ant['courseid'],$datacourses_ant['courseid']." | ".$datacourses_ant['name']);
                        $form->course_equivalence_2->addMultiOption($datacourses_ant['courseid'],$datacourses_ant['courseid']." | ".$datacourses_ant['name']);
                    }                    
                }
                else{
                    $form->course_equivalence->addMultiOption('','No tiene currícula anterior');
                    $form->course_equivalence_2->addMultiOption('','No tiene currícula anterior');
                    $this->view->clave='1';
                }
                if ($datacourses) {
                    foreach ($datacourses as $datacourses) {
                        $form->req_1->addMultiOption($datacourses['courseid'],$datacourses['courseid']." | ".$datacourses['name']);
                        $form->req_2->addMultiOption($datacourses['courseid'],$datacourses['courseid']." | ".$datacourses['name']);
                        $form->req_3->addMultiOption($datacourses['courseid'],$datacourses['courseid']." | ".$datacourses['name']);
                    }
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