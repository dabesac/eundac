<?php

class Soporte_SemesterController extends Zend_Controller_Action{

	public function init(){
		$this->eid='20154605046';
		$this->oid='1';
	}

	public function indexAction(){
		try {
			$eid=$this->eid;
			$oid=$this->oid;
			$semester=new Api_Model_DbTable_Semester();
			$where['eid']=$eid;
			$where['oid']=$oid;
			$data=$semester->_getAll($where);
			//print_r($data);
			$this->view->data=$data;
		} catch (Exception $e) {
			print "Error: get Semester".$e->getMessage();
			
		}
	}

	public function newAction(){
		try {
			$eid=$this->eid;
			$oid=$this->oid;
			$form= new Soporte_Form_Semester();
			$this->view->form=$form;
            if ($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();
                if ($form->isValid($frmdata))
                {                    
                    unset($frmdata['send']);
                    trim($frmdata['semid']);
                    trim($frmdata['name']);
                    $frmdata['eid']=$eid;
                    $frmdata['oid']=$oid;                   
                    $reg_= new Api_Model_DbTable_Semester();
                    //print_r($frmdata);
                    $reg_->_save($frmdata);
                    $this->_redirect("/soporte/semester/");                           
                }
                else
                {
                    echo "Ingrese nuevamente por favor";
                }
            }
			
		} 
		catch (Exception $e) {
			print ("Error: Save Semester ".$e->_getMessage());
		}


	}

	public function updateAction(){
		try {
			$eid=$this->eid;
			$oid=$this->oid;
			$semid=base64_decode($this->_getParam('semid'));
			$semester=new Api_Model_DbTable_Semester();
			$where['eid']=$eid;
			$where['oid']=$oid;
			$where['semid']=$semid;
			$data=$semester->_getOne($where);
			$form=new Soporte_Form_Semester();
			$form->populate($data);
			$form->semid->setAttrib('readonly',true);
			$this->view->form=$form;
			if ($this->getRequest()->isPost()) {
				$frmdata=$this->getRequest()->getPost();
				if ($form->isValid($frmdata)) {
					unset($frmdata['send']);
					trim($frmdata['semid']);
                    trim($frmdata['name']);
                    $pk['eid']=$eid;
                    $pk['oid']=$oid; 
                    $pk['semid']=$semid;                  
                    $reg_= new Api_Model_DbTable_Semester();
                    // print_r($frmdata);
                    $reg_->_update($frmdata,$pk);
                    $this->_redirect("/soporte/semester/");
				}
				else
                {
                    echo "Ingrese nuevamente por favor";
                }
			}			
		} catch (Exception $e) {
			print "Error: Update Semester".$e->getMessage();
		}

	}

	public function deleteAction(){
		$eid=$this->eid;
		$oid=$this->oid;
		$semid=base64_decode($this->_getParam('semid'));
		$data['eid']=$eid;
		$data['oid']=$oid;
		$data['semid']=$semid;
		$reg_= new Api_Model_DbTable_Semester();
		$reg_->_delete($data);
		$this->_redirect("/soporte/semester/");
	}
}
