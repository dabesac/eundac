<?php 

class Api_Model_DbTable_HoursBeginClasses extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_hours_begin_classes';
	protected $_primary = array("hoursid","eid","oid","perid","escid","subid");
    protected $_sequence ="s_hoursbeginclasses";

    public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_hours_begin_classes");
				else $select->from("base_hours_begin_classes",$attrib);
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
			print "Error: Read Filter Hours Begin Classes".$e->getMessage();
		}
	}

	public function _save($data){
        try{
            if ($data['eid']==""||$data['oid']==""||$data['perid']==""||$data['escid']==""||$data['subid']==""||$data['hours_begin']=="") return false;
            return $this->insert($data);
            return false;
        }catch (Exception $ex){
            print "Error: Save data. ".$ex->getMessage();
        }
    }
}    