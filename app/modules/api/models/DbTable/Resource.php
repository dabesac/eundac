<?php

class Api_Model_DbTable_Resource extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_resource';
	protected $_primary = array("eid","oid","reid","mid");
	protected $_sequence ="s_resource";

	public function _getAll($where=null,$order='',$start=0,$limit=0){

		try {
			if($where['eid']=='' || $where['oid']=='')
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

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_resource");
				else $select->from("base_resource",$attrib);
				foreach ($where as $atri=>$value){
					$select->where("$atri = ?", $value);
				}
				if ($orders ){
					foreach ($orders as $key => $order) {
						$select->order($order);
					}	
				}
				
				$results = $select->query();
				$rows = $results->fetchAll();
				if ($rows) return $rows;
				return false;
		}catch (Exception $e){
			print "Error: Read Filter Acl's ".$e->getMessage();
		}
	}


	public function _save($data){
		try{
			if ($data['eid']=='' || $data['oid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
			print "Error al Guardar Recurso ".$e->getMessage();
		}
	}


	public function _getOne($where=null){
		try{
			if ($where['eid']=="" || $where['oid']=="" || $where['reid']=="") return false;
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and reid = '".$where['reid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Faculty ".$e->getMessage();
		}
	}


	
	public function _update($data,$pk){
		try{
			if ($pk['oid']=='' || $pk['eid']=='' || $pk['reid']=='') return false;
			$where = "eid = '".$pk['eid']."'and oid = '".$pk['oid']."' and reid = '".$pk['reid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Organization ".$e->getMessage();
		}
	}

	public function _delete($pk){
		try{
			if ($pk['oid']=='' || $pk['eid']=='' || $pk['reid']=='') return false;
			$where = "eid = '".$pk['eid']."'and oid = '".$pk['oid']."' and reid = '".$pk['reid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Organization ".$e->getMessage();
		}
	}

}