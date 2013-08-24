<?php

class Api_Model_DbTable_Course extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_courses';
	protected $_primary = array('eid', 'oid', 'curid', 'escid', 'subid', 'courseid');


	public function _save($data){
		try {
				if ($data['eid']=='' || $data['oid']=='' || $data['curid']=='' || $data['escid']=='' || $data['subid']=='' || $data['courseid']=='' || $data['semid']) return false;
				return $this->insert($data);
				return false;			
		} catch (Exception $e) {
			print "Error: Save Course".$e->getMessage();
		}
	}

	public function _update($data,$pk){
		try {
				if ($pk['eid']=='' || $pk['oid']=='' || $pk['curid']=='' || $pk['escid']=='' || $pk['subid']=='' || $pk['courseid']=='') return false;
				$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and curid='".$pk['curid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."' and courseid='".$pk['courseid']."'";
				return $this->update($data, $where);
				return false;
		} catch (Exception $e) {
			print "Error: Update Course".$e->getMessage();
		}
	}

	public function _getOne($where=array()){
		try{
			if ($where['eid']==""|| $where['oid']=='' || $where['curid']=='' || $where['escid']=="" || $where['subid']=='' || $where['courseid']=='' ) return false;
			$wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and curid='".$where['curid']."' and escid='".$where['escid']."' and subid='".$where['subid']."' and courseid='".$where['courseid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Course ".$e->getMessage();
		}
	}

	public function _getAll($where=null,$order='',$start=0,$limit=0){
		try{
			if($where['eid']=='' || $where['oid']=='' || $where['curid']=='' || $where['escid']=='' || $where['subid']=='')
				$wherestr=null;
			else
				$wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and curid='".$where['curid']."' and escid='".$where['escid']."' and subid='".$where['subid']."'";
			if ($limit==0) $limit=null;
			if ($start==0) $start=null;
			
			$rows=$this->fetchAll($wherestr,$order,$start,$limit);
			if($rows) return $rows->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read All Course ".$e->getMessage();
		}
	}

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_courses");
				else $select->from("base_courses",$attrib);
				foreach ($where as $atri=>$value){
					$select->where("$atri = ?", $value);
				}
				if($orders){
					foreach ($orders as $key => $order) {
						$select->order($order);
					}
				}
				$results = $select->query();
				$rows = $results->fetchAll();
				if ($rows) return $rows;
				return false;
		}catch (Exception $e){
			print "Error: Read Filter Course ".$e->getMessage();
		}
	}
}
