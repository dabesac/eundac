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

}