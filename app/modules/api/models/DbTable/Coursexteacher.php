<?php

class Api_Model_DbTable_Coursexteacher extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_course_x_teacher';
	protected $_primary = array("eid","oid","escid","subid","courseid","curid","turno","perid","uid","pid");


	public function _save($data){
		try {
			if($data["eid"]=='' || $data["oid"]=='' ||  $data["escid"]=='' ||  $data["subid"] =='' || $data["courseid"] =='' || $data["curid"] =='' 
				| $data["turno"]=='' || $data["perid"]=='' || $data["uid"]=='' || $data["pid"]=='')
				return false;
				return $this->insert($data);
				return false;
		} catch (Exception $e) {
			print "Error: Save cours ".$e->getMessage();
		}
	}


	public function _update($data,$pk){
		try{
			if ($pk["eid"]=='' || $pk["oid"]=='' ||  $pk["escid"]=='' ||  $pk["subid"] =='' || $pk["courseid"]=='' || $pk["curid"] =='' || $pk["turno"] =='' || $pk["perid"]=='' || $pk["uid"]=='' || $pk["pid"]=='') return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."' and courseid='".$pk['courseid']."' and curid='".$pk['curid']."' and turno='".$pk['turno']."' and perid='".$pk['perid']."' and uid='".$pk['uid']."' and pid='".$pk['pid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Organization ".$e->getMessage();
		}
	}

	public function _getOne($where=array()){
		try{
			if ($where["eid"]=='' || $where["oid"]=='' ||  $where["escid"]=='' ||  $where["subid"] =='' || $where["courseid"]=='' || $where["curid"] =='' || $where["turno"] =='' || $where["perid"]=='' || $where["uid"]=='' || $where["pid"]=='') return false;
			$wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".$where['escid']."' and subid='".$where['subid']."' and courseid='".$where['courseid']."' and curid='".$where['curid']."' and turno='".$where['turno']."' and perid='".$where['perid']."' and uid='".$where['uid']."' and pid='".$where['pid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Course ".$e->getMessage();
		}
	}

}