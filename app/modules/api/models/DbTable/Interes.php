<?php

class Api_Model_DbTable_Interes extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_interes';
	protected $_primary = array("iid","eid","pid");
	protected $_sequence ="s_interes";

	public function _getOne($where=null){
		try{
			
			if ($where['iid']=="" || $where['eid']=="" || $where['pid']=="") return false;
			
			$wherestr="iid = '".$where['iid']."' and eid = '".$where['eid']."' and pid = '".$where['pid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Interes ".$e->getMessage();
		}
	}

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['pid']=='' ) return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_interes");
				else $select->from("base_interes",$attrib);
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
			print "Error: Read Filter Intereses ".$e->getMessage();
		}
	}

	public function _save($data){
		try{
			if ($data['eid']=='' || $data['pid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
			print "Error al Guardar Interes ".$e->getMessage();
		}
	}


	public function _delete($pk){
		try{
			if ($pk['eid']=='' || $pk['pid']==''|| $pk['iid']=='') return false;
			$where = "eid = '".$pk['eid']."'and pid = '".$pk['pid']."'and iid = '".$pk["iid"]."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Interes ".$e->getMessage();
		}
	}
}