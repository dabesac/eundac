<?php

class Admin_Model_DbTable_Subsidiary extends Zend_Db_Table_Abstract
{

    protected $_name = 'base_subsidiary';
    protected $_primary = array("subid","eid","oid");
  
 	//Lista todas las sedes en el Index 
    public function _listAllSub($eid='',$oid='')
    {
        try
        {
            if ($eid=='' || $oid=='') return false;
            $f = $this->fetchAll("eid ='$eid' and oid ='$oid'");
            if ($f) return $f->toArray ();
            return false;
        }  
    	catch (Exception $e)
    	{
        	print "Error!";
    	}
    }

    //listar sede por codigo de sede
    public function _listSub($eid='',$oid='',$subid='')
    {
        try
        {
        	if ($eid<>"")
            {
                if ($eid=='' || $oid=='' || $subid=='')return false;
            	$f = $this->fetchRow("eid='$eid' and oid='$oid' and subid='$subid'");
                return (($f)? $f->toArray():false);
            }
        }  
        catch (Exception $e)
        {
            print "Error al leer la sede ";
        }
    }



    //Guarda nueva sede
    public function _save($data)
    {
        try
            {
            	if ($data['eid']=="" || $data['oid']=="" || $data['subid']=="") return false;
            	return $this->insert($data);
                
            }
        catch (Exception $ex)
            {
                print "Error al guardar la Sede ".$ex;
            }
    }

    //Actualiza datos de una sede
    public function _update($data,$str='')
        {
        try
            {
                if ($str=="") return false;
                return $this->update($data,$str);
            }
        catch (Exception $ex){
                print "Error al Actualizar Sede";
            }
        }


    //Eliminar Sede
     public function _delete($eid='',$oid='',$subid='')
     {
        try{
                if ($eid==''||  $oid==''|| $subid=='') return false;
                return $this->delete("eid='$eid' and oid='$oid' and subid='$subid'");
           }
            catch (Exception $error){
                Print "Error al Eliminar Sede";
            }
    }

}