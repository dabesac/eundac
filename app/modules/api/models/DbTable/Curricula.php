<?php

class Api_Model_DbTable_Curricula extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_curricula';
	protected $_primary = array("eid","oid","escid","subid","curid");

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['curid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: curricula ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk)
	{
		try{
			if ($pk['eid']=='' ||   $pk['oid']=='' ||  $pk['escid']=='' ||  $pk['curid']=='' || $pk['subid']=='') return false;
			$where = "eid = '".$pk['eid']."'  and oid = '".$pk['oid']."' and escid = '".$pk['escid']."' and curid = '".$pk['curid']."' and subid = '".$pk['subid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Curricula".$e->getMessage();
		}
	}
	
	public function _delete($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['curid']=='') return false;
			$where = 	"eid = '".$data['eid']."' and oid='".$data['oid']."' and escid='".$data['escid']."' and subid='".$data['subid']."' and curid='".$data['curid']."' ";			
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Curricula".$e->getMessage();
		}
	}

	
	public function _getOne($where=array()){
		try{
			if ($where['eid']=='' ||  $where['oid']=='' || $where['escid']=='' || $where['subid']=='' || $where['curid']=='') return false;
			$wherestr = "eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".$where['escid']."' and subid='".$where['subid']."' and curid='".$where['curid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Curricula ".$e->getMessage();
		}
	}

	
 	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_curricula");
				else $select->from("base_curricula",$attrib);
				foreach ($where as $atri=>$value){
					$select->where("$atri = ?", $value);
				}
				foreach ($orders as $key => $order) {
						$select->order($order);
				}
				$results = $select->query();
				$rows = $results->fetchAll();
				if ($rows) return $rows;
				return false;
		}catch (Exception $e){
			print "Error: Read Filter Curricula ".$e->getMessage();
		}
	}

	public function _getCourses($where=null,$attrib=null){
		try {
			$base_courses = new Api_Model_DbTable_Course();
			if($where=='' && $attrib=='') return false;
			 $data_courses = $base_courses->_getFilter($where,$attrib);
			 if ($data_courses) return $data_courses;
			 return false;
		} catch (Exception $e) {
			print "Error: Read Course ";
		}
	}
}
