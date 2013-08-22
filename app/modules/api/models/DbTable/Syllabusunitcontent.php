<?php

class Api_Model_DbTable_Syllabusunitcontent extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_syllabus_units_content';
	protected $_primary = array("eid","oid","subid","perid","escid","curid","courseid","turno","unit","session");

	public function _save($data){
		try{
			if ($data['eid']=='' || $data['oid']=='' || $data['subid']=='' || $data['perid']=='' || $data['escid']=='' || $data['curid']=='' || $data['courseid']=='' || $data['turno']=='' || $data['unit']=='' || $data['session']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save SyllabusUnitsContent ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk){
		try{
			if ($pk['eid']=='' || $pk['oid']=='' || $pk['subid']=='' || $pk['perid']=='' || $pk['escid']=='' || $pk['curid']=='' || $pk['courseid']=='' || $pk['turno']=='' || $pk['unit']=='' || $pk['session']=='') return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and subid='".$pk['subid']."' and perid='".$pk['perid']."' and escid='".$pk['escid']."' and curid='".$pk['curid']."' and courseid='".$pk['courseid']."' and turno='".$pk['turno']."' and unit='".$pk['unit']."' and session='".$pk['session']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update SyllabusUnitsContent ".$e->getMessage();
		}
	}
	
	public function _delete($data){
		try{
			if ($data['eid']=='' || $data['oid']=='' || $data['subid']=='' || $data['perid']=='' || $data['escid']=='' || $data['curid']=='' || $data['courseid']=='' || $data['turno']=='' || $data['unit']=='' || $data['session']=='') return false;
			$where = "eid = '".$data['eid']."' and oid='".$data['oid']."' and subid='".$data['subid']."' and perid='".$data['perid']."' and escid='".$data['escid']."' and curid='".$data['curid']."' and courseid='".$data['courseid']."' and turno='".$data['turno']."' and unit='".$data['unit']."' and session='".$data['session']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete SyllabusUnitsContent ".$e->getMessage();
		}
	}
	
	public function _getOne($where=array()){
		try{
			if ($where['eid']=='' || $where['oid']=='' || $where['subid']=='' || $where['perid']=='' || $where['escid']=='' || $where['curid']=='' || $where['courseid']=='' || $where['turno']=='' || $where['unit']=='' || $where['session']=='') return false;
			$wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and subid='".$where['subid']."' and perid='".$where['perid']."' and escid='".$where['escid']."' and curid='".$where['curid']."' and courseid='".$where['courseid']."' and turno='".$where['turno']."' and unit='".$where['unit']."' and session='".$where['session']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One SyllabusUnitsContent ".$e->getMessage();
		}
	}

	public function _getAllXUnit($where=array()){
		try{
			if ($where['eid']=='' || $where['oid']=='' || $where['subid']=='' || $where['perid']=='' || $where['escid']=='' || $where['curid']=='' || $where['courseid']=='' || $where['turno']=='' || $where['unit']=='') return false;
			$wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and subid='".$where['subid']."' and perid='".$where['perid']."' and escid='".$where['escid']."' and curid='".$where['curid']."' and courseid='".$where['courseid']."' and turno='".$where['turno']."' and unit='".$where['unit']."'";
			$row = $this->fetchAll($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One SyllabusUnitsContent ".$e->getMessage();
		}
	}
}
