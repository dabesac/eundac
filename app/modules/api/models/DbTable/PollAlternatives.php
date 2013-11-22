<?php

class Api_Model_DbTable_PollAlternatives extends Zend_Db_Table_Abstract
{
    protected $_name = 'base_poll_aternatives';
    protected $_primary = array('eid', 'oid', 'atlid', 'qid');
    protected $_sequence ="s_alternative";
 
    public function _save($data)
    {
        try{
            if ($data['eid']=='' || $data['oid']=='' ||  $data['qid']=='' || $data['alternative']=='') return false;
            return $this->insert($data);
        }catch (Exception $e){
            print "Error: save PollAlternative ".$e->getMessage();
        }
    }

    public function _update($data,$pk)
    {
        try{
            if ($pk['eid']=='' || $pk['oid']=='' || $pk['atlid']=='' || $pk['qid']=='') return false;
            $where = "eid = '".$pk['eid']."' and oid = '".$pk['oid']."' and atlid = '".$pk['atlid']."' and qid='".$pk['qid']."'";
            return $this->update($data, $where);
        }catch (Exception $e){
            print "Error: Update PollQuestion ".$e->getMessage();
        }
    }
    
    public function _delete($data)
    {
        try{
            if ($data['eid']=='' ||  $data['oid']=='' || $data['atlid']=='' || $data['qid']=='') return false;
            $where = "eid = '".$data['eid']."' and oid='".$data['oid']."' and atlid='".$data['atlid']."' and qid='".$data['qid']."'";
            return $this->delete($where);
            return false;
        }catch (Exception $e){
            print "Error: Delete Alternative".$e->getMessage();
        }
    }

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

    public function _getFilter($where=null,$attrib=null,$orders=null){
        try{
            if($where['eid']=='' || $where['oid']=='') return false;
                $select = $this->_db->select();
                if ($attrib=='') $select->from("base_poll_aternatives");
                else $select->from("base_poll_aternatives",$attrib);
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