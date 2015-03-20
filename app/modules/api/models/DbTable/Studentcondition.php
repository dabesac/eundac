<?php

class Api_Model_DbTable_Studentcondition extends Zend_Db_Table_Abstract
{
	protected $_name = 'student_condition';
	protected $_primary = array("condition_id","eid","oid", "uid", "pid", "escid","subid","perid");
	protected $_sequence ="s_student_condition"; 

	// public function _getOne($where=array()){
	// 	try {
			
	// 	} catch (Exception $e) {
	// 		print "Error: Read One "
	// 	}
	// }

	public function _getAll($where=null,$order='',$start=0,$limit=0){
		try {
			if ( $where["pid"]=='' || $where["escid"]=='' || $where["uid"]=='' || $where["perid"]=='' ||
				 $where["eid"]=='' || $where["oid"]=='' || $where["subid"]=='') return false;
				
				$wherestr= "pid = '".$where['pid']."' and escid = '".$where['escid']."' and uid = '".$where['uid']."' and perid = '".$where['perid']."' 
				and eid = '".$where['eid']."' and oid = '".$where['oid']."' and subid = '".$where['subid']."' ";
				if ($limit==0) $limit=null;
				if ($start==0) $start=null;
				
				$rows=$this->fetchAll($wherestr,$order,$start,$limit);
				if($rows) return $rows->toArray();
				return false;
		} catch (Exception $e) {
			print "Error: Read All Student Condition".$e->getMessage();
		}
	}

	public function _getAllStudent($where=null){
		try {
			if (!$where["eid"] || !$where["oid"] || !$where["uid"] || !$where["pid"] ||
					 !$where["escid"] || !$where["subid"] || !$where["perid"]) return false;
			$wherestr = "eid = '".$where['eid'].
						"' and oid = '".$where['oid'].
						"' and uid = '".$where['uid'].
						"' and pid = '".$where['pid'].
						"' and escid = '".$where['escid'].
						"' and subid = '".$where['subid'].
						"' and perid = '".$where['perid']."' ";
			$rows=$this->fetchAll($wherestr);
			if($rows) return $rows->toArray();
			return false;
		} catch (Exception $e) {
			print "Error: Read All Condition by Student".$e->getMessage();
		}
	}
    

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