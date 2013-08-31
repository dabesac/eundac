<?php

class Api_Model_DbTable_Academicrecord extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_academic_record';
	protected $_primary = array("acid","eid","pid");

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['pid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_academic_record");
				else $select->from("base_academic_record",$attrib);
				foreach ($where as $atri=>$value){
					$select->where("$atri = ?", $value);
				}
				if($orders){
					foreach ($orders as $key => $order) {
						$select->order($order);
					}	
				}
				
				$results = $select->query();
				$rows = $results->fetchAll();
				if ($rows) return $rows;
				return false;
		}catch (Exception $e){
			print "Error: Read Filter Familiars ".$e->getMessage();
		}
	}
}