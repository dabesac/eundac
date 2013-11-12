<?php
class Bienestar_SpecialitylockController extends Zend_Controller_Action {

	public function init(){
		$sesion = Zend_Auth::getInstance();
		if (!$sesion->hasIdentity()) {
			$this->_helper->redirector('index','index','default');
		}
		$login = $sesion->getStorage()->read();
		$this->sesion = $login;
	}

	public function indexAction(){
		try {
			$frm = new Bienestar_Form_Speciality();
			$this->view->frm=$frm;
			$fm= new Bienestar_Form_Lock();
			$this->view->fm=$fm;

		} catch (Exception $e) {
			print "Error: Lock Speciality".$e->getMessage();
		}
	}

	public function getspecialityAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$where['eid']=$this->sesion->eid;
			$where['oid']=$this->sesion->oid;
			$where['facid']=$this->_getParam('facid');
			$where['state']='A';
			$attrib = array('escid','subid','facid','name','state');
			$filter=new Api_Model_DbTable_Speciality();
			$data=$filter->_getFilter($where,$attrib);
			$this->view->data=$data;
		} catch (Exception $e) {
			print "Error: get Speciality".$e->getMessage();
		}
	}

	public function listlockAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$escid=$this->_getParam('escid');
			$subid=$this->_getParam('subid');
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'rid'=>'AL','state'=>'A');
			$attrib=array('pid','uid','escid','subid');
			$dbuser= new Api_Model_DbTable_Users();
			$datauser=$dbuser->_getFilter($where,$attrib);
			// print_r($datauser);
			$this->view->datauser=$datauser;
		} catch (Exception $e) {
			print "Error: detail lock ".$e->getMessage();
				
		}	
	}
}
