<?php

class Api_Model_DbTable_News extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_system_news';
	protected $_primary = array("newid");
	protected $_sequence ="s_system_news";

	
	public function _getLastNews(){
		try{
            $sql=$this->_db->query("select * from base_system_news limit 20");
        	$r = $sql->fetchAll();
        	return $r;
		}catch (Exception $e){
			print "Error: Read Filter News ".$e->getMessage();
		}
	}

	public function _getAll($where=null,$order=null,$start=0,$limit=0){

		try {
			if($where['eid']=='')
				$wherestr= null;
			else
				$wherestr="eid='".$where['eid'];

			if ($orders<>null || $orders<>"") {
				if (is_array($orders))
					$select->order($orders);
			}

			if($limit==0) $limit=null;	
			if($start==0) $start=null;

			$rows=$this->fetchAll($wherestr,$order,$start,$limit);
			if($rows) return $rows->toArray();
			return false;

		} catch (Exception $e) {
			print "Error: Read All Faculty".$e->getMessage();			
		}
	}

	public function _getOne($where=null){
		try{
			
			if ($where['newid']=="") return false;
			
			$wherestr="newid = '".$where['newid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One New ".$e->getMessage();
		}
	}

	public function _save($data){
		try{
			return $this->insert($data);
			return false;
		}catch (Exception $e){
			print "Error al Guardar New	 ".$e->getMessage();
		}
	}

	public function _update($data,$pk){
		try {	
				if ($pk['newid']=='') return false;
				$where = "newid = '".$pk['newid']."'";
				return $this->update($data, $where);
				return false;
		} catch (Exception $e) {
			print "Error: Update Course".$e->getMessage();
		}
	}

	public function _delete($pk){
		try{
			if ($pk['newid']=='') return false;
			$where = "newid = '".$pk['newid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Organization ".$e->getMessage();
		}
	}
}