<?php

class Api_Model_DbTable_Speciality extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_speciality';
	protected $_primary = array("eid","oid","escid","subid");

	
	public function _getOne($where=array()){
		try{
			if ($where['eid']=="" || $where['oid']=="" || $where['escid']=="" || $where['subid']=="") return false;
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and escid = '".$where['escid']."' and subid='".$where['subid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Speciality ".$e->getMessage();
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
			print "Error: Read All Speciality ".$e->getMessage();
		}
	}

	public function _getFilter($where=array()){
		try{
			//print_r($where);
			$wherestr="eid='".$where['eid']."' and oid='".$where['oid']."' and facid= '".$where['facid']."' and subid= '".$where['subid']."' and state = '".$where['state']."'";
			$row = $this->fetchAll($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read Filter Faculty".$e->getMessage();
		}
	}

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' ) return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save Speciality ".$e->getMessage();
		}
	}

	public function _update($data,$pk)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']=='' ) return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Speciality".$e->getMessage();
		}
	}

	public function _delete($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' ) return false;
			$where = "eid = '".$data['eid']."' and oid='".$data['oid']."' and escid='".$data['escid']."' and subid='".$data['subid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Rol".$e->getMessage();
		}
	}
}
