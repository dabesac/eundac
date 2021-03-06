<?php

class Api_Model_DbTable_Studentxcurricula extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_student_curricula';
	protected $_primary = array("eid","oid","escid","subid","curid","uid","pid");

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['curid']=='' || $data['uid']=='' || $data['pid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save User ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk)
	{
		try{
			if ($pk['eid']=='' ||  $pk['pid']=='' ||  $pk['oid']=='' ||  $pk['escid']=='' ||  $pk['uid']=='' || $pk['subid']=='') return false;
			$where = "eid = '".$pk['eid']."' and pid='".$pk['pid']."' and oid = '".$pk['oid']."' and escid = '".$pk['escid']."' and uid = '".$pk['uid']."' and subid = '".$pk['subid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update User".$e->getMessage();
		}
	}
	
	public function _delete($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['curid']=='' || $data['uid']=='' || $data['pid']=='') return false;
			$where = 	"eid = '".$data['eid']."' and oid='".$data['oid']."' and escid='".$data['escid']."' and subid='".$data['subid']."' and curid='".$data['curid']."' and uid='".$data['uid']."' and pid='".$data['pid']."'";			
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete User".$e->getMessage();
		}
	}

	
	public function _getOne($where=array()){
		try{
			if ($where['eid']=='' ||  $where['oid']=='' || $where['escid']=='' || $where['subid']=='' ||  $where['uid']=='' || $where['pid']=='') return false;
			$wherestr = "eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".$where['escid']."' and subid='".$where['subid']."' and state='A' and uid='".$where['uid']."' and pid='".$where['pid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Entity ".$e->getMessage();
		}
	}

	public function _getsearch($where=array()){
		try{
			if ($where['eid']=='' ||  $where['oid']=='' || $where['escid']=='' || $where['subid']=='' ||  $where['uid']=='' || $where['pid']=='') return false;
			$wherestr = "eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".$where['escid']."' and subid='".$where['subid']."' and uid='".$where['uid']."' and pid='".$where['pid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Entity ".$e->getMessage();
		}
	}

	
 	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if(!$where['eid'] || !$where['oid']) return false;
			
			$select = $this->_db->select();
			if ($attrib=='') $select->from("base_student_curricula");
			else $select->from("base_student_curricula",$attrib);
			foreach ($where as $atri=>$value){
                $select->where("$atri = ?", $value);
            }
            if ($orders<>null || $orders<>"") {
                if (is_array($orders))
                    $select->order($orders);
            }
			$results = $select->query();
			$rows = $results->fetchAll();
			if ($rows) return $rows;
			return false;
		}catch (Exception $e){
			print "Error: Read Filter Curricula por Student ".$e->getMessage();
		}
	}
}
