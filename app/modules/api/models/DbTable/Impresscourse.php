<?php

class Api_Model_DbTable_Impresscourse extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_impress_course';
	protected $_primary = array('eid', 'oid', 'perid', 'courseid', 'escid', 'subid', 'curid', 'turno');
	protected $_sequence ="s_impresscourse";

	public function _save($data){
		try{
			if ($data['eid']=='' || $data['oid']==''  || $data['perid']=='' || $data['courseid']=='' ||
			 $data['escid']=='' || $data['subid']=='' || $data['curid']=='' || $data['turno']=='' || 
			 $data['register']==''      ) return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save impress coursee ".$e->getMessage();
		}
	}
	
}