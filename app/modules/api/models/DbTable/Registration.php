<?php

class Api_Model_DbTable_Registration extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_registration';
	protected $_primary = array("eid","oid","escid","subid","regid","pid","uid","perid");

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['regid']=='' || $data['pid']=='' || $data['uid']=='' || $data['perid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Registration ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk)
	{
		try{
			if ($pk['eid']=='' ||   $pk['oid']=='' ||  $pk['escid']=='' ||  $pk['regid']=='' || $pk['subid']=='' || $pk['pid']=='' || $pk['uid']=='' || $pk['perid']=='') return false;
			$where = "eid = '".$pk['eid']."' and pid='".$pk['pid']."' and oid = '".$pk['oid']."' and escid = '".$pk['escid']."' and uid = '".$pk['uid']."' and subid = '".$pk['subid']."' and regid = '".$pk['regid']."' and perid = '".$pk['perid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Registration".$e->getMessage();
		}
	}
	
	public function _delete($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['regid']==''|| $data['pid']==''|| $data['uid']=='' || $data['perid']=='') return false;
			$where = 	"eid = '".$data['eid']."' and oid='".$data['oid']."' and escid='".$data['escid']."' and subid='".$data['subid']."' and regid='".$data['regid']."' and pid='".$data['pid']."' and uid='".$data['uid']."' and perid='".$data['perid']."'";			
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Registration".$e->getMessage();
		}
	}

	
	public function _getOne($where=array()){
		try{
			if ($where['eid']=='' ||  $where['oid']=='' || $where['escid']=='' || $where['subid']=='' || $where['regid']=='' || $where['pid']=='' || $where['uid']=='' || $where['perid']=='') return false;
			$wherestr = "eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".$where['escid']."' and subid='".$where['subid']."' and regid='".$where['regid']."' and pid='".$where['pid']."' and uid='".$where['uid']."' and perid='".$where['perid']."'";

			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Registration".$e->getMessage();
		}
	}

	
 public function _getFilter($where=array()){
		try{
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and escid = '".$where['escid']."' and subid='".$where['subid']."'";
			$row = $this->fetchAll($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read Filter Registration".$e->getMessage();
		}
	}

public function _getPaymentsStudent($where=null,$attrib=null,$order=null){
	try {
		if($where=='' && $attrib=='') return false;
		$base_PaymentsDetail = new Api_Model_DbTable_PaymentsDetail();
		$data_payments = $base_PaymentsDetail->_getFilter($where,$attrib,$orders);
		if($data_payments) return $data_payments;
		return false;
	} catch (Exception $e) {
		print "Error: Read PaymentStudent".$e->getMessage();
	}
}



 public function _totalSchoolEnrollment($where=null)
	{
		try{
			if ($where['eid']=='' ||  $where['oid']=='' || $where['perid']=='' || $where['facid']=='') return false;
			 $select = $this->_db->select()
			->from(array('m' => 'base_registration'),array('e.name'))
				->join(array('e' => 'base_speciality'),'e.eid=m.eid and e.oid=m.oid and m.escid=e.escid', 
						array('conteo' => 'COUNT(*)'))
				->where('perid = ?', $where['perid'])->where('facid = ?', $where['facid'])->where('m.state = ?','M')
				->where('m.oid = ?', $where['oid'])->where('m.eid = ?', $where['eid'])
				->group('e.name');
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;	
		}catch (Exception $e){
			print "Error: Read UserInfo ".$e->getMessage();
		}
		
	}
       



}
