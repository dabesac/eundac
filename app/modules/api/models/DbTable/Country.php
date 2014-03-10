<?php
class Api_Model_DbTable_Country extends Zend_Db_Table_Abstract{

	protected $_name='base_country';
	protected $_primary=array('coid');

	public function _getOne($where=array()){
		try{
			if ($where['coid']=="") return false;
			$wherestr="coid = '".$where['coid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Country ".$e->getMessage();
		}
	}

	public function _getAll(){
		try{
			$order=array('name_c');
			$rows=$this->fetchAll($where = null, $order);
			if($rows) return $rows->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read All Course ".$e->getMessage();
		}
	}
}