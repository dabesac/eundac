<?php

class Api_Model_DbTable_Addreportacadadm extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_add_reportacad_adm';
	protected $_primary = array("eid","oid","escid","subid","perid","uid","pid");

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['perid']=='' || $data['pid']=='' || $data['uid']=='' ) return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save Add_reportacad_adm ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']=='' || $pk['perid']=='' || $pk['pid']=='' || $pk['uid']=='') return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."' and perid='".$pk['perid']."' and pid='".$pk['pid']."' and uid='".$pk['uid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Add_reportacad_adm ".$e->getMessage();
		}
	}
	
	public function _delete($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['perid']=='' || $data['pid']=='' || $data['uid']=='') return false;
			$where = "eid = '".$data['eid']."' and oid='".$data['oid']."' and escid='".$data['escid']."' and subid='".$data['subid']."' and perid='".$data['perid']."'	and pid='".$data['pid']."' and uid='".$data['uid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Add_reportacad_adm ".$e->getMessage();
		}
	}

	public function _getOne($where=array()){
		try{
			if ($where['eid']=='' ||  $where['oid']=='' || $where['escid']=='' || $where['subid']=='' || $where['perid']=='' || $where['pid']=='' || $where['uid']=='') return false;
			$wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".$where['escid']."' and subid='".$where['subid']."' and perid='".$where['perid']."' and pid='".$where['pid']."' and uid='".$where['uid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Add_reportacad_adm ".$e->getMessage();
		}
	}

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_add_reportacad_adm");
				else $select->from("base_add_reportacad_adm",$attrib);
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
			print "Error: Read Filter Add_reportacad_adm ".$e->getMessage();
		}
	}
}