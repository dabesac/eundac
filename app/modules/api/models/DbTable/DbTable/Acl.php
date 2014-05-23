<?php

class Api_Model_DbTable_Acl extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_acl';
	protected $_primary = array("eid","oid","reid","rid","mid");

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='' ) return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_acl");
				else $select->from("base_acl",$attrib);
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
			print "Error: Read Filter Acl's ".$e->getMessage();
		}
	}

	public function _getinfoResource($where=null,$attrib=null,$order=null){
		try {
			if ($where=='' && $attrib=='' ) return false;
				$base_resource = new Api_Model_DbTable_Resource();
				$data_resource = $base_resource->_getFilter($where,$attrib,$order);
			if($data_resource) return $data_resource;
			return false;
		} catch (Exception $e) {
			print "Error: Read info Resource ";

		}
	}

	public function _save($data){
		try{
			if ($data['eid']=='' || $data['oid']=='' || $data['reid']=='' || $data['rid']=='' || $data['mid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
			print "Error al Guardar ACL Datos Repetidos".$e->getMessage();
		}
	}

	public function _delete($pk){
		try{
			if ($pk['oid']=='' || $pk['eid']=='' || $pk['reid']=='' || $pk['rid']=='' || $pk['mid']=='') return false;
			$where = "eid = '".$pk['eid']."'and oid = '".$pk['oid']."' and reid = '".$pk['reid']."' 
					and rid = '".$pk['rid']."' and mid = '".$pk['mid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Acl ".$e->getMessage();
		}
	}

	public function _getACL($where=null){
		try{
			if ($where['eid']=="" || $where['oid']=="" || $where['rid']=="" ) return false;
			$select = $this->_db->select()
			->from(array('r' => 'base_resource'),array('r.name','r.mid','r.controller','r.imgicon'))
			->join(array('a' => 'base_acl'),"a.eid= r.eid and a.oid=r.oid and a.reid=r.reid and a.mid=r.mid and a.state= 'A'")
				->where('a.eid = ?', $where['eid'])->where('a.oid = ?', $where['oid'])
				->where('a.rid = ?', $where['rid'])
				->order('a.mid');
			$results = $select->query();
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;
		}catch (Exception $ex){
			print "Error: Load ACL ".$ex->getMessage();
		}
	}

	public function _getAclRol($where){
		try {
			if ($where['eid']=="" || $where['oid']=="" || $where['mid'] == '' || $where['reid'] == '' ) return false;
			$select = $this->_db->select()
			->from(array('r' => 'base_rol'),array('r.name'))
			->joinleft(array('a' => 'base_acl'),"a.eid= r.eid and a.oid=r.oid and a.rid=r.rid ")
				->where('a.eid = ?', $where['eid'])
				->where('a.oid = ?', $where['oid'])
				->where('a.mid = ?', $where['mid'])
				->where('a.reid = ?', $where['reid'])
				->where('r.state = ?', 'A');
				
			$results = $select->query();
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;
		} catch (Exception $e) {
			echo "error in _getAclRol";
		}
	}

	public function _update($data,$pk){
		try{
			if ($pk['oid']=='' || $pk['eid']=='' || $pk['reid']=='' || $pk['mid']=='' || $pk['rid']=='' ) return false;
			$where = "eid = '".$pk['eid']."'and oid = '".$pk['oid']."' and reid = '".$pk['reid']."'
			  and mid = '".$pk['mid']."'  and rid = '".$pk['rid']."' ";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update acl ".$e->getMessage();
		}
	}

	/*public function _getOne($where=null){
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






	
	


	*/

}