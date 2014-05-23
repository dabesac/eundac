<?php

class Api_Model_DbTable_Org extends Zend_Db_Table_Abstract
{
	protected $_name = 'org';
	protected $_primary = array("oid");

	public function _save($data){
		try{
			if ($data['oid']=='' || $data['name']=='' ) return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save Organization ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk){
		try{
			if ($pk['oid']=='' || $pk['eid']=='') return false;
			$where = "eid = '".$pk['eid']."'and oid = '".$pk['oid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Organization ".$e->getMessage();
		}
	}
	
	public function _delete($data){
		try{
			if ($data['oid']=='') return false;
			$where = "oid = '".$data['oid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Organization ".$e->getMessage();
		}
	}
	
	public function _getOne($where=array()){
		try{
			if ($where['eid']=="" || $where['oid']=="") return false;
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Organization ".$e->getMessage();
		}
	}
	
	public function _getAll($where=null,$order='',$start=0,$limit=0){
		print_r ($order);
		try{
			if ($where['eid']=='') return false; 
			$wherestr= "eid='".$where['eid']."'";
			if ($limit==0) $limit=null;
			if ($start==0) $start=null;
			$rows=$this->fetchAll($wherestr,$order,$start,$limit);
			if($rows) return $rows->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read All Organization ".$e->getMessage();
		}
	}
	
	public function _getFilter($where=null,$atrib=array()){
		try{
			if ($where['eid']=='' || $where['oid']=='') return false;
			$select = $this->select()->from('entity',$atrib);
			foreach ($where as $key => $value){
				$select->where('$key = ?', $value);
			}
			$rows = $this->fetchAll($select);
			if($rows) return $rows->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read Filter Organization ".$e->getMessage();
		}
	}
}
