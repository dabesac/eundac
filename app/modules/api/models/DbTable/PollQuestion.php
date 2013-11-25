<?php

class Api_Model_DbTable_PollQuestion extends Zend_Db_Table_Abstract
{
    protected $_name = 'base_poll_question';
    protected $_primary = array('eid', 'oid', 'qid');
    protected $_sequence ="s_question";
 
    public function _save($data)
    {
        try{
            if ($data['eid']=='' || $data['oid']=='' ||  $data['pollid']=='' || $data['question']=='' || $data['type']=='' || $data['state']=='') return false;
            return $this->insert($data);
        }catch (Exception $e){
            print "Error: save PollQuestion ".$e->getMessage();
        }
    }

    public function _update($data,$pk)
    {
        try{
            if ($pk['eid']=='' || $pk['oid']=='' || $pk['qid']=='') return false;
            $where = "eid = '".$pk['eid']."' and oid = '".$pk['oid']."' and qid = '".$pk['qid']."'";
            return $this->update($data, $where);
        }catch (Exception $e){
            print "Error: Update PollQuestion ".$e->getMessage();
        }
    }

    public function _getOne($data=null){
            try{               
                if ($data['eid']=='' || $data['oid']=='' || $data['qid']=='') return false;
                $str ="eid='".$data['eid']."' and oid='".$data['oid']."' and qid='".$data['qid']."'";
                $row = $this->fetchRow($str);
            if($row) return $row->toArray();
            }  catch (Exception $ex){
                print "Error Leer Pregunta ".$ex->getMessage();
            }
    }


    public function _getPreguntasXencuesta($data=null,$order='', $start=0, $limit=0){
        try{
           // if ($data['eid'] == "" || $data['oid'] =="" || $data['pollid'] == "") return false;
            $str="eid='".$data['eid']."' and oid='".$data['oid']."' and pollid='".$data['pollid']."'";
            
            $row = $this->fetchAll($str,$order,$start,$limit);

            if ($row ) return $row->toArray();
           
        }catch (Exception $ex){
            print "Error: Lecturando las preguntas por encuestas".$ex->getMessage();
        }

    } 


     public function _getEncuestaActiva($data=null){
        try{
            if ($data['eid']=="" || $data['oid'] =="") return false;
            $str="eid='".$data['eid']."' and  oid='".$data['oid']."' and state='A'";
            $row=$this->fetchRow($str);
            if($row) return $row->toArray();            

        }  catch (Exception $ex){
            print "Error: Leer encuesta activa. ".$ex->getMessage();
        }
    }

    public function _getFilter($where=null,$attrib=null,$orders=null){
        try{
            if($where['eid']=='' || $where['oid']=='') return false;
                $select = $this->_db->select();
                if ($attrib=='') $select->from("base_poll_question");
                else $select->from("base_poll_question",$attrib);
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
            print "Error: Read Filter Poll_question ".$e->getMessage();
        }
    }
}
