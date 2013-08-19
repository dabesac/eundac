<?php

class Api_Model_DbTable_Curricula extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_condition';
	protected $_primary = array("cnid","pid","escid","uid","perid","eid","oid","subid");

	public function _getFilter($where=array())
	{
		try{
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and escid = '".$where['pid']."' and pid='".$where['subid']."'";
			$row = $this->fetchAll($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read Filter Curricula".$e->getMessage();
		}
	}

}