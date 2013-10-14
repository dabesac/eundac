<?php

class Api_Model_DbTable_Tmpgeneratedcode extends Zend_Db_Table_Abstract
{
	protected $_name = 'tmp_generatedcode';
	protected $_primary = array("escid");

	public function _save($data){
		try{
			if ($data['escid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save Tmp ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk){
		try{
			if ($pk['escid']=='') return false;
			$where = "escid='".$pk['escid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Tmp ".$e->getMessage();
		}
	}
	
	public function _delete($data){
		try{
			if ($data['escid']=='') return false;
			$where = "escid='".$data['escid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Tmp ".$e->getMessage();
		}
	}
	
	public function _getOne($where=array()){
		try{
			if ($where['escid']=='') return false;
			$wherestr="escid='".$where['escid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Tmp ".$e->getMessage();
		}
	}

	public function _getAll(){
        try {
            $rows=$this->fetchAll();
            if($rows) return $rows->toArray();
            return false;

        } catch (Exception $e) {
            print "Error: Leer Tmp".$e->getMessage();           
        }
    }
}
