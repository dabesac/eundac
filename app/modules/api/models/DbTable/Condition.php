<?php

class Api_Model_DbTable_Condition extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_condition';
	protected $_primary = array("cnid","pid","escid","uid","perid","eid","oid","subid");

	public function _getFilter($where=array())
	{
		try{
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and escid = '".$where['pid']."' and pid='".$where['subid']."'";
			$row = $this->fetchAll($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read Filter Curricula".$e->getMessage();
		}
	}


	 public function _getUsercCondition($eid='',$oid='',$str='',$escid='',$perid='')
	{
		try{
			// if ($where['eid']=='' ||  $where['oid']=='' || $where['perid']=='' || $where['facid']=='') return false;
			//  $select = $this->_db->select()->distinct()
			// ->from(array('ca' => 'base_condition'),array('uid','ca.pid','subid','ca.eid','oid','escid','p.first_name','p.last_name0','p.last_name1'))
			// 	->join(array('p' => 'base_person'),'ca.pid=p.pid and ca.eid=p.eid')
			// 	->where('ca.eid = ?', $where['eid'])->where('ca.oid = ?', $where['oid'])->where('ca.escid = ?',$where['escid'])->where('ca.perid = ?',$where['perid']);
			// $results = $select->query();			
			// $rows = $results->fetchAll();
			// if($rows) return $rows;
			// return false;	

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

           // select u.eid, u.oid, u.sedid,u.uid,u.escid,u.pid,p.nombres,p.ape_pat,p.ape_mat from usuario as u
           //      inner join persona as p
           //      on u.pid=p.pid and u.eid=p.eid and u.oid=p.oid
           //      where u.estado='A' and u.eid='$eid' and u.oid ='$oid' and u.rid='AL' and u.escid='$escid' 
           //      and (p.ape_pat like '%$ap%' and p.ape_mat like '%$am%' and u.uid like '$codigo%' and upper(p.nombres) like '%$nombre%' ) 
	//and uid not in (select uid from condicion_alumno
           //      where perid='$perid' AND eid='$eid' 
           //      and oid='$oid' and escid='$escid')

	    public function _getUsersCondition($where=null){
         try{
	// if ($where['eid']=='' ||  $where['oid']=='' || $where['perid']=='' || $where['facid']=='') return false;
			 $sub_select = $this->_db->select()
				->from("base_condition", "uid") 
                ->where("perid = ?", $where['perid'])->where("eid = ?", $where['eid'])->where("oid = ?", $where['oid'])->where("escid = ?", $where['escid']); 
			 $select = $this->_db->select()
			->from(array('u' => 'base_users'),array('u.eid','u.oid','u.subid','u.uid','u.escid','u.pid','p.first_name','p.last_name0','p.last_name1'))
				->join(array('p' => 'base_person'),'u.pid=p.pid and u.eid=p.eid')
				->where('u.state = ?', 'A')->where('u.oid = ?', $where['oid'])->where('u.oid = ?', $where['oid'])->where('u.escid = ?',$where['escid'])->where('u.rid = ?','AL')
				 ->where('(p.last_name0 LIKE ?)', $where['ap'])->where('(p.last_name1 LIKE ?)', $where['am'])->where('(upper(p.first_name) LIKE ?)', $where['am'])
				->where("u.uid NOT IN ?", $sub_select) ;
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;	
         }catch (Exception $ex) {
             print $ex->getMessage();
         }
     }

}