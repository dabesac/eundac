    <?php

class Api_Model_DbTable_Person extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_person';
	protected $_primary = array("eid","pid");

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['pid']=='' || $data['typedoc']=='' || $data['last_name0']=='' || $data['last_name1']=='' || $data['first_name']=='' || $data['register']=='' || $data['created']==''	) return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save User ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk)
	{
		try{
			if ($pk['eid']=='' ||  $pk['pid']=='' ) return false;
			$where = "eid = '".$pk['eid']."' and pid='".$pk['pid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update User".$e->getMessage();
		}
	}
	
	public function _delete($data)
	{
		try{
			if ($data['eid']=='' || $data['pid']=='' ) return false;
			$where = 	"eid = '".$data['eid']."' and pid='".$data['pid']."'";			
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete User".$e->getMessage();
		}
	}

	
	public function _getOne($where=array()){
		try{
			if ($where['eid']=="" || $where['pid']=="") return false;
			$wherestr="eid = '".$where['eid']."' and pid = '".$where['pid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Entity ".$e->getMessage();
		}
	}


public function _getAll($where,$order='',$start=0,$limit=0){
		try{
			if ($where['eid']=='') return false; 
			$wherestr= "eid='".$where['eid']."'";
			if ($limit==0) $limit=null;
			if ($start==0) $start=null;
			$rows=$this->fetchAll($wherestr,$order,$start,$limit);
			if($rows) return $rows->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read All Persom ".$e->getMessage();
		}
	}
	
	public function _getFilter($where=array()){
		try{
			$wherestr="eid = '".$where['eid']."' and state = '".$where['state']."'";
			$row = $this->fetchAll($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read Filter Persom ".$e->getMessage();
		}
	}


	// public function _getFilter($atrib=array()){
	// 	try{
	// 		// if ($where['eid']=='' || $where['oid']=='') return false;
	// 		 $select = $this->select()->from( 'base_person')->where($atrib);
	// 		 $stmt = $this->_db->query($select);
 //              $result = $stmt->fetchAll($select);
 //              print_r($result);
			
	// 		// $rows = $this->fetchAll($select);

	// 		// if($rows) return $rows->toArray();
	// 		// return false;
	// 	}catch (Exception $e){
	// 		print $e->getMessage();
	// 	}
	// }

}
