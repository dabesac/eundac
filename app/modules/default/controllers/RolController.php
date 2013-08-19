<?php

class RolController extends Zend_Controller_Action{

	public function init(){
		$this->eid='20154605046';
		$this->oid='1';
	}

	public function indexAction(){
		$eid=$this->eid;
		$oid=$this->oid;
		$rol=new Api_Model_DbTable_Rol();
		$where['eid']=$eid;
		$where['oid']=$oid;
		$data=$rol->_getAll($where);
		//print_r($data);
		$this->view->data=$data;	
	}

	public function _save(){

	}

	public function _update(){

	}

	public function _delete(){

	}
}
