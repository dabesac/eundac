<?php

class Api_Model_DbTable_Module extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_module';
	protected $_primary = array("eid","oid","mid");

	public function _save($data){
		try{
			if ($data['eid']=='' || $data['oid']=='' || $data['name']=='' ) return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save Module ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk){
		try{
			if ($pk['oid']=='' || $pk['eid']=='' || $pk['mid']=='' ) return false;
			$where = "eid = '".$pk['eid']."' and oid = '".$pk['oid']."' and mid = '".$pk['mid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Module ".$e->getMessage();
		}
	}
	
	public function _delete($data=null){
		try{
			if ($data['oid']==''|| $data['eid']==''|| $data['mid']=='') return false;
			$where = "eid = '".$data['eid']."' and oid = '".$data['oid']."' and mid = '".$data['mid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Module ".$e->getMessage();
		}
	}
	
	public function _getOne($where=array()){
		try{
			if ($where['eid']=="" || $where['oid']=="" || $where['mid']=="" ) return false;
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."'  and mid = '".$where['mid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Module ".$e->getMessage();
		}
	}
	
	public function _getAll($where=null,$order='',$start=0,$limit=0){
		try{
			if($where['eid']=='' || $where['oid']=='' )
				$wherestr=null;
			else
				$wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."'";
			if ($limit==0) $limit=null;
			if ($start==0) $start=null;
				
			$rows=$this->fetchAll($wherestr,$order,$start,$limit);
			if($rows) return $rows->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read All Modules ".$e->getMessage();
		}
	}
}
