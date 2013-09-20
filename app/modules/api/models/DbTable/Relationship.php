<?php

class Api_Model_DbTable_Relationship extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_relationship';
	protected $_primary = array("eid","pid","famid");

	public function _save($data){
		try{
			print_r($data);
			if ($data['eid']=='' || $data['pid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
			print "Error al Guardar Familia ".$e->getMessage();
		}
	}

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['pid']=='' ) return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_relationship");
				else $select->from("base_relationship",$attrib);
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

	public function _getInfoFamiliars($where=null,$attrib=null,$order=null){
		try {
			if ($where=='' && $attrib=='' ) return false;
				$base_family = new Api_Model_DbTable_Family();
				$data_family = $base_family ->_getFilter($where,$attrib,$order);
			if($data_family) return $data_family;
			return false;
		} catch (Exception $e) {
			print "Error: Read info Course ";
		}
	}
}