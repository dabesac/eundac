<?php

class Api_Model_DbTable_Jobs extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_jobs';
	protected $_primary = array("lid", "eid","pid");

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['pid']=='' ) return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_jobs");
				else $select->from("base_jobs",$attrib);
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