<?php

class Api_Model_DbTable_Infoteacher extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_user_infoteacher';
	protected $_primary = array("eid","oid","uid","pid","escid","subid");

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['pid']=='' || $data['uid']=='' ) return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save User ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']=='' || $pk['uid']=='' || $pk['pid']=='') return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."' 
						and uid='".$pk['subid']."' and pid='".$pk['pid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update User".$e->getMessage();
		}
	}
	
	public function _getOne($pk=null)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']=='' || $pk['uid']=='' || $pk['pid']=='') return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."'
						and uid='".$pk['uid']."' and pid='".$pk['pid']."'";
			$row = $this->fetchRow($where);
			if ($row) return $row->toArray();
			return false;
		}catch (Exception $ex){
			print "Error: Get Info Teacher ".$ex->getMessage();
		}
	}
	public function _getPrincipal($pk=null)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']==''|| $pk['is_director']=='') return false;
			$where = "eid='".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."' and is_director='".$pk['is_director']."'";
			$row = $this->fetchRow($where);
			if ($row) return $row->toArray();
			return false;
		}catch (Exception $ex){
			print "Error: Get Info Teacher ".$ex->getMessage();
		}
	}

	public function _getDirectores($data){
		try{
			if($data['eid']=='' || $data['oid']=='')return false;
			$sql=$this->_db->query("
				select uid, pid, escid, subid from base_user_infoteacher
				where eid='".$data['eid']."' and oid='".$data['oid']."' and is_director='S' and pid<>'TEMP01' order by escid
				");
			$row=$sql->fetchall();
			return $row;
		}catch (Exception $ex){
			print 'Error: Get Info Teacher '.$ex->getMessage();
		}
	}

}
