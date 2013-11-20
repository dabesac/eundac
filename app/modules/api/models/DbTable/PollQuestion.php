<?php

class Api_Model_DbTable_PollQuestion extends Zend_Db_Table_Abstract
{
    protected $_name = 'base_poll_question';
    protected $_primary = array('eid', 'oid', 'qid');
    protected $_sequence ="s_question";
 

    public function _getPreguntasXencuesta($data=null){
        try{
           // if ($data['eid'] == "" || $data['oid'] =="" || $data['pollid'] == "") return false;
            $str="eid='".$data['eid']."' and oid='".$data['oid']."' and pollid='".$data['pollid']."'";
            //,"orden asc"
            $row = $this->fetchAll($str);

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

}
