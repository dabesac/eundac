<?php

class Api_Model_DbTable_Campuscourse extends Zend_Db_Table_Abstract
{
	protected $_name = 'course';
	protected $_primary = array("id");
	
	
	
	protected function _setupDatabaseAdapter() {
		$this->_db = Zend_Registry::get('Adaptador2'); 
		parent::_setupDatabaseAdapter();
	}
	
	public function _save($data=null){
		try{
			if ($data['code']=="" || $data['registration_code']=="") return false;
			if ($this->insert($data)) {
				return true;
			}
			return false;
		} catch (Exception $e) {
			print "Error: Save Campus Course ".$e->getMessage();
		}
	}
}