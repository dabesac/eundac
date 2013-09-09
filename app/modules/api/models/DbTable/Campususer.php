<?php

class Api_Model_DbTable_Campususer extends Zend_Db_Table_Abstract
{
	protected $_name = 'user';
	protected $_primary = array("user_id");
	protected function _setup() {
	    $this->_db = Zend_Registry::get('db2');
	    parent::_setup();
	}
	
	public function _update($where=null,$data){
		if ($data['password']=="" || $where['username']=="") return false;
		$str = "username='".$where['username']."'";
		if ($this->update($where, $data)) return true;
		return false;
	}
}