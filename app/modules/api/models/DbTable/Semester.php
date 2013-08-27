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


	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_semester");
				else $select->from("base_semester",$attrib);
				foreach ($where as $atri=>$value){
					$select->where("$atri = ?", $value);
				}
				foreach ($orders as $key => $order) {
						$select->order($order);
				}
				$results = $select->query();
				$rows = $results->fetchAll();
				if ($rows) return $rows;
				return false;
		}catch (Exception $e){
			print "Error: Read Filter Semester ".$e->getMessage();
		}
	}

	public function _getSemesterXPeriodsXEscid($where=null){
        try{
            if ($where['escid']=="" || $where['perid']=="" || $where['eid']=="" || $where['oid']=="" ) return false;
			$sub_select=$this->_db->select()
				->from(array('pc' => 'base_periods_courses'),array('semid'))
					->where("eid = ?",$where['eid'])->where("oid = ?",$where['oid'])
					->where("perid = ?",$where['perid'])->where("escid = ?",$where['escid']);
			$select=$this->_db->select()
				->from(array('s' => 'base_semester'),array('s.*'))
					->where('s.semid IN ?',$sub_select)
					->order('cast(semid as integer)');
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;         
        }  catch (Exception $ex){
            print "Error: Obteniendo semestres".$ex->getMessage();
        }
    }

}

