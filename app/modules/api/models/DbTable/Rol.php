<?php

class Api_Model_DbTable_Rol extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_rol';
	protected $_primary = array("eid","oid","rid");

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['rid']=='' || $data['name']=='' ) return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save Rol ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['rid']=='' ) return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and rid='".$pk['rid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Rol".$e->getMessage();
		}
	}
	
	public function _delete($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['rid']=='') return false;
			$where = "eid = '".$data['eid']."' and oid='".$data['oid']."' and rid='".$data['rid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Rol".$e->getMessage();
		}
	}
	
	public function _getOne($where=array())
	{
		try{
			
			if ($where['eid']=="" || $where['oid']=="" || $where['rid']=="") return false;
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and rid = '".$where['rid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Rol".$e->getMessage();
		}
	}
	
	public function _getAll($where=null,$order='',$start=0,$limit=0)
	{
		try{
			if ($where['eid']=='' ||  $where['oid']=='') return false;
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."'";
			if ($limit==0) $limit=null;
			if ($start==0) $start=null;			
			$rows=$this->fetchAll($wherestr,$order,$start,$limit);
			if($rows) return $rows->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read All Rol ".$e->getMessage();
		}
	}
}
