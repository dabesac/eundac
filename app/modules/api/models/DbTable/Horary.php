<?php

class Api_Model_DbTable_Horary extends Zend_Db_Table_Abstract
{
	protected $_name = 'horary_periods';
	protected $_primary = array("eid","oid","hid","escid","subid","perid","courseid","curso","turno");

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("horary_periods");
				else $select->from("horary_periods",$attrib);
				foreach ($where as $atri=>$value){
					$select->where("$atri = ?", $value);
				}
				foreach ($orders as $key => $order) {
						$select->order($order);
				}
				$results = $select->query();
				$rows = $results->fetchAll();
				if ($rows) return $rows;
				return false;
		}catch (Exception $e){
			print "Error: Read Filter Curricula ".$e->getMessage();
		}
	}
 }
