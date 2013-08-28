<?php

class Admin_ModuleController extends Zend_Controller_Action{

	public function init(){
		$this->sesion->eid='20154605046';
		$this->sesion->oid='1';
	}

	public function indexAction(){
		try {
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$module=new Api_Model_DbTable_Module();
			$where['eid']=$eid;
			$where['oid']=$oid;
			$data=$module->_getAll($where);
			$this->view->data=$data;
		} catch (Exception $e) {
			print "Error: get Rol".$e->getMessage();
			
		}
	}

	public function newAction(){
		try {
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$form= new Admin_Form_Module();
            if ($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();
                if ($form->isValid($frmdata))
                {                    
                    unset($frmdata['save']);
                    $frmdata['mid']=time();
                    trim($frmdata['mid']);
                    trim($frmdata['name']);
                    trim($frmdata['imgicon']);
                    trim($frmdata['state']);
                    trim($frmdata['module']);
                    $frmdata['eid']=$eid;
                    $frmdata['oid']=$oid;                   
                    $reg_= new Api_Model_DbTable_Module();
                    if ($reg_->_save($frmdata))
                    	$this->_redirect("/admin/module/");
                    print_r($frmdata);                           
                }
                else
                {
                	$form->populate($frmdata);
                }
            }
			$this->view->form=$form;
		} 
		catch (Exception $e) {
			print ("Error: Save Rol ".$e->_getMessage());
		}
	}

	public function editAction(){
		try {
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$mid= base64_decode($this->_getParam("mid"));
			if ($mid=="") $this->_redirect("/admin/module/");
			$form= new Admin_Form_Module();
			$form->setAction("/admin/module/edit");
			if ($this->getRequest()->isPost())
			{
				$frmdata=$this->getRequest()->getPost();
				if ($form->isValid($frmdata))
				{
					$frmdata['mid']=base64_decode($frmdata['mid']);
					$data['mid'] = $frmdata['mid'];
					$data['eid'] = $eid;
					$data['oid'] = $oid;
					unset($frmdata['save']);
					unset($frmdata['mid']);
					trim($frmdata['name']);
					trim($frmdata['imgicon']);
					trim($frmdata['state']);
					trim($frmdata['module']);
					$reg_= new Api_Model_DbTable_Module();
					if ($reg_->_update($frmdata, $data))
						$this->_redirect("/admin/module/");
				}
				else
				{
					$form->populate($frmdata);
				}
			}else{
				$module = new Api_Model_DbTable_Module();
				$data['eid']= $eid;
				$data['oid']= $oid;
				$data['mid']= $mid;
				$rows = $module->_getOne($data);
				if ($rows){
					$rows['mid']=base64_encode($rows['mid']);
					$form->populate($rows);
				}
			}
			$this->view->form=$form;
		} catch (Exception $e) {
			print "Error: Update Rol".$e->getMessage();
		}

	}

	public function deleteAction(){
		$data['eid']=$this->sesion->eid;
		$data['oid']=$this->sesion->oid;
		$data['mid']=base64_decode($this->_getParam("mid"));
		$reg_= new Api_Model_DbTable_Module();
		if ($reg_->_delete($data))
			$this->_redirect("/admin/module/");
			
	}
}
