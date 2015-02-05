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
				if ($orders<>null || $orders<>"") {
					if (is_array($orders))
						$select->order($orders);
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

    public function _countMemo($data){
    	try {
    		if ($data['eid']=='' || $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['type_impression']=='') return false;
    		$eid=$data['eid'];
    		$oid=$data['oid'];
    		$escid=$data['escid'];
    		$subid=$data['subid'];
    		$type=$data['type_impression'];
    		$sql=$this->_db->query("
    								select count(distinct uid) from base_count_impression
									where eid='$eid' and oid='$oid' and escid='$escid' and
									subid='$subid' and type_impression='$type'       			
             					   ");          
            return $sql->fetchAll();
            return false;
    	} catch (Exception $e) {
    		print "Error: count memo".$e->getMessage();
    	}
    }
}    