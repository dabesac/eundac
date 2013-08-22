<?php

class Admin_RolController extends Zend_Controller_Action{

	public function init(){
		$this->eid='20154605046';
		$this->oid='1';
	}

	public function indexAction(){
		try {
			$eid=$this->eid;
			$oid=$this->oid;
			$rol=new Api_Model_DbTable_Rol();
			$where['eid']=$eid;
			$where['oid']=$oid;
			$data=$rol->_getAll($where);
			//print_r($data);
			$this->view->data=$data;
		} catch (Exception $e) {
			print "Error: get Rol".$e->getMessage();
			
		}
	}

	public function newAction(){
		try {
			$eid=$this->eid;
			$oid=$this->oid;
			$form= new Admin_Form_Rol();
			$this->view->form=$form;
            if ($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();
                if ($form->isValid($frmdata))
                {                    
                    unset($frmdata['send']);
                    trim($frmdata['rid']);
                    trim($frmdata['name']);
                    trim($frmdata['state']);
                    trim($frmdata['module']);
                    $frmdata['eid']=$eid;
                    $frmdata['oid']=$oid;                   
                    $reg_= new Api_Model_DbTable_Rol;
                    //print_r($frmdata);
                    $reg_->_save($frmdata);
                    $this->_redirect("/Admin/rol/");                           
                }
                else
                {
                    echo "Ingrese nuevamente por favor";
                }
            }
			
		} 
		catch (Exception $e) {
			print ("Error: Save Rol ".$e->_getMessage());
		}
	}

	public function updateAction(){
		try {
			$eid=$this->eid;
			$oid=$this->oid;
			$rid=base64_decode($this->_getParam('rid'));
			$rol=new Api_Model_DbTable_Rol();
			$where['eid']=$eid;
			$where['oid']=$oid;
			$where['rid']=$rid;
			$data=$rol->_getOne($where);
			$form=new Admin_Form_Rol();
			$form->populate($data);
			$form->rid->setAttrib('readonly',true);
			$this->view->form=$form;
			if ($this->getRequest()->isPost()) {
				$frmdata=$this->getRequest()->getPost();
				if ($form->isValid($frmdata)) {
					unset($frmdata['send']);
					trim($frmdata['name']);
                    trim($frmdata['state']);
                    trim($frmdata['module']);
                    trim($frmdata['prefix']);
                    $pk['eid']=$eid;
                    $pk['oid']=$oid; 
                    $pk['rid']=$rid;                  
                    $reg_= new Api_Model_DbTable_Rol();
                    // print_r($frmdata);
                    $reg_->_update($frmdata,$pk);
                    $this->_redirect("/Admin/rol/");
				}
				else
                {
                    echo "Ingrese nuevamente por favor";
                }
			}			
		} catch (Exception $e) {
			print "Error: Update Rol".$e->getMessage();
		}

	}

	public function deleteAction(){
		$eid=$this->eid;
		$oid=$this->oid;
		$rid=base64_decode($this->_getParam('rid'));
		$data['eid']=$eid;
		$data['oid']=$oid;
		$data['rid']=$rid;
		$reg_= new Api_Model_DbTable_Rol();
		$reg_->_delete($data);
		$this->_redirect("/Admin/rol/");
	}
}
