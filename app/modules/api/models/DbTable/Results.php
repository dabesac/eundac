<?php

class Api_Model_DbTable_Results extends Zend_Db_Table_Abstract

{
    protected $_name = 'base_poll_restuls';
    protected $_primary = array("eid", "oid", "code", "qid", "altid", "escid", "subid", "uid", "pid");


    
    public function _save($data)
    {
        try{
            if ($data['eid']=='' || $data['oid']=='' || $data['code']=='' || $data['qid']=='' ||
                $data['altid']=='' || $data['escid']=='' || $data['subid']=='' || $data['uid']=='' || $data['pid']=='') return false;
            
            return $this->insert($data);
            return false;
        }catch (Exception $e){
                print "Error: save Poll ".$e->getMessage();
        }
    }


    public function _getAlumnoPasoEncuestaT($data){
    	try
    	{
	        if ($data['eid']=="" || $data['oid']=="" || $data['pid']=="" || $data['uid']=="" || $data['qid']=="")return false;

	        $str= "eid='".$data['eid']."' and  oid='".$data['oid']."' and pid='".$data['pid']."' and uid='".$data['uid']."' and qid='".$data['qid']."' ";
	        
	        $row=$this->fetchRow($str);
	        if($row) return $row->toArray();   

    	}
    	catch (Exception $e)
    	{
            print "Error: save Poll ".$e->getMessage();
        }	

    }


}