<?php

class Api_Model_DbTable_Logs extends Zend_Db_Table_Abstract
{
	protected $_name = 'logaccess';
	protected $_primary = array("tokenid");

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' ||  $data['tokenid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save LogAccess ".$e->getMessage();
		}
	}

	public function _update($where, $data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' ||  $data['tokenid']=='') return false;
			$sql="eid='".$data['eid']."' and oid='".$data['oid']."' and tokenid='".$data['tokenid']."'";
			return $this->update($where,$sql);
			return false;
		}catch (Exception $e){
			print "Error: Save LogAccess ".$e->getMessage();
		}
	}
	
	public function _getOne($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' ||  $data['tokenid']=='') return false;
			$sql="eid='".$data['eid']."' and oid='".$data['oid']."' and tokenid='".$data['tokenid']."'";
			$row=$this->fetchRow($sql);
			if ($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Save LogAccess ".$e->getMessage();
		}
			
	}
	
	public function _getFilter($where=null,$attrib=null)
	{
		try{
			if ($where['eid']=='' ||  $where['oid']=='' ) return false;
			print_r($where);
			$select = $this->_db->select();
			if ($attrib=='') $select->from("logaccess");
			else $select->from("logaccess",$attrib);
			foreach ($where as $atri=>$value){
				$select->where("$atri = ?", $value);
			}
			$results = $select->query();
			$rows = $results->fetchAll();
			if ($rows) return $rows;
			return false;			
		}catch (Exception $e ){
			print "Error Get Filter ".$e->getMessage();
		}
	}
	
	public function _getConnect($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' ||  $data['uid']=='') return false;
			$sql="eid='".$data['eid']."' and oid='".$data['oid']."' and uid='".$data['uid']."' and state='A'";
			$row=$this->fetchAll($sql);
			if ($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Save LogAccess ".$e->getMessage();
		}
			
	}
}
