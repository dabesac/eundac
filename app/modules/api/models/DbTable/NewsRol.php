<?php

class Api_Model_DbTable_NewsRol extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_system_new_rol';
	protected $_primary = array('eid', 'oid', 'rid', 'newid');

	
	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_system_new_rol");
				else $select->from("base_system_new_rol",$attrib);
				foreach ($where as $atri=>$value){
					$select->where("$atri = ?", $value);
				}
				if ($orders<>null || $orders<>"") {
					if (is_array($orders))
						$select->order($orders);
				}
				$results = $select->query();
				$rows = $results->fetchAll();
				if ($rows) return $rows;
				return false;
		}catch (Exception $e){
			print "Error: Read Filter Course ".$e->getMessage();
		}
	}

	public function _getOne($where=null){
		try{
			
			if ($where['eid']=="" || $where['oid']=="" || $where['newid']=="") return false;
			
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and newid = '".$where['newid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One New ".$e->getMessage();
		}
	}

	public function _save($data){
		try{
			if ($data['eid']=="" || $data['oid']=="" || $data['rid']=="" || $data['newid']=="") return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
			print "Error al Guardar New and Rol ".$e->getMessage();
		}
	}

	public function _update($data,$pk){
		try {	
				if ($pk['eid']=="" || $pk['oid']=="" || $pk['rid']=="" || $pk['newid']=="") return false;
				$where = "eid = '".$pk['eid']."' and oid = '".$pk['oid']."' and newid = '".$pk['newid']."' and rid = '".$pk['rid']."'";
				return $this->update($data, $where);
				return false;
		} catch (Exception $e) {
			print "Error: Update Course".$e->getMessage();
		}
	}

	public function _delete($pk){
		try{
			if ($pk['oid']=='' || $pk['eid']=='' || $pk['rid']=='' || $pk['newid']=='') return false;
			$where = "eid = '".$pk['eid']."'and oid = '".$pk['oid']."' and rid = '".$pk['rid']."' and newid = '".$pk['newid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Organization ".$e->getMessage();
		}
	}
}