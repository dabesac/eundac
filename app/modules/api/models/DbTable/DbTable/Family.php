<?php

class Api_Model_DbTable_Family extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_family';
	protected $_primary = array("eid","famid");
	protected $_sequence ="s_family";

	public function _save($data){
		try{
			if ($data['eid']=='') return false;
			$this->insert($data);
				$rowset= $this->fetchAll()->toArray();
                $rowCount = count($rowset);
                $var=$rowCount-1;
                return ($rowset[$var]['famid']);

		}catch (Exception $e){
			print "Error al Guardar Familia ".$e->getMessage();
		}
	}


	public function _update($data,$pk){
		try{
			if ($pk['eid']=='' || $pk['famid']=='') return false;
			$where = "eid = '".$pk['eid']."' and famid = '".$pk['famid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Family ".$e->getMessage();
		}
	}

	public function _delete($pk){
		try{
			if ($pk['eid']=='' || $pk['famid']=='') return false;
			$where = "eid = '".$pk['eid']."'and famid = '".$pk["famid"]."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Interes ".$e->getMessage();
		}
	}


	public function _getOne($where=null){
		try{
			
			if ($where['eid']=="" || $where['famid']=="") return false;
			
			$wherestr="eid = '".$where['eid']."' and famid = '".$where['famid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Interes ".$e->getMessage();
		}
	}


	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_family");
				else $select->from("base_family",$attrib);
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