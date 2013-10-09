<?php

class Api_Model_DbTable_Traslatestudent extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_traslate_student';
	protected $_primary = array("eid","oid","escid","subid","pid","uid");

	public function _save($data){
		try{
			if ($data['eid']=='' || $data['oid']=='' || $data['subid']=='' || $data['escid']=='' || $data['uid']=='' || $data['pid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save Traslate ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk){
		try{
			if ($pk['eid']=='' || $pk['oid']=='' || $pk['subid']=='' || $pk['escid']=='' || $pk['uid']=='' || $pk['pid']=='') return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and subid='".$pk['subid']."' and escid='".$pk['escid']."' and uid='".$pk['uid']."' and pid='".$pk['pid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Traslate ".$e->getMessage();
		}
	}
	
	public function _delete($data){
		try{
			if ($data['eid']=='' || $data['oid']=='' || $data['subid']=='' || $data['escid']=='' || $data['uid']=='' || $data['pid']=='') return false;
			$where = "eid = '".$data['eid']."' and oid='".$data['oid']."' and subid='".$data['subid']."' and escid='".$data['escid']."' and uid='".$data['uid']."' and pid='".$data['pid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Traslate ".$e->getMessage();
		}
	}
	
	public function _getOne($where=array()){
		try{
			if ($where['eid']=='' || $where['oid']=='' || $where['subid']=='' || $where['escid']=='' || $where['uid']=='' || $where['pid']=='') return false;
			$wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and subid='".$where['subid']."' and escid='".$where['escid']."' and uid='".$where['uid']."' and pid='".$where['pid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Traslate ".$e->getMessage();
		}
	}
}
