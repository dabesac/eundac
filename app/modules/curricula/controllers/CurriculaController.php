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
            $escid=base64_decode($this->_getParam('escid'));
            $subid=base64_decode($this->_getParam('subid')); 
            $form = new Rcentral_Form_Curricula();
            $form->subid->setvalue($subid);
            $form->escid_cur->setvalue($escid);
            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($this->getRequest()->getPost())){
                    $formData['eid']=$eid;
                    $formData['oid']=$oid;
                    $formData['created']=date('Y-m-d h:m:s');
                    $formData['register']=$this->sesion->uid;
                    $formData['escid']=$formData['escid_cur'];
                    unset($formData['escid_cur']);
                    // print_r($formData);
                    // echo"asfs";
                    $base_curricula = new Api_Model_DbTable_Curricula();
                    // $base_curricula->save($formData);
                    if ($base_curricula->_save($formData)) {
                        $json = array('status' => true,
                                'tmp' => $formData['escid']."--".$formData['subid']);
                        $this->_response->setHeader('Content-Type', 'application/json');  
                        $this->view->data = $json;
                    }else{
                        $json = array('status' => false);
                        $this->_response->setHeader('Content-Type', 'application/json');  
                        $this->view->data = $json;
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
            $curid=base64_decode($this->_getParam('curid'));
            $escid=base64_decode($this->_getParam('escid'));
            $subid=base64_decode($this->_getParam('subid'));
            $accion=$this->_getParam('accion');
            $where=array('eid'=>$eid, 'oid'=>$oid, 'escid'=>$escid,
                        'curid'=>$curid, 'subid'=>$subid);
            $curr= new Api_Model_DbTable_Curricula();
            $curricula=$curr->_getOne($where);
            $form = new Rcentral_Form_Curricula();
            $form->year->setAttrib("disabled",'disabled');
            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                // print_r($formData);
                if ($form->isValid($formData)) {
                    $pk=array('eid'=>$this->sesion->eid,
                                'oid'=>$this->sesion->oid,
                                'escid'=>$formData['escid_cur'],
                                'curid'=>$formData['curid'],
                                'subid'=>$formData['subid']);
                    $formData['updated']=date('Y-m-d h:m:s');
                    $formData['modified']=$this->sesion->uid;
                    unset($formData['escid_cur']);
                    $base_curricula = new Api_Model_DbTable_Curricula();
                    if ($base_curricula->_update($formData,$pk)) {
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
                $where=array('eid'=>$eid,'oid'=>$oid,
                              'curid'=>$curid,'escid'=>$escid,
                              'subid'=>$subid);
                // $order=" semid ASC";
                $base_course= new Api_Model_DbTable_Curricula();
                $data_course = $base_course->_getAmountCourses($curid,$subid,$escid,$oid,$eid);
                // $data_course = $base_course->_getAll($where,$order);
                // if ($data_course){
                //       $base_semestre = new Api_Model_DbTable_Semester();
                //       foreach ($data_course as $key => $data) {
                //             // echo $data['semid'];
                //             $where=array('eid'=>$eid,'oid'=>$oid,'semid'=>$data['semid']);
                //             $nom_sem=$base_semestre->_getOne($where);
                //             $data_course[$key]['name_sem']=$nom_sem['name'];
                //             // print_r($nom_sem);
                //             // $data_course[$key]['name_sem']=$nom_sem[0];
                //       }
                // }
                $this->view->data_course=$data_course;
                // print_r($data_course);
            } catch (Exception $e) {
                  print "Error: listcourses ".$e->getMessage();
            }
      }

      public function addcoursesAction(){
            try {
                  $eid=$this->sesion->eid;
                  $oid=$this->sesion->oid;
                  $curid=base64_decode($this->_getParam('curid'));
                  $escid=base64_decode($this->_getParam('escid'));
                  $subid=base64_decode($this->_getParam('subid'));
                  $form= new Rcentral_Form_Course();
                  $this->view->form->$formData;
            } catch (Exception $e) {
                  print "Error : in addcourse".$e->getMessage();
            }
      }
}