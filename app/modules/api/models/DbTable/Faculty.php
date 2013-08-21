<?php

class Api_Model_DbTable_Faculty extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_faculty';
	protected $_primary = array("eid","oid","facid");

	
	public function _getOne($where=null){
		try{
			
			if ($where['eid']=="" || $where['oid']=="" || $where['facid']=="") return false;
			
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and facid = '".$where['facid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Faculty ".$e->getMessage();
		}
	}

	public function _getAll($where=null,$order='',$start=0,$limit=0){

		try {
			if($where['eid']=='')
				$wherestr= null;
			else
				$wherestr="eid='".$where['eid']."' and oid='".$where['oid']."'";
			if($limit==0) $limit=null;	
			if($start==0) $start=null;

			$rows=$this->fetchAll($wherestr,$order,$start,$limit);
			if($rows) return $rows->toArray();
			return false;

		} catch (Exception $e) {
			print "Error: Read All Faculty".$e->getMessage();			
		}
	}

	public function _save($data){
		try{
			if ($data['eid']=='' || $data['oid']=='' || $data['register']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
			print "Error al Guardar Facultad ".$e->getMessage();
		}
	}
	

	public function _update($data,$pk){
		try{
			if ($pk['oid']=='' || $pk['eid']=='' || $pk['facid']=='') return false;
			$where = "eid = '".$pk['eid']."'and oid = '".$pk['oid']."' and facid = '".$pk['facid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Organization ".$e->getMessage();
		}
	}
	
	
	public function _delete($pk){
		try{
			if ($pk['oid']=='' || $pk['eid']=='' || $pk['facid']=='') return false;
			$where = "eid = '".$pk['eid']."'and oid = '".$pk['oid']."' and facid = '".$pk['facid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Organization ".$e->getMessage();
		}
	}

}
