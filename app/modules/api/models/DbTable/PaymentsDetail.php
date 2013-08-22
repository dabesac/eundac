<?php

class Api_Model_DbTable_PaymentsDetail extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_payments_detail';
	protected $_primary = array('pdid');
	protected $_sequence ="s_detailpayment";

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || 
				$data['pid']=='' || $data['uid']=='' || $data['perid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: save Payments Detail".$e->getMessage();
		}
	}

	public function _update($data,$pk)
	{
		try{
			if ($pk['eid']=='' ||   $pk['oid']=='' ||  $pk['escid']=='' ||  $pk['subid']=='' || 
				$pk['pid']=='' || $pk['uid']=='' || $pk['perid']=='') return false;
			$where = "eid = '".$pk['eid']."' and pid='".$pk['pid']."' and oid = '".$pk['oid']."'
			 and escid = '".$pk['escid']."' and uid = '".$pk['uid']."' and subid = '".$pk['subid']."' 
			 and perid = '".$pk['perid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Payments Detail".$e->getMessage();
		}
	}

	public function _getOne($where=array())
	{
		try{
			if ($where['pdid']==''|| $where['eid']=='' ||  $where['oid']=='' || $where['escid']=='' || $where['subid']==''  || 
				$where['pid']=='' || $where['uid']=='' || $where['perid']=='') return false;
			$wherestr = "pdid ='".$where['pdid']."' eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".$where['escid']."' 
			and subid='".$where['subid']."'  and pid='".$where['pid']."' and uid='".$where['uid']."' and perid='".$where['perid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Payments Detail".$e->getMessage();
		}
	}

	public function _getAll($where=null,$order='',$start=0,$limit=0)
	{
		try{
			if ($where['eid']=='' ||  $where['oid']=='' || $where['escid']=='' || $where['subid']==''  || 
				$where['pid']=='' || $where['uid']=='' || $where['perid']=='' ) return false;
			$wherestr = "eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".$where['escid']."' 
			and subid='".$where['subid']."'  and pid='".$where['pid']."' and uid='".$where['uid']."' and perid='".$where['perid']."'";
			if ($limit==0) $limit=null;
			if ($start==0) $start=null;
			
			$rows=$this->fetchAll($wherestr,$order,$start,$limit);
			if($rows) return $rows->toArray();
			return false;

		}catch (Exception $e){
			print "Error: Read All Payments Detail".$e->getMessage();
		}
	}

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_payments_detail");
				else $select->from("base_payments_detail",$attrib);
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
			print "Error: Read Filter Payments Detail".$e->getMessage();
		}
	}



}