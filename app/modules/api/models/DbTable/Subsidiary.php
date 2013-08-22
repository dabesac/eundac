<?php

class Api_Model_DbTable_Subsidiary extends Zend_Db_Table_Abstract
{

    protected $_name = 'base_subsidiary';
    protected $_primary = array("subid","eid","oid");
  
 	//Lista todas las sedes en el Index 
    public function _getAll($where=null,$order='',$start=0,$limit=0){

        try {
            if($where['eid']=='' || $where['oid']=='')
                $wherestr= null;
            else
                $wherestr="eid='".$where['eid']."' and oid='".$where['oid']."'";
            if($limit==0) $limit=null;  
            if($start==0) $start=null;

            $rows=$this->fetchAll($wherestr,$order,$start,$limit);
            if($rows) return $rows->toArray();
            return false;

        } catch (Exception $e) {
            print "Error: Leer las facultades".$e->getMessage();           
        }
    }

    //listar sede por codigo de sede
    public function _getOne($where=array()){
        try{
            if ($where['oid']=="" || $where['eid']=="" || $where['subid']=="" ) return false;
            $wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and subid = '".$where['subid']."'";
            $row = $this->fetchRow($wherestr);
            if($row) return $row->toArray();
            return false;
        }catch (Exception $e){
            print "Error: al Leer la Organization ".$e->getMessage();
        }
    }




    //Guarda nueva sede
    public function _save($data)
    {
        try{
            if ($data['oid']=='' || $data['eid']=='' ) return false;
            return $this->insert($data);
            return false;
        }catch (Exception $e){
                print "Error: Save Organization ".$e->getMessage();
        }
    }

    //Actualiza datos de una sede
    public function _update($data,$pk)
    {
        try{
            if ($pk['oid']=="" || $pk['eid']=="" || $pk['subid']=="" ) return false;
            $where="eid = '".$pk['eid']."' and oid = '".$pk['oid']."' and subid = '".$pk['subid']."'";
            return $this->update($data, $where);
            return false;
        }catch (Exception $e){
            print "Error al Actualizar Organizacion ".$e->getMessage();
        }
    }


    //Eliminar Sede
    public function _delete($pk)
    {
        try{
            if ($pk['oid']=="" || $pk['eid']=="" || $pk['subid']=="" ) return false;
            $where="eid = '".$pk['eid']."' and oid = '".$pk['oid']."' and subid = '".$pk['subid']."'";
            return $this->delete($where);
            return false;
        }catch (Exception $e){
            print "Error: Delete Organization ".$e->getMessage();
        }
    }

}