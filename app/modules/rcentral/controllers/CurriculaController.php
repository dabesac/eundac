<?php
class Rcentral_CurriculaController extends Zend_Controller_Action
{
	public function init(){
	}

	public function indexAction(){
		try {
			$eid="20154605046";
            $oid="1";
            $where['eid']=$eid;
            $where['oid']=$oid;
            $fac= new Api_Model_DbTable_Faculty();
            $facultad=$fac->_getAll($where,$order='',$start=0,$limit=0);
            $this->view->facultad=$facultad;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function schoolsAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid="20154605046";
            $oid="1";
            $facid=$this->_getParam('facid');
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['facid']=$facid;
            $where['state']='A';
            $esc= new Api_Model_DbTable_Speciality();
            $escuelas=$esc->_getFilter($where);
            $this->view->escuelas=$escuelas;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function curriculasAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid="20154605046";
            $oid="1";
            $escid=$this->_getParam('escid');
            $subid=$this->_getParam('subid');
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['subid']=$subid;
            $curr= new Api_Model_DbTable_Curricula();
            $curriculas=$curr->_getFilter($where);
            $this->view->curriculas=$curriculas;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function modifycurriculaAction(){
		try {
			$eid="20154605046";
            $oid="1";
            $curid=base64_decode($this->_getParam('curid'));
            $escid=base64_decode($this->_getParam('escid'));
            $subid=base64_decode($this->_getParam('subid'));
            $accion=$this->_getParam('accion');
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['curid']=$curid;
            $where['subid']=$subid;
            $curr= new Api_Model_DbTable_Curricula();
            print_r($curricula=$curr->_getOne($where));
            $form = new Rcentral_Form_Curricula();
            $form->populate($curricula);
            $form->year->setAttrib("readonly",true);
            $this->view->form=$form;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}
}