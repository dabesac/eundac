<?php

class Api_Model_DbTable_UserInfoTeacher extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_user_infoteacher';
	protected $_primary = array("eid","oid","uid","escid","subid","pid");

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['pid']=='' || $data['uid']=='' ) return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save UserInfo ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']=='' || $pk['pid']=='' || $pk['uid']=='') return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."' and pid='".$pk['pid']."' and uid='".$pk['uid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update UserInfo".$e->getMessage();
		}
	}
	
	public function _delete($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['pid']=='' || $data['uid']=='') return false;
			$where = "eid = '".$data['eid']."' and oid='".$data['oid']."' and escid='".$data['escid']."' and subid='".$data['subid']."'	and pid='".$data['pid']."' and uid='".$data['uid']."'";			
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete UserInfo".$e->getMessage();
		}
	}

	public function _getOne($where=array()){
		try{
			if ($where['eid']=='' ||  $where['oid']=='' || $where['escid']=='' || $where['subid']=='' || $where['pid']=='' || $where['uid']=='') return false;
			$wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".$where['escid']."' and subid='".$where['subid']."'	and pid='".$where['pid']."' and uid='".$where['uid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One UserInfo ".$e->getMessage();
		}
	}
}