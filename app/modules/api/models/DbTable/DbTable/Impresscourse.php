<?php

class Api_Model_DbTable_Impresscourse extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_impress_course';
	protected $_primary = array('eid', 'oid', 'perid', 'courseid', 'escid', 'subid', 'curid', 'turno');
	protected $_sequence ="s_impresscourse";

	public function _save($data){
		try{
			if ($data['eid']=='' || $data['oid']==''  || $data['perid']=='' || $data['courseid']=='' ||
			 $data['escid']=='' || $data['subid']=='' || $data['curid']=='' || $data['turno']=='' || 
			 $data['register']==''      ) return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save impress coursee ".$e->getMessage();
		}
	}

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_impress_course");
				else $select->from("base_impress_course",$attrib);
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
			print "Error: Read Filter Impression Course".$e->getMessage();
		}
	}
	
}