<?php
class Api_Model_DbTable_Lockandunlock extends Zend_Db_Table_Abstract{

	protected $_name = 'base_lock_unlock_user';
	protected $_primary = array('dbuid');
	protected $_sequence = 's_lock_unlock';

	public function _save($data){
		try {
				if ($data['eid']=='' || $data['oid']=='' || $data['subid']=='' || $data['pid']=='' || $data['escid']=='' || $data['uid']=='') return false;
				return $this->insert($data);
				return false;			
		} catch (Exception $e) {
			print "Error: Save Lock and Unlock user".$e->getMessage();
		}
	}

	public function _update($data,$pk){
		try {
				if ($pk['dbuid']=='' || $pk['eid']=='' || $pk['oid']=='' || $pk['subid']=='' || $pk['escid']=='' || $pk['pid']=='' || $pk['uid']=='') return false;
				$where = "dbuid = '".$pk['dbuid']."' and eid = '".$pk['eid']."' and oid='".$pk['oid']."' and subid='".$pk['subid']."' and escid='".$pk['escid']."' and pid='".$pk['pid']."' and uid='".$pk['uid']."'";
				return $this->update($data, $where);
				return false;
		} catch (Exception $e) {
			print "Error: Update Lock and Unlock user".$e->getMessage();
		}
	}

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_lock_unlock_user");
				else $select->from("base_lock_unlock_user",$attrib);
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
			print "Error: Read Filter Lock and Unlock user ".$e->getMessage();
		}
	}
}