<?php
class Admin_SpecialityController extends Zend_Controller_Action{

	public function init(){
		$this->eid='20154605046';
		$this->oid='1';
	}

	public function indexAction(){
		try {
			$where['eid']=$this->eid;
			$where['oid']=$this->oid;
			$list=new Api_Model_DbTable_Speciality();
			$data=$list->_getAll($where);
			$this->view->data=$data;			
		} catch (Exception $e) {
			print "Error: get Speciality".$e->getMessage();
		}

	}

	public function getfacultyAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$where['eid']=$this->eid;
			$where['oid']=$this->oid;
			$where['facid']=$this->_getParam('facid');
			$where['state']='A';
			$filter=new Api_Model_DbTable_Speciality();
			$data=$filter->_getFilter($where);
			// $data=$filter->_getFilter(array("eid" => $eid, "oid" => $oid,"facid" =>$facid,"state" =>$state));
			$this->view->data=$data;			
		} catch (Exception $e) {
			print "Error: get Faculty".$e->getMessage();
		}
	}

	public function newAction(){
		try {
			$eid=$this->eid;
			$oid=$this->oid;
			$form= new Admin_Form_Speciality();
			$this->view->form=$form;
            if ($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();
                // print_r($frmdata);
                if ($form->isValid($frmdata))
                {                    
                    unset($frmdata['send']);
                    trim($frmdata['escid']);
                    trim($frmdata['name']);
                    trim($frmdata['atribbute']);
                    $frmdata['eid']=$eid;
                    $frmdata['oid']=$oid;
                    $frmdata['created']=date('Y-m-d h:m:s'); 
                    $frmdata['register']='000XXX';                  
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
			$eid=$this->eid;
			$oid=$this->oid;
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
                    $frmdata['modified']='000XXX';
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
		$eid=$this->eid;
		$oid=$this->oid;
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