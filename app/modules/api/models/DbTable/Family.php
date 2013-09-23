<?php

class Api_Model_DbTable_Family extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_family';
	protected $_primary = array("eid","famid");
	protected $_sequence ="s_family";

	public function _save($data){
		try{
			if ($data['eid']=='') return false;
			$this->insert($data);
				$rowset= $this->fetchAll()->toArray();
                $rowCount = count($rowset);
                $var=$rowCount-1;
                return ($rowset[$var]['famid']);

		}catch (Exception $e){
			print "Error al Guardar Familia ".$e->getMessage();
		}
	}

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='') return false;
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