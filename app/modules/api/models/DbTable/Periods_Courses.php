<?php

class Api_Model_DbTable_Periods_Courses extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_periods_courses';
	protected $_primary = array("eid", "oid", "perid", "courseid", "escid", "subid", "curid", "turno");

	public function _save($data){
		try {
			if($data["eid"]=='' || $data["oid"]=='' ||  $data["perid"]=='' ||  $data["courseid"] =='' $data["escid"]=='' || $data["subid"] =='' || $data["curid"] =='' || $data["turno"]=='')
				return false;
				return $this->insert($data);
				return false;
		} catch (Exception $e) {
			print "Error: Save Periods_Courses ".$e->getMessage();
		}
	}

	public function _update($data,$pk){
		try {
				if ($pk["eid"]=='' || $pk["oid"]=='' ||  $pk["perid"]=='' ||  $pk["courseid"] =='' $pk["escid"]=='' || $pk["subid"] =='' || $pk["curid"] =='' || $pk["turno"]=='') return false;
				$where="eid = '".$where['eid']."' and oid='".$where['oid']."' 
						and courseid='".$where['courseid']."' and escid='".$where['escid']."' 
						and perid='".$where['perid']."' and turno='".$where['turno']."' and subid='".$where['subid']."' and curid='".$where['curid']."' ";
				return $this->update($data, $where);
				return false;
		} catch (Exception $e) {
			print "Error: Update Periods_Courses".$e->getMessage();
		}
	}

	public function _getOne($where=array()){
		try{
			if ($where['eid']=="" || $where['oid']=="" || $where['courseid']=="" || $where['escid'] || $where['perid']==""  || $where['turno'] || $where['subid']=='' || $where['curid']=='') return false;
			$wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' 
						and courseid='".$where['courseid']."' and escid='".$where['escid']."' 
						and perid='".$where['perid']."' and turno='".$where['turno']."' and subid='".$where['subid']."' and curid='".$where['curid']."' ";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Periods_Courses ".$e->getMessage();
		}
	}

	
	public function _getAll($where=null,$order='',$start=0,$limit=0)
	{
		try {
			if ($where['eid']=="" || $where['oid']=="" || $where['escid'] || $where['perid']=="" || $where['subid']=='' || $where['curid']=='')	
				$wherestr=null;
			else 
				$wherestr= "eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".$where['escid']."' and perid='".$where['perid']."' and subid='".$where['subid']."' and curid='".$where['curid']."' ";
			if ($limit==0) $limit=null;
			if ($start==0) $start=null;
			
			$rows=$this->fetchAll($wherestr,$order,$start,$limit);
			if($rows) return $rows->toArray();
			return false;

		} catch (Exception $e) {
			print "Error: Read All Periods_Courses ".$e->getMessage();
		}
	}
	
}
