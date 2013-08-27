<?php

class Api_Model_DbTable_Periods extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_periods';
	protected $_primary = array("eid","oid","perid");

	public function _getOne($where=array()){
		try{
			if ($where['eid']=="" || $where['oid']=="" || $where['perid']=="") return false;
			$wherestr="eid='".$where['eid']."' and oid='".$where['oid']."' and perid='".$where['perid']."' and (state='I' or state='A')";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Entity ".$e->getMessage();
		}
	}

	public function _getOnePeriod($where=array()){
		try{
			if ($where['eid']=="" || $where['oid']=="" || $where['perid']=="") return false;
			$wherestr="eid='".$where['eid']."' and oid='".$where['oid']."' and perid='".$where['perid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Entity ".$e->getMessage();
		}
	}
	
	public function _getPeriodsCurrent($data=null)
	{
		try{
			if ($data['eid']=='' || $data['oid']=='' ) return false;
			$where = "eid='".$data['eid']."' and oid='".$data['oid']."' and state='A'";
			$row = $this->fetchRow($where);
			if ($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Entity ".$e->getMessage();
		}
	}
	
	public function _getPeriodsNext($data=null)
	{
		try{
			if ($data['eid']=='' || $data['oid']=='' ) return false;
			$where = "eid='".$data['eid']."' and oid='".$data['oid']."' and state='I'";
			$row = $this->fetchRow($where);
			if ($row) return $row->toArray();
			return $this->_getPeriodsCurrent($data);	
		}catch (Exception $e){
			print "Error: Read One Entity ".$e->getMessage();
		}
	}
	
	public function _getPeriodsTermporaly()
	{
		try{
			if ($data['eid']=='' || $data['oid']=='' ) return false;
			$where = "eid='$eid' and oid='$oid' and state='T'";
			$row = $this->fetchAll($where);
			if ($row) return $row->toArray();
				
		}catch (Exception $e){
			print "Error: Read One Entity ".$e->getMessage();
		}
	}

	public function _getPeriodsxYears($data=null){
		// $sql=$this->_db->query("select * from periodos where eid='$eid' and oid ='$oid' and left(perid,2)='$anio' order by perid");
		try {
			if ($data['eid']=='' || $data['oid']=='') return false;
			$wherestr="eid='".$data['eid']."' and oid='".$data['oid']."' and left(perid,2)='".$data['year']."'";
			$order="perid asc";
			$rows= $this->fetchAll($wherestr,$order);
			if ($rows) return $rows->toArray();
			return false;
		} catch (Exception $e) {
			print "Error: Read All Periods x Years" .$e->getMessage();
		}
	}




	
	
}
