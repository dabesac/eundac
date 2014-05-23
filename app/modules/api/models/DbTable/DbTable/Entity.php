<?php

class Api_Model_DbTable_Entity extends Zend_Db_Table_Abstract
{
	protected $_name = 'entity';
	protected $_primary = array("eid");

	public function _save($data){
		try{
			if ($data['eid']=='' || $data['name']=='' ) return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save Entity ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk){
		try{
			if ($pk['eid']=='') return false;
			$where = "eid = '".$pk['eid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Entity ".$e->getMessage();
		}
	}
	
	public function _delete($data){
		try{
			if ($data['eid']=='') return false;
			$where = "eid = '".$data['eid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Entity ".$e->getMessage();
		}
	}
	
	public function _getOne($where=array()){
		try{
			if ($where['eid']=="") return false;
			$wherestr="eid = '".$where['eid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Entity ".$e->getMessage();
		}
	}
	
	public function _getAll($where=null,$order='',$start=0,$limit=0){
		try{
			if ($where['eid']=='') 
				$wherestr= null;
			else 
				$wherestr= "eid='".$where['eid']."'";
			if ($limit==0) $limit=null;
			if ($start==0) $start=null;
			
			$rows=$this->fetchAll($wherestr,$order,$start,$limit);
			if($rows) return $rows->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read All Entity ".$e->getMessage();
		}
	}
}
