<?php
class Api_Model_DbTable_ProgramTasas extends Zend_Db_Table_Abstract{
	protected $_name = 'lang_program_tasa';
	protected $_primary =array('perid', 'cid', 'eid', 'tasaid');

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
	        if($where['eid']=='') return false;
	          $select = $this->_db->select();
	          if ($attrib=='') $select->from("lang_program_tasa");
	          else $select->from("lang_program_tasa",$attrib);
	          foreach ($where as $atri=>$value){
	            $select->where("$atri = ?", $value);
	          }
	          if($orders){
	            foreach ($orders as $key => $order) {
	              $select->order($order);
	            } 
	          }
	          
	          $results = $select->query();
	          $rows = $results->fetchAll();
	          if ($rows) return $rows;
	          return false;
	    }catch (Exception $e){
	    	print "Error: Read Filter Tasas Por Programa ".$e->getMessage();
	    }
    }


  	public function _save($data){
		try {	
				if ($data['eid']=='' || $data['cid'] == '' || $data['perid'] == '' || $data['tasaid'] == '') return false;
				return $this->insert($data);
				return false;			
		} catch (Exception $e) {
			print "Error: Save Program".$e->getMessage();
		}
	}

	/*public function _update($data,$pk){
		try {
				if ($pk['eid']=='' || $pk['cid']=='' || $pk['perid']=='' || $pk['turno']=='') return false;
				$where = "eid = '".$pk['eid']."' and cid='".$pk['cid']."' and perid='".$pk['perid']."' and turno='".$pk['turno']."'";
				return $this->update($data, $where);
				return false;
		} catch (Exception $e) {
			print "Error: Update Course".$e->getMessage();
		}
	}*/

	public function _delete($pk){
		try{
			if ($pk['eid']=='' || $pk['cid']=='' || $pk['perid']=='' || $pk['tasaid']=='') return false;
			$where = "eid = '".$pk['eid']."'and cid='".$pk['cid']."' and perid='".$pk['perid']."' and tasaid='".$pk['tasaid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Organization ".$e->getMessage();
		}
	}
}
  
