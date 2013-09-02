<?php

class Distribution_Model_DbTable_Distribution extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_distribution';
	protected $_primary = array("eid","oid","distid","escid","subid","perid");

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']==''
				|| $data['distid']=='' || $data['perid']=='' ) return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save Distribution ".$e->getMessage();
		}
	}
	
	public function _update($data=null,$pk=null)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']=='' 
			|| $pk['distid']=='' || $pk['perid']=='' ) return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."'
					 and subid='".$pk['subid']."' and distid='".$pk['distid']."' and perid='".$pk['perid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Distribution".$e->getMessage();
		}
	}
	
	public function _delete($pk=null)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']==''
					|| $pk['distid']=='' || $pk['perid']=='' ) return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."'
					 and subid='".$pk['subid']."' and distid='".$pk['distid']."' and perid='".$pk['perid']."'";
			return $this->delete( $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Distribution".$e->getMessage();
		}
	}
	
	public function _getOne($pk=null)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']==''
			|| $pk['distid']=='' || $pk['perid']=='') return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."'
					 and subid='".$pk['subid']."' and distid='".$pk['distid']."' and perid='".$pk['perid']."'";
			$row = $this->fetchRow($where);
			if ($row) return $row->toArray();
			return false;
		}catch (Exception $ex){
			print "Error: Get Info Distribution ".$ex->getMessage();
		}
	}
	
	public function _getAll($pk=null)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']=='') return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."'";
			$row = $this->fetchAll($where);
			if ($row) return $row->toArray();
			return false;
		}catch (Exception $ex){
			print "Error: Get Info Distribution ".$ex->getMessage();
		}
	}
	
	public function _getFilter($where=null,$atrib=array()){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_distribution");
				else $select->from("base_distribution",$attrib);
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
			print "Error: Read Filter Distribution ".$e->getMessage();
		}
	}

}
