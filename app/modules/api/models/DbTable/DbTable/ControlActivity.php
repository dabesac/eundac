<?php

class Api_Model_DbTable_ControlActivity extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_syllabus_content_controller';
	protected $_primary = array("eid", "oid", "perid", "subid", "courseid", "curid", "turno", "unit", "session", "week");

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_syllabus_content_controller");
				else $select->from("base_syllabus_content_controller",$attrib);
				foreach ($where as $atri=>$value){
					$select->where("$atri = ?", $value);
				}
				if ($orders<>null || $orders<>"") {
					if (is_array($orders))
						$select->order($orders);
				}
				$results = $select->query();
				$rows = $results->fetchAll();
				if ($rows) return $rows;
				return false;
		}catch (Exception $e){
			print "Error: Read Filter Content of the fucking Syllabus ".$e->getMessage();
		}
	}

	public function _save($data){
		try{
			if ($data['eid']=='' || $data['oid']=='' || $data['perid']=='' || $data['subid']=='' || $data['courseid']=='' || $data['curid']=='' || $data['turno']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
			print "Error al Guardar Control Syllabus".$e->getMessage();
		}
	}
	/*Devuele la ultima sesion de clases guardada*/
	public function _getUltimateclass($where=null){
		try {
				$select = $this->_db->select();
				$select->from("base_syllabus_content_controller")
				   		->where("eid = ?",$where['eid'])->where("oid = ?",$where['oid'])
				   		->where("escid = ?",$where['escid'])->where("courseid = ?",$where['courseid'])
				   		->where("perid = ?",$where['perid'])->where("state = ?",$where['state'])
				   		->order(array('session DESC','week DESC'))
				   		->limit(1);
// "select * from base_syllabus_content_controller
// where escid='4SI' and perid='14A' and courseid='94302' and state='D'
// order by session DESC,week DESC limit 1"
				$results = $select->query();
				$rows = $results->fetchAll();
				if ($rows) return $rows;
				return false;
		} catch (Exception $e) {
			print "Error: Read Get Ultimate Class".$e->getMessage();
		}
	}
	
}