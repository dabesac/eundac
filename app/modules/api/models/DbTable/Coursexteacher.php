<?php

class Api_Model_DbTable_Coursexteacher extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_course_x_teacher';
	protected $_primary = array("eid","oid","escid","subid","courseid","curid","turno","perid","uid","pid");

	public function _save($data){
		try {
			if($data["eid"]=='' || $data["oid"]=='' ||  $data["escid"]=='' ||  $data["subid"] =='' || $data["courseid"] =='' || $data["curid"] =='' 
				| $data["turno"]=='' || $data["perid"]=='' || $data["uid"]=='' || $data["pid"]=='')
				return false;
				return $this->insert($data);
				return false;
		} catch (Exception $e) {
			print "Error: Save cours ".$e->getMessage();
		}
	}


	public function _update($data,$pk){
		try{
			if ($pk["eid"]=='' || $pk["oid"]=='' ||  $pk["escid"]=='' ||  $pk["subid"] =='' || $pk["courseid"]=='' || $pk["curid"] =='' || $pk["turno"] =='' || $pk["perid"]=='' || $pk["uid"]=='' || $pk["pid"]=='') return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."' and courseid='".$pk['courseid']."' and curid='".$pk['curid']."' and turno='".$pk['turno']."' and perid='".$pk['perid']."' and uid='".$pk['uid']."' and pid='".$pk['pid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Organization ".$e->getMessage();
		}
	}

	public function _getOne($where=array()){
		try{
			if ($where["eid"]=='' || $where["oid"]=='' ||  $where["escid"]=='' ||  $where["subid"] =='' || $where["courseid"]=='' || $where["curid"] =='' || $where["turno"] =='' || $where["perid"]=='' || $where["uid"]=='' || $where["pid"]=='') return false;
			$wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".$where['escid']."' and subid='".$where['subid']."' and courseid='".$where['courseid']."' and curid='".$where['curid']."' and turno='".$where['turno']."' and perid='".$where['perid']."' and uid='".$where['uid']."' and pid='".$where['pid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Course ".$e->getMessage();
		}
	}


	public function _getAll($where=array()){
		try{
			if ($where["eid"]=='' || $where["oid"]=='' ||  $where["escid"]=='' ||  $where["subid"] =='' || 
				$where["courseid"]=='' || $where["curid"] =='' || $where["turno"] =='' || $where["perid"]=='') return false;
			$wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".
					$where['escid']."' and subid='".$where['subid']."' and courseid='".$where['courseid']."' 
					and curid='".$where['curid']."' and turno='".$where['turno']."' and perid='".$where['perid']."'";
			$rows = $this->fetchAll($wherestr);
			if($rows) return $rows->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read All Course ".$e->getMessage();
		}
	}



	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_course_x_teacher");
				else $select->from("base_course_x_teacher",$attrib);
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
			print "Error: Read Filter Courses Teacher ".$e->getMessage();
		}
	}


	public function _getinfoTeacher($where=null,$attrib=null,$order=null){
		try {
			if ($where=='' && $attrib=='' ) return false;
				$base_teacher = new Api_Model_DbTable_Users();
				$data_teacher = $base_teacher->_getinfoUser($where,$attrib,$order);
			if($data_teacher) return $data_teacher;
			return false;
		} catch (Exception $e) {
			print "Error: Read info Course ";

		}
	}


	 /* Retorna los datos del docente asignado a la escuela($escid), curso($cursoid) y turno($turno), */
    public function _getinfoDoc($where=null){
        try{
            if ($where['eid']=='' || $where['oid']=='' || $where['escid']=='' || $where['perid']=='' || $where['courseid']=='' || $where['turno']=='') return false;
            $eid=$where['eid'];
            $oid=$where['oid'];
            $perid=$where['perid'];
            $escid=$where['escid'];
            $turno=$where['turno'];
            $subid=$where['subid'];
            $courseid=$where['courseid'];
            $curid=$where['curid'];
            $str=" select last_name0 || ' ' || last_name1 || ', ' || first_name as nameteacher,            
            pc.pid from base_course_x_teacher as pc
            inner join base_person as p on pc.pid=p.pid and pc.eid=p.eid 
            where p.eid='$eid' and pc.oid ='$oid' and pc.perid = '$perid' and pc.escid='$escid' and pc.turno='$turno'
            and pc.subid='$subid' and pc.courseid='$courseid' and pc.curid='$curid' 
            and pc.is_main='S' and  not p.pid='TEMP01' order by pc.is_main desc";

            $sql=$this->_db->query($str);
                return $sql->fetchAll(); 
            return false;        
        }  catch (Exception $ex){
            print "Error: lista Docentes  ".$ex->getMessage();
        }

    }

}