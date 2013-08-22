<?php

class Api_Model_DbTable_Studentcondition extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_student_condition';
	protected $_primary = array("temid","eid","oid","escid","subid","perid","uid","pid");
	protected $_sequence ="s_conditionstudent"; 


    
    /*Guarda un registro en la tabla CondicionXAlumno*/
     public function _guardar($datos){
     	        try{
            return $this->insert($datos);
        }catch (Exception $e){
            print "Error: Al momento de insertar condicion de alumno temporal ".$e->getMessage();
        }
    } 

      public function _delete($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['uid']=='' || $data['perid']=='' || $data['escid']=='' || $data['cnid']=='' || $data['pid']==''|| $data['subid']=='') return false;
			$where = 	"eid = '".$data['eid']."' and oid='".$data['oid']."' and uid='".$data['uid']."' and perid='".$data['perid']."' and escid='".$data['escid']."' and cnid='".$data['cnid']."' and pid='".$data['pid']."'  and subid='".$data['subid']."' ";			
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Condition alumno".$e->getMessage();
		}
	}
}