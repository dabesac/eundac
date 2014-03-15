<?php
class Api_Model_DbTable_CountryDistrict extends Zend_Db_Table_Abstract{

	protected $_name='base_country_district';
	protected $_primary=array('disid');

	public function _getOne($where=array()){
		try{
			if ($where['disid']=="") return false;
			$wherestr="disid = '".$where['disid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Country District ".$e->getMessage();
		}
	}

	public function _getAll(){
		try{
			$row=$this->fetchAll();
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read All Country District".$e->getMessage();
		}
	}

	public function _getAllxProvince($where=null){
        try{
            if($where['proid']=='')
                $wherestr=null;
            else
                $wherestr="proid = '".$where['proid']."'";
			
			$order=array('name_d');
            $rows=$this->fetchAll($wherestr,$order);                            
            if($rows) return $rows->toArray();
            return false;
        }catch (Exception $e){
            print "Error: Read All x Province ".$e->getMessage();
        }
	}

	public function _infoUbigeo($where=null){
		try {
			if ($where['disid']=='') return false;
			$sql=$this->_db->query("
			 	select * from base_country c
				inner join base_country_state s
					on c.coid=s.coid
				inner join base_country_province p
					on s.cosid=p.cosid
				inner join base_country_district d
					on p.proid=d.proid
				where d.disid='".$where['disid']."'
           	");
           
           	$row=$sql->fetchAll();
           	return $row;
			return false;	
		} catch (Exception $e) {
			print "Error: Info Ubigeo".$e->getMessage();
		}
	}
}	