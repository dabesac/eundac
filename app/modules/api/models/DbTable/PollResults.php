<?php

class Api_Model_DbTable_PollResults extends Zend_Db_Table_Abstract
{
    protected $_name = 'base_poll_restuls';
    protected $_primary = array('eid', 'oid', 'code', 'qid', 'altid', 'escid', 'subid', 'uid', 'pid');
 
     public function _getFilter($where=null,$attrib=null,$orders=null){
        try{
            if($where['eid']=='' || $where['oid']=='') return false;
                $select = $this->_db->select();
                if ($attrib=='') $select->from("base_poll_restuls");
                else $select->from("base_poll_restuls",$attrib);
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
            print "Error: Read Filter Poll_alternative ".$e->getMessage();
        }
    }
    
}