<?php

class Api_Model_DbTable_CourseCompetency extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_course_competency';
	protected $_primary = array('eid', 'oid', 'escid', 'subid', 'courseid', 'curid', 'perid', 'turno');

    public function _getOne($where=array()){
        try {
            if ($where['eid']=="" || $where['oid']=="" || $where['escid']=="" || $where['subid']==""  ||
                $where['courseid '] || $where['curid']=="" || $where['perid']=="" || $where['turno']==""  
               ) return false;
            $wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and curid='".$where['curid']."' and escid='".$where['escid']."' and subid='".$where['subid']."' and courseid='".$where['courseid']."' and turno='".$where['turno']."'";
                $row = $this->fetchRow($wherestr);
                if($row) return $row->toArray();
                return false;
        } catch (Exception $e) {
            echo "Error: read one course competency".$e->getMessage();
        }
    }

}
