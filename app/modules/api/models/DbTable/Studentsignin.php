<?php

class Api_Model_DbTable_Studentsignin extends Zend_Db_Table_Abstract
{
	protected $_name = 'addons_student_signin';
	protected $_primary = array("eid","oid","escid","subid","uid","pid");

	
	public function _getOne($where=array()){
		try{
			if ($where['eid']=="" || $where['oid']=="" || $where['escid']=="" || $where['subid']=="" || $where['uid']=="" || $where['pid']=="") return false;
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and escid = '".$where['escid']."' and subid='".$where['subid']."' and uid = '".$where['uid']."' and pid = '".$where['pid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One StudentSignin ".$e->getMessage();
		}
	}
}
