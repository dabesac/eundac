<?php

class Api_Model_DbTable_Campususerxcourse extends Zend_Db_Table_Abstract
{
	protected $_name = 'course_rel_user';
	protected $_primary = array("course_code","user_id");
	
	
	
	protected function _setupDatabaseAdapter() {
		$this->_db = Zend_Registry::get('Adaptador2'); 
		parent::_setupDatabaseAdapter();
	}
	
	public function _save($data)
	{
		try{
			if ($data['user_id']=='' ||  $data['status']=='' || $data['course_code']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Registration ".$e->getMessage();
		}
	}
}