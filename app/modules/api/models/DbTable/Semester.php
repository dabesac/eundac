<?php

class Api_Model_DbTable_Semester extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_semester';
	protected $_primary = array("eid","oid","semid");

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['semid']=='' || $data['name']=='' ) return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save semester ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['semid']=='' ) return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and semid='".$pk['semid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update semester".$e->getMessage();
		}
	}
	
	public function _delete($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['semid']=='') return false;
			$where = "eid = '".$data['eid']."' and oid='".$data['oid']."' and semid='".$data['semid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete semester".$e->getMessage();
		}
	}
	
	public function _getOne($where=array())
	{
		try{
			
			if ($where['eid']=="" || $where['oid']=="" || $where['semid']=="") return false;
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and semid = '".$where['semid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One semester".$e->getMessage();
		}
	}
	
	public function _getAll($where=null,$order='',$start=0,$limit=0)
	{
		try{
			if ($where['eid']=='' ||  $where['oid']=='') return false;
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."'";
			if ($limit==0) $limit=null;
			if ($start==0) $start=null;			
			$rows=$this->fetchAll($wherestr,$order,$start,$limit);
			if($rows) return $rows->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read All semester ".$e->getMessage();
		}
	}

	public function _getSemesterXPeriodsXEscid($where=null){
        try{
            if ($where['escid']=="" || $where['perid']=="" || $where['eid']=="" || $where['oid']=="" ) return false;
			$select = $this->_db->select()
			->from(array('p' => 'base_person'),array('p.pid','p.typedoc','numdoc','p.last_name0','p.last_name1','p.first_name','p.birthday','p.photografy'))
				->join(array('u' => 'base_users'),'u.eid= p.eid and u.pid=p.pid', 
						array('u.uid','u.uid','u.escid','u.eid','u.oid','u.subid'))
				->where('u.eid = ?', $where['eid'])
				->where('u.oid = ?', $where['oid'])
				->where('u.uid = ?', $where['uid'])
				->where('u.rid = ?', $where['rid'])
				->order('last_name0');
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;         
        }  catch (Exception $ex){
            print "Error: Obteniendo semestres".$ex->getMessage();
        }
    }
	// select * from semestre
 // where semid in (select SEMID from periodos_cursos
 // where perid='$perid' and escid='$escid' and eid='$eid' and oid='$oid') 
 // ORDER BY cast(semid as integer) 
}
