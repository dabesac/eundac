<?php

class Api_Model_DbTable_Family extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_family';
	protected $_primary = array("eid","famid");

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['famid']=='' ) return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_family");
				else $select->from("base_family",$attrib);
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