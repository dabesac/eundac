<?php

class Api_Model_DbTable_Statistics extends Zend_Db_Table_Abstract
{
	protected $_name = 'addons_student_statistics';
	protected $_primary = array("eid", "oid","pid","uid","escid","subid");

	public function _getOne($where=array()){
		try{
			if ($where['eid']=="" || $where['oid']=="" || $where['pid']=="" || $where['uid']=="" || $where['escid']=="" || $where['subid']=="") return false;
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."'"." and pid = '".$where['pid']."'"." and uid = '".$where['uid']."'"." and escid = '".$where['escid']."'"." and subid = '".$where['subid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Datos Estadisticos ".$e->getMessage();
		}
	}

	public function _save($data){
		try{
			if ($data['eid']=='' || $data['oid']=='' || $data['pid']=='' || $data['uid']=='' || $data['escid']=='' || $data['subid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
			print "Error al Guardar Estatistics ".$e->getMessage();
		}
	}

	public function _update($data,$pk){
		try{
			if ($pk['eid']=='' || $pk['oid']=='' || $pk['pid']=='' || $pk['uid']=='' || $pk['escid']=='' || $pk['subid']=='') return false;
			$where = "eid = '".$pk['eid']."' and oid = '".$pk['oid']."'"." and pid = '".$pk['pid']."'"." and uid = '".$pk['uid']."'"." and escid = '".$pk['escid']."'"." and subid = '".$pk['subid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Statistic ".$e->getMessage();
		}
	}

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='' ) return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("addons_student_statistics");
				else $select->from("addons_student_statistics",$attrib);
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
			print "Error: Read Filter Familiars ".$e->getMessage();
		}
	}

}