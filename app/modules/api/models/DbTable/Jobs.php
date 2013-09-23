<?php

class Api_Model_DbTable_Jobs extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_jobs';
	protected $_primary = array("lid", "eid","pid");
	protected $_sequence ="s_jobs";

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

	public function _save($data){
		try{
			if ($data['eid']=='' || $data['pid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
			print "Error al Guardar Jobs ".$e->getMessage();
		}
	}

	public function _delete($pk){
		try{
			if ($pk['eid']=='' || $pk['pid']==''|| $pk['lid']=='') return false;
			$where = "eid = '".$pk['eid']."'and pid = '".$pk['pid']."'and lid = '".$pk["lid"]."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Interes ".$e->getMessage();
		}
	}
}