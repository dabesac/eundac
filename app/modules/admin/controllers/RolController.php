<?php

class Admin_RolController extends Zend_Controller_Action{

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
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$rol=new Api_Model_DbTable_Rol();
			$where['eid']=$eid;
			$where['oid']=$oid;
			$orders=array('rid');
			$data=$rol->_getFilter($where,$attrib=null,$orders);
			//print_r($data);
			$this->view->data=$data;
		} catch (Exception $e) {
			print "Error: get Rol".$e->getMessage();
			
		}
	}

	public function newAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
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
                  
                    if ($reg_->_save($frmdata)) {
                  		$this->view->clave=1;      
                    }
                    // $this->_redirect("/admin/rol/");   
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
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
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
				$frmdata['rid']=base64_decode($frmdata['rid']);
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
                    // $reg_->_update($frmdata,$pk);
                    if ($reg_->_update($frmdata,$pk)) {
                  		$this->view->valor=2;      
                    }

                    // $this->_redirect("/admin/rol/");
				}
				else
                {
                    echo "Ingrese nuevamente por favor";
                }
			}
			else{
				$this->view->rid=$rid;
			}		
		} catch (Exception $e) {
			print "Error: Update Rol".$e->getMessage();
		}

	}

	public function deleteAction(){
		$eid=$this->sesion->eid;
		$oid=$this->sesion->oid;
		$rid=base64_decode($this->_getParam('rid'));
		$data['eid']=$eid;
		$data['oid']=$oid;
		$data['rid']=$rid;
		$reg_= new Api_Model_DbTable_Rol();
		$reg_->_delete($data);
		$this->_redirect("/admin/rol/");
	}
}
