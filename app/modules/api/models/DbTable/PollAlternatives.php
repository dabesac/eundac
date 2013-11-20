<?php

class Api_Model_DbTable_PollAlternatives extends Zend_Db_Table_Abstract
{
    protected $_name = 'base_poll_aternatives';
    protected $_primary = array('eid', 'oid', 'atlid', 'qid');
    protected $_sequence ="s_alternative";
 

    
    public function _getAll($data=null){
        try{
            if ($data['eid'] == "" || $data['oid'] =="" || $data['qid'] == "") return false;
            $str="eid='".$data['eid']."' and oid='".$data['oid']."' and qid='".$data['qid']."'";
            //,"orden asc"
            $row = $this->fetchAll($str);

            if ($row ) return $row->toArray();
           
        }catch (Exception $ex){
            print "Error: Lecturando las preguntas por encuestas".$ex->getMessage();
        }

    } 

}