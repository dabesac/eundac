<?php
class Api_Model_DbTable_LangProgramTurnos extends Zend_Db_Table_Abstract{
	protected $_name = 'lang_program_turn';
	protected $_primary =array('perid', 'course_id', 'eid', 'turn_id', 'subid', 'type');

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
	        if($where['eid']=='') return false;
	          $select = $this->_db->select();
	          if ($attrib=='') $select->from("lang_program_turn");
	          else $select->from("lang_program_turn",$attrib);
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
				if ($data['eid']=='' || $data['course_id'] == '' || $data['perid'] == '' || $data['turn_id'] == '' || $data['subid'] == '' || $data['type'] == '') return false;
				return $this->insert($data);
				return false;			
		} catch (Exception $e) {
			//print "Error: Save Program".$e->getMessage();
		}
	}

	public function _update($data,$pk){
		try {
				if ($pk['eid']=='' || $pk['course_id']=='' || $pk['perid']=='' || $pk['turn_id']=='' || $pk['subid']=='' || $pk['type']=='') return false;
				$where = "eid = '".$pk['eid']."' and course_id='".$pk['course_id']."' and perid='".$pk['perid']."' and turn_id='".$pk['turn_id']."' and subid='".$pk['subid']."' and type='".$pk['type']."'";
				return $this->update($data, $where);
				return false;
		} catch (Exception $e) {
			print "Error: Update Course".$e->getMessage();
		}
	}

	public function _delete($pk){
		try{
			if ($pk['eid']=='' || $pk['course_id']=='' || $pk['perid']=='' || $pk['turn_id']=='' || $pk['subid']=='' || $pk['type'] == '') return false;
			$where = "eid = '".$pk['eid']."'and course_id='".$pk['course_id']."' and perid='".$pk['perid']."' and turn_id ='".$pk['turn_id']."' and subid ='".$pk['subid']."' and type ='".$pk['type']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Organization ".$e->getMessage();
		}
	}
}
  
