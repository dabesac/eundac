<?php
class Api_Model_DbTable_LangTasa extends Zend_Db_Table_Abstract{

	protected $_name = 'lang_rate';
	protected $_primary = array('eid', 'oid', 'rate_id');
	protected $_sequence = 's_lang_rate';

   	public function _getAll($eid){
      	try{
  			$f=$this->fetchAll("eid='$eid'");
          	if($f) return $f->toArray(); 
          	return false;
      	}catch (Exception $e){
          	print "Error: to show Rates (Module)".$e->getMessage();
      	}
  	}

    public function _getOne($where=array()){
		try{
			if (!$where['eid'] || !$where['oid'] || !$where['rate_id']) return false;

			$wherestr = "eid = '".$where['eid'].
						"' and oid ='".$where['oid'].
						"' and rate_id ='".$where['rate_id']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Rate ".$e->getMessage();
		}
	}

  	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
	        if(!$where['eid'] || !$where['oid']) return false;

			$select = $this->_db->select();
			if ($attrib=='') $select->from("lang_rate");
			else $select->from("lang_rate",$attrib);

			foreach ($where as $atri=>$value){
				$select->where("$atri = ?", $value);
			}
			if($orders){
				foreach ($orders as $key => $order) {
			  		$select->order($order);
				} 
			}

			$results = $select->query();
			$rows = $results->fetchAll();
			if ($rows) return $rows;
			return false;
	    }catch (Exception $e){
	    	print "Error: Read Filter Rates ".$e->getMessage();
	    }
    }


   public function _save($data){
		try {
			if (!$data['eid'] || !$data['oid']) return false;
			return $this->insert($data);
			return false;			
		} catch (Exception $e) {
			print "Error: Save Course ".$e->getMessage();
		}
	}

	public function _update($pk, $data){
		try {
				if (!$pk['eid'] || !$pk['oid'] || !$pk['rate_id']) return false;
				$where = "eid = '".$pk['eid'].
							"' and oid='".$pk['oid'].
							"' and rate_id='".$pk['rate_id']."'";
				return $this->update($data, $where);
				return false;
		} catch (Exception $e) {
			print "Error: Update Rate".$e->getMessage();
		}
	}


	public function _delete($pk){
		try{
			if ($pk['eid']=='' || $pk['oid']=='' || $pk['rate_id']=='') return false;
			$where = "eid = '".$pk['eid']."'and oid = '".$pk['oid']."'and rate_id = '".$pk['rate_id']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Tasa ".$e->getMessage();
		}
	}

}