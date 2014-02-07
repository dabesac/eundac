<?php 

class Api_Model_DbTable_Countimpressionall extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_count_impression';
	protected $_primary = array("countid");
    protected $_sequence ="s_impression_all";

    public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_count_impression");
				else $select->from("base_count_impression",$attrib);
				foreach ($where as $atri=>$value){
					$select->where("$atri = ?", $value);
				}
				foreach ($orders as $key => $order) {
						$select->order($order);
				}
				$results = $select->query();
				$rows = $results->fetchAll();
				if ($rows) return $rows;
				return false;
		}catch (Exception $e){
			print "Error: Read Filter Count Impression".$e->getMessage();
		}
	}

	public function _save($data){
        try{
            if ($data['eid']=="" || $data['oid']=="" || $data['uid']=="" || $data['escid']=="" || $data['subid']=="" || $data['pid']=="" || $data['type_impression']=="" || $data['date_impression']=="") return false;
            return $this->insert($data);
            return false;
        }catch (Exception $ex){
            print "Error: Save data. ".$ex->getMessage();
        }
    }	
}    