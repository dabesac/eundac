<?php

class Api_Model_DbTable_PersonDocument extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_person_document';
	protected $_primary = array('eid', 'pid', 'document_type');

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_person_document");
				else $select->from("base_person_document",$attrib);
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
			print "Error: Read Filter Documento por Persona ".$e->getMessage();
		}
	}

	public function _save($data){
		try {
				if ($data['eid']=='' || $data['pid']=='' || $data['document_type']=='' || $data['acumulado']=='') return false;
				return $this->insert($data);
				return false;			
		} catch (Exception $e) {
			print "Error: Save Course".$e->getMessage();
		}
	}

	public function _update($data,$pk){
		try {
				if ($pk['eid']=='' || $pk['pid']=='' || $pk['document_type']=='' ) return false;
				$where = "eid = '".$pk['eid']."' and pid='".$pk['pid']."' and document_type='".$pk['document_type']."'";
				return $this->update($data, $where);
				return false;
		} catch (Exception $e) {
			print "Error: Update Course".$e->getMessage();
		}
	}

}