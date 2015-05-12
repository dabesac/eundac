<?php
class Api_Model_DbTable_LangRegister extends Zend_Db_Table_Abstract{
  protected $_name = 'lang_student_register';
  protected $_primary = array( 'eid', 'oid', 'perid', 'subid', 'pid', 'uid');
  	
	public function _save($data){
		try {	
				if ($data['eid']=='' || $data['oid']=='' || $data['perid']=='' ||  $data['subid']=='' || $data['pid']=='' || $data['uid']=='') return false;
				return $this->insert($data);
				return false;			
		} catch (Exception $e) {
			print "Error: Save Period".$e->getMessage();
		}
	}


	public function _update($data,$pk){
	    try {
	        if ($pk['eid']=='' || $pk['oid']=='' || $pk['perid']=='' || $pk['subid']=='' || $pk['pid']=='' || $pk['uid']=='') return false;
	        $where = "eid = '".$pk['eid']."' and perid='".$pk['perid']."' and pid='".$pk['pid']."'";
	        return $this->update($data, $where);
	        return false;
	    } catch (Exception $e) {
	      print "Error: Update Course".$e->getMessage();
	    }
	}


	public function _getFilter($where=null,$attrib=null,$orders=null){
    try{
          if($where['eid']=='') return false;
            $select = $this->_db->select();
            if ($attrib=='') $select->from("lang_student_register");
            else $select->from("lang_student_register",$attrib);
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
        print "Error: Read Filter Registers".$e->getMessage();
      }
    }

  }