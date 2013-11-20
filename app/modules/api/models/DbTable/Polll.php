<?php

class Api_Model_DbTable_Polll extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_poll';
	protected $_primary = array('eid', 'oid', 'pollid');
    protected $_sequence ="s_poll";

    public function _save($data)
    {
        try{
            if ($data['eid']=='' || $data['oid']=='' || $data['pollid']=='' || $data['title']=='' ||
                $data['published']=='' || $data['closed']=='' || $data['is_all']=='') return false;
            
            return $this->insert($data);
            return false;
        }catch (Exception $e){
                print "Error: save Poll ".$e->getMessage();
        }
    }

    public function _update($data,$pk)
    {
        try{
            if ($pk['eid']=='' ||   $pk['oid']=='' ||  $pk['pollid']=='') return false;
            $where = "eid = '".$pk['eid']."' and pid='".$pk['pid']."' and oid = '".$pk['oid']."' and pollid = '".$pk['pollid']."'";
            return $this->update($data, $where);
            return false;
        }catch (Exception $e){
            print "Error: Update Poll ".$e->getMessage();
        }
    }


    public function _getOne($data=null){
            try{               
                if ($data['eid']=='' || $data['oid']=='' || $data['pollid']=='') return false;
                $str ="eid='".$data['eid']."' and oid='".$data['oid']."' and pollid='".$data['pollid']."'";
                $row = $this->fetchRow($str);
            if($row) return $row->toArray();
            }  catch (Exception $ex){
                print "Error Leer Encuestas".$ex->getMessage();
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

    public function _getAll($where=null, $order='', $start=0, $limit=0)
    {
        try{
            if ($where['eid']=='' ||  $where['oid']=='') return false;
            $wherestr = "eid = '".$where['eid']."' and oid='".$where['oid']."'";
            if ($limit==0) $limit=null;
            if ($start==0) $start=null;
            
            $rows=$this->fetchAll($wherestr,$order,$start,$limit);
            if($rows) return $rows->toArray();
            return false;

        }catch (Exception $e){
            print "Error: Read All Poll".$e->getMessage();
        }
    }

    public function _getFilter($where=null,$attrib=null,$orders=null){
        try{
            if($where['eid']=='' || $where['oid']=='') return false;
                $select = $this->_db->select();
                if ($attrib=='') $select->from("base_poll");
                else $select->from("base_poll",$attrib);
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
            print "Error: Read Filter Poll ".$e->getMessage();
        }
    }

}
