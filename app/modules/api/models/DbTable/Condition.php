<?php

class Api_Model_DbTable_Condition extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_condition';
	protected $_primary = array("cnid","pid","escid","uid","perid","eid","oid","subid");
	protected $_sequence ="s_conditions"; 

	public function _getFilter($where=array())
	{
		try{
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and escid = '".$where['escid']."' and pid='".$where['pid']."' and uid='".$where['uid']."' and perid='".$where['perid']."' and subid='".$where['subid']."'";
			$row = $this->fetchAll($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read Filter Curricula".$e->getMessage();
		}
	}

  /*Guarda un registro en la tabla CondicionXAlumno*/
     public function _guardar($data){
        try{
             	// if ($data['eid']=='' || $data['oid']=='' ||  $data['escid']=='' || $data['subid']=='' || $data['perid']=='' || $data['uid'] || $data['pid']) return false;
            if($data=='')return false;
            return $this->insert($data);
        }catch (Exception $e){
            print "Error: Al momento de insertar condicion de alumno ".$e->getMessage();
        }
    } 

	 public function _getUsercCondition($eid='',$oid='',$str='',$escid='',$perid='')
	{
		try{

			 $sql=$this->_db->query("
                select  distinct on(uid,ca.subid,ca.eid,ca.oid,ca.escid,ca.pid,p.first_name,p.last_name0,p.last_name1) * from base_condition AS ca
                inner join base_person as p
                on ca.pid=p.pid and ca.eid=p.eid
                where ca.eid='$eid' and ca.oid ='$oid' and ca.escid='$escid' and ca.perid='$perid' $str
            ");
            
            $row=$sql->fetchAll();
            return $row; 
		}catch (Exception $e){
			print "Error: Read Condition ".$e->getMessage();
		}
		
	}


	    public function _getUsersCondition($where=null){
         try{
			 $sub_select = $this->_db->select()
			 	->from(array('co' => 'base_condition'),array('uid'))
                ->where("perid = ?", $where['perid'])->where("eid = ?", $where['eid'])->where("oid = ?", $where['oid'])->where("escid = ?", $where['escid']); 
			 $select = $this->_db->select()
			->from(array('u' => 'base_users'),array('u.eid','u.oid','u.subid','u.uid','u.escid','u.pid','p.first_name','p.last_name0','p.last_name1'))
				->join(array('p' => 'base_person'),'u.pid=p.pid and u.eid=p.eid')
				->where('u.state = ?', 'A')->where('u.oid = ?', $where['oid'])->where('u.oid = ?', $where['oid'])->where('u.escid = ?',$where['escid'])->where('u.rid = ?','AL')
				 ->where('(p.last_name0 LIKE ?)', '%'.$where['ap'].'%')->where('(p.last_name1 LIKE ?)', '%'.$where['am'].'%')->where('(upper(p.first_name) LIKE ?)', '%'.$where['am'].'%')
				->where("u.uid NOT IN ?", $sub_select) ;
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;	
         }catch (Exception $ex) {
             print $ex->getMessage();
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
			print "Error: Delete Condition".$e->getMessage();
		}
	}





}