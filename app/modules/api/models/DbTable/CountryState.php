<?php
class Api_Model_DbTable_CountryState extends Zend_Db_Table_Abstract{

	protected $_name='base_country_state';
	protected $_primary=array('cosid');

	public function _getOne($where=array()){
		try{
			if ($where['cosid']=="") return false;
			$wherestr="cosid = '".$where['cosid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Country State ".$e->getMessage();
		}
	}

	public function _getAll(){
		try{
			$row=$this->fetchAll();
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read All Country State ".$e->getMessage();
		}
	}

	public function _getAllxCountry($where=null){
        try{
            if($where['coid']=='')
                $wherestr=null;
            else
                $wherestr="coid = '".$where['coid']."'";

            $order=array('name_s');
            $rows=$this->fetchAll($wherestr,$order);
            if($rows) return $rows->toArray();
            return false;
        }catch (Exception $e){
            print "Error: Read All x Country ".$e->getMessage();
        }
	}
}