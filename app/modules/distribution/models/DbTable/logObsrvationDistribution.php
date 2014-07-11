<?php

class Distribution_Model_DbTable_logObsrvationDistribution extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_log_observation_distribution';
	protected $_primary = array("logobdistrid");
    protected $_sequence ="s_logobservation_distribution";

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']==''
				|| $data['distid']=='' || $data['perid']=='' ) return false;
			return $this->insert($data);
		}catch (Exception $e){
				print "Error: Save Log Observation Distribution ".$e->getMessage();
		}
	}
		
	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_log_observation_distribution");
				else $select->from("base_log_observation_distribution",$attrib);
				foreach ($where as $atri=>$value){
					$select->where("$atri = ?", $value);
				}
				if ($orders<>null || $orders<>"") {
					if (is_array($orders))
						$select->order($orders);
				}
				$results = $select->query();
				$rows = $results->fetchAll();
				if ($rows) return $rows;
				return false;
		}catch (Exception $e){
			print "Error: Read Filter Log Observation Distribution ".$e->getMessage();
		}
	}

	public function _update($data=null,$pk=null)
	{
		try{
			if ($pk['logobdistrid']=='' || $pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']=='' 
			|| $pk['distid']=='' || $pk['perid']=='' ) return false;
			$where = "logobdistrid = '".$pk['logobdistrid']."' and eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."'
					 and subid='".$pk['subid']."' and distid='".$pk['distid']."' and perid='".$pk['perid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Log observation Distribution".$e->getMessage();
		}
	}

	public function _getUltimateObservation($where=null){
		try{
			if ($where['eid']=='' ||  $where['oid']=='' || $where['distid']=='' || $where['escid']=='' || $where['subid']=='' || $where['perid']=='') return false;
			// $wherestr = "eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".$where['escid']."' and subid='".$where['subid']."' and distid='".$where['distid']."' and perid='".$where['perid']."'";
			$select = $this->_db->select()
								->from("base_log_observation_distribution")
								->where('eid = ?', $where['eid'])
								->where('oid = ?', $where['oid'])
								->where('distid = ?', $where['distid'])
								->where('escid = ?', $where['escid'])
								->where('subid = ?', $where['subid'])
								->where('perid = ?', $where['perid'])
								->where('state = ?', 'O')
								->order(array('logobdistrid DESC'))
								->limit(1);
			$results = $select->query();
			$rows = $results->fetchAll();
			if ($rows) return $rows;
			return false;
		}catch (Exception $ex){
			print "Error: Get Info Distribution ".$ex->getMessage();
		}
		// $sql="select * from base_log_observation_distribution 
		// 	where eid='20154605046' and oid='1' and escid='4MI' and subid='1901' and state='O'
		// 	order by logobdistrid desc
		// 	limit 1";
	}
}
