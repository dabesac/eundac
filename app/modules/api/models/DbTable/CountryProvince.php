<?php
class Api_Model_DbTable_CountryProvince extends Zend_Db_Table_Abstract{

	protected $_name='base_country_province';
	protected $_primary=array('proid');

	public function _getOne($where=array()){
		try{
			if ($where['proid']=="") return false;
			$wherestr="proid = '".$where['proid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Country Province ".$e->getMessage();
		}
	}

	public function _getAll(){
		try{
			$row=$this->fetchAll();
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read All Country Province ".$e->getMessage();
		}
	}

	public function _getAllxState($where=null){
        try{
            if($where['cosid']=='')
                $wherestr=null;
            else
                $wherestr="cosid = '".$where['cosid']."'";
			
			$order=array('name_p');
            $rows=$this->fetchAll($wherestr,$order);
            if($rows) return $rows->toArray();
            return false;
        }catch (Exception $e){
            print "Error: Read All x State ".$e->getMessage();
        }
	}
}