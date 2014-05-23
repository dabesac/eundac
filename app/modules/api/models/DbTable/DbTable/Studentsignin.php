<?php

class Api_Model_DbTable_Studentsignin extends Zend_Db_Table_Abstract
{
	protected $_name = 'addons_student_signin';
	protected $_primary = array("eid","oid","escid","subid","uid","pid");

	
	public function _getOne($where=array()){
		try{
			if ($where['eid']=="" || $where['oid']=="" || $where['escid']=="" || $where['subid']=="" || $where['uid']=="" || $where['pid']=="") return false;
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and escid = '".$where['escid']."' and subid='".$where['subid']."' and uid = '".$where['uid']."' and pid = '".$where['pid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One StudentSignin ".$e->getMessage();
		}
	}

	public function _save($data){
		try {	
				if ($data['eid']=='' || $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['uid']=='' || $data['pid']=='') return false;
				return $this->insert($data);
				return false;			
		} catch (Exception $e) {
			print "Error: Save Course".$e->getMessage();
		}
	}

	public function _update($data,$pk){
		try {	
				if ($pk['eid']=='' || $pk['oid']=='' || $pk['escid']=='' || $pk['subid']=='' || $pk['uid']=='' || $pk['pid']=='') return false;
				$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."' and uid='".$pk['uid']."' and pid='".$pk['pid']."'";
				return $this->update($data, $where);
				return false;
		} catch (Exception $e) {
			print "Error: Update Course".$e->getMessage();
		}
	}
}
