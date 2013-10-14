<?php

class Api_Model_DbTable_Reportacademyc extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_syllabus';
	protected $_primary = array("eid","oid","escid","subid","perid","courseid","curid","turno");
	public function guardar($data){
		$this->insert($data);
	}
}