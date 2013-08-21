<?php

class Api_Model_DbTable_Registrationxcourse extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_registration_course';
	protected $_primary = array("eid","oid","escid","subid","courseid","curid" ,"perid","turno","regid","pid","uid");

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['courseid']=='' || $data['curid']=='' || $data['regid']=='' || $data['turno']=='' || $data['pid']=='' || $data['uid']=='' || $data['perid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Registration ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']=='' || $pk['courseid']=='' || $pk['curid']=='' || $pk['regid']=='' || $pk['turno']=='' || $pk['pid']=='' || $pk['uid']=='' || $pk['perid']=='') return false;
			$where = "eid = '".$pk['eid']."' and pid='".$pk['pid']."' and oid = '".$pk['oid']."' and escid = '".$pk['escid']."' and uid = '".$pk['uid']."' and subid = '".$pk['subid']."' and regid = '".$pk['regid']."' and perid = '".$pk['perid']."' and turno = '".$pk['turno']."' and curid = '".$pk['curid']."' and courseid = '".$pk['courseid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Registration".$e->getMessage();
		}
	}
	
	public function _delete($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['courseid']=='' || $data['curid']=='' || $data['regid']=='' || $data['turno']=='' || $data['pid']=='' || $data['uid']=='' || $data['perid']=='') return false;
			$where = "eid = '".$data['eid']."' and pid='".$data['pid']."' and oid = '".$data['oid']."' and escid = '".$data['escid']."' and uid = '".$data['uid']."' and subid = '".$data['subid']."' and regid = '".$data['regid']."' and perid = '".$data['perid']."' and turno = '".$data['turno']."' and curid = '".$data['curid']."' and courseid = '".$data['courseid']."'";			
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Registration".$e->getMessage();
		}
	}

	
	public function _getOne($where=array()){
		try{
			if ($where['eid']=='' ||  $where['oid']=='' || $where['escid']=='' || $where['subid']=='' || $where['courseid']=='' || $where['curid']=='' || $where['regid']=='' || $where['turno']=='' || $where['pid']=='' || $where['uid']=='' || $where['perid']=='') return false;
			$wherestr = "eid = '".$where['eid']."' and pid='".$where['pid']."' and oid = '".$where['oid']."' and escid = '".$where['escid']."' and uid = '".$where['uid']."' and subid = '".$where['subid']."' and perid = '".$where['perid']."' and turno = '".$where['turno']."' and curid = '".$where['curid']."' and courseid = '".$where['courseid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Registration".$e->getMessage();
		}
	}

	
 public function _getFilter($where=array()){
		try{
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and escid = '".$where['escid']."' and subid='".$where['subid']."'";
			$row = $this->fetchAll($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read Filter Registration".$e->getMessage();
		}
	}

	public function _getStudentXcoursesXescidXperiods($where=null)
	{
		try{
			if ($where['eid']=='' || $where['oid']=='' || $where['perid']=='' || $where['curid']=="" || $where['escid']=="" || $where['courseid']=='' || $where['turno']=='') return false;
			$select = $this->_db->select()
			->from(array('p' => 'base_person'),array('p.last_name0','p.last_name1','p.first_name'))
				->join(array('rc' => 'base_registration_course'),'rc.pid=p.pid and p.eid=rc.eid', array('rc.*'))
				->where('rc.eid = ?', $where['eid'])->where('rc.oid = ?', $where['oid'])
				->where('rc.subid = ?', $where['subid'])->where('rc.escid = ?', $where['escid'])
				->where('rc.curid = ?', $where['curid'])->where('rc.perid = ?', $where['perid'])
				->where('rc.courseid = ?', $where['courseid'])->where('rc.turno = ?', $where['turno'])
				->where('rc.state = ?','M')->orwhere('rc.state = ?','C')
				->order('p.last_name0');
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;
		}catch (Exception $e){
			print "Error: Read UserInfo ".$e->getMessage();
		}
	}
}
