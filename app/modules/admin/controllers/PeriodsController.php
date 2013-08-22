<?php
class Admin_PeriodsController extends Zend_Controller_Action{

	public function init(){
		$this->eid='20154605046';
		$this->oid='1';
	}

	public function indexAction(){

	}

	public function getperiodsAction(){
		try {
			$this->_helper->layout()->disableLayout();
        	$data['eid']= $this->eid;
        	$data['oid']= $this->oid;
         	$anio=$this->_getParam('anio');
         	$data['year']= substr($anio, 2, 3);
         	// print_r($data);
            $perio = new Api_Model_DbTable_Periods();
            $data=$perio->_getPeriodsxYears($data);
         	// print_r($lper); 
        	$this->view->data=$data; 
			
		} catch (Exception $e) {
			
		}
	}

	public function newAction(){
		try {
			$this->_helper->layout()->disableLayout();
		} catch (Exception $e) {
			
		}
	}

	public function updateAction(){

	}

	public function deleteAction(){

	}
}