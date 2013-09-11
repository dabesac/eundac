<?php
class Admin_SpecialityController extends Zend_Controller_Action{

	public function init(){
 		$sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		$this->sesion = $login;
 	}

	public function indexAction(){
		try {
			$where['eid']=$this->sesion->eid;
			$where['oid']=$this->sesion->oid;
			$form= new Admin_Form_Speciality();
			$this->view->form=$form;
			// $list=new Api_Model_DbTable_Speciality();
			// $data=$list->_getAll($where);
		} catch (Exception $e) {
			print "Error: get Speciality".$e->getMessage();
		}

	}

	public function getspecialityAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$where['eid']=$this->sesion->eid;
			$where['oid']=$this->sesion->oid;
			$where['facid']=$this->_getParam('facid');
			$where['state']='A';
			$filter=new Api_Model_DbTable_Speciality();
			$data=$filter->_getFilter($where);
			// print_r($data);
			$this->view->data=$data;
		} catch (Exception $e) {
			print "Error: get Speciality".$e->getMessage();
		}

	}

	public function getfacultyAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$where['eid']=$this->sesion->eid;
			$where['oid']=$this->sesion->oid;
			$where['facid']=$this->_getParam('facid');
			$where['state']='A';
			$filter=new Api_Model_DbTable_Speciality();
			$data=$filter->_getFilter($where);
			$this->view->data=$data;			
		} catch (Exception $e) {
			print "Error: get Faculty".$e->getMessage();
		}
	}

	public function newAction(){
		try {
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$register=$this->sesion->uid;
			$form= new Admin_Form_Speciality();
			$this->view->form=$form;
            if ($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();
                // print_r($frmdata);exit();
                if ($form->isValid($frmdata))
                {                    
                    unset($frmdata['send']);
                    trim($frmdata['escid']);
                    trim($frmdata['name']);
                    trim($frmdata['atribbute']);
                    $frmdata['eid']=$eid;
                    $frmdata['oid']=$oid;
                    $frmdata['created']=date('Y-m-d h:m:s'); 
                    $frmdata['register']=$register;                  
                    $reg_= new Api_Model_DbTable_Speciality();
                    // print_r($frmdata);
                    $reg_->_save($frmdata);
                    $this->_redirect("/admin/speciality/");                           
                }
                else
                {
                    echo "Ingrese nuevamente por favor";
                }
            }
			
		} catch (Exception $e) {
			print "Error: Save Speciality".$e->getMessage();	
		}

	}

	public function updateAction(){
		try {
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$modified=$this->sesion->uid;
			$escid=base64_decode($this->_getParam('escid'));
			$subid=base64_decode($this->_getParam('subid'));
			$spc=new Api_Model_DbTable_Speciality();
			$where['eid']=$eid;
			$where['oid']=$oid;
			$where['escid']=$escid;
			$where['subid']=$subid;
			$data=$spc->_getOne($where);
			$form=new Admin_Form_Speciality();
			$form->populate($data);
			$form->escid->setAttrib('readonly',true);
			$this->view->form=$form;
			if ($this->getRequest()->isPost()) {
				$frmdata=$this->getRequest()->getPost();
                $frmdata['subid']=$subid;
                $frmdata['facid']=$data['facid'];
				if ($form->isValid($frmdata)) {
					unset($frmdata['send']);
                    trim($frmdata['name']);
                    trim($frmdata['abbreviation']);
                    $frmdata['updated']=date('Y-m-d h:m:s'); 
                    $frmdata['modified']=$modified;
                    $pk['eid']=$eid;
                    $pk['oid']=$oid; 
                    $pk['escid']=$escid;
                    $pk['subid']=$subid;                  
                    $reg_= new Api_Model_DbTable_Speciality();
                    // print_r($frmdata);
                    $reg_->_update($frmdata,$pk);
                    $this->_redirect("/admin/speciality/");
				}
				else
                {
                    echo "Ingrese nuevamente por favor";
                }
			}			
		} catch (Exception $e) {
			print "Error: Update Speciality".$e->getMessage();
		}

	}

	public function deleteAction(){
		$eid=$this->sesion->eid;
		$oid=$this->sesion->oid;
		$escid=base64_decode($this->_getParam('escid'));
		$subid=base64_decode($this->_getParam('subid'));
		$data['eid']=$eid;
		$data['oid']=$oid;
		$data['escid']=$escid;
		$data['subid']=$subid;
		$reg_= new Api_Model_DbTable_Speciality();
		$reg_->_delete($data);
		$this->_redirect("/admin/speciality/");
	}
}