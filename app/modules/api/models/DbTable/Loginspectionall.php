<?php

class Api_Model_DbTable_Loginspectionall extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_log_inspection_all';
	protected $_sequence ="s_inspection_all";
	protected $_primary = array("liaid");
	 
	
	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' ||  $data['escid']=='' || $data['subid']=='' || $data['perid']=='' || $data['document_type']=='' || $data['register']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){	
				print "Error: Save base_log_inspection_all ".$e->getMessage();
		}
	}
}