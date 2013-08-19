<?php

class Api_Model_DbTable_Users extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_users';
	protected $_primary = array("eid","oid","uid","escid","subid","pid");

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['pid']=='' || $data['password']=='' ) return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save User ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']=='' || $pk['pid']=='' ) return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."' and pid='".$pk['pid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update User".$e->getMessage();
		}
	}
	
	public function _delete($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['pid']=='' ) return false;
			$where = 	"eid = '".$data['eid']."' and oid='".$data['oid']."' and escid='".$data['escid']."' and subid='".$data['subid']."'
						and pid='".$data['pid']."'";			
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete User".$e->getMessage();
		}
	}
	
	public function _getInfoUser($where=null)
	{
		try{
			if ($where['eid']=="" || $where['oid']=="" || $where['escid']=="" || $where['subid']=="" 
				|| $where['uid']=="" || $where['pid']=="") return false;
			
			$select = $this->_db->select()
			->from(array('p' => 'base_person'),array('p.pid','p.typedoc','numdoc','p.last_name0','p.last_name1','p.first_name','p.birthday','p.photografy'))
				->join(array('u' => 'base_users'),'u.eid= p.eid and u.pid=p.pid', 
						array('u.uid','u.uid'))
				->where('u.eid = ?', $where['eid'])->where('u.oid = ?', $where['oid'])
				->where('u.escid = ?', $where['escid'])->where('u.subid = ?', $where['subid'])
				->where('u.uid = ?', $where['uid'])->where('u.pid = ?', $where['pid'])
				->order('last_name0');
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;
		}catch (Exception $e){
			print "Error: Read UserInfo ".$e->getMessage();
		}
		
	}
}
