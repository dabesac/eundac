<?php

class Api_Model_DbTable_Academicrecord extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_academic_record';
	protected $_primary = array("acid","eid","pid");
	protected $_sequence ="s_academic";

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['pid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_academic_record");
				else $select->from("base_academic_record",$attrib);
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

	public function _getOne($where=array()){
		try{
			if ($where['acid']==""|| $where['eid']=='' || $where['pid']=='') return false;
			$wherestr="acid = '".$where['acid']."' and eid='".$where['eid']."' and pid='".$where['pid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Academic Data ".$e->getMessage();
		}
	}

	public function _save($data){
		try{
			if ($data['eid']=='' || $data['pid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
			print "Error al Guardar Datos Academicos ".$e->getMessage();
		}
	}

	public function _update($data,$pk){
		try{
			if ($pk['acid']=='' || $pk['eid']=='' || $pk['pid']=='') return false;
			$where = "acid = '".$pk['acid']."'and eid = '".$pk['eid']."' and pid = '".$pk['pid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Academic".$e->getMessage();
		}
	}	

	public function _delete($pk){
		try{
			if ($pk['eid']=='' || $pk['pid']==''|| $pk['acid']=='') return false;
			$where = "eid = '".$pk['eid']."'and pid = '".$pk['pid']."'and acid = '".$pk["acid"]."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Interes ".$e->getMessage();
		}
	}
}