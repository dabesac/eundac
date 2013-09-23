<?php

class Api_Model_DbTable_Course extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_courses';
	protected $_primary = array('eid', 'oid', 'curid', 'escid', 'subid', 'courseid');


	public function _save($data){
		try {
				if ($data['eid']=='' || $data['oid']=='' || $data['curid']=='' || $data['escid']=='' || $data['subid']=='' || $data['courseid']=='' || $data['semid']=='') return false;
				return $this->insert($data);
				return false;			
		} catch (Exception $e) {
			print "Error: Save Course".$e->getMessage();
		}
	}

	public function _update($data,$pk){
		try {
				if ($pk['eid']=='' || $pk['oid']=='' || $pk['curid']=='' || $pk['escid']=='' || $pk['subid']=='' || $pk['courseid']=='') return false;
				$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and curid='".$pk['curid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."' and courseid='".$pk['courseid']."'";
				return $this->update($data, $where);
				return false;
		} catch (Exception $e) {
			print "Error: Update Course".$e->getMessage();
		}
	}

	public function _getOne($where=array()){
		try{
			if ($where['eid']==""|| $where['oid']=='' || $where['curid']=='' || $where['escid']=="" || $where['subid']=='' || $where['courseid']=='' ) return false;
			$wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and curid='".$where['curid']."' and escid='".$where['escid']."' and subid='".$where['subid']."' and courseid='".$where['courseid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Course ".$e->getMessage();
		}
	}



	public function _getAll($where=null,$order='',$start=0,$limit=0){
		try{
			if($where['eid']=='' || $where['oid']=='' || $where['curid']=='' || $where['escid']=='' || $where['subid']=='')
				$wherestr=null;
			else
				$wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and curid='".$where['curid']."' and escid='".$where['escid']."' and subid='".$where['subid']."'";
			if ($limit==0) $limit=null;
			if ($start==0) $start=null;
			
			$rows=$this->fetchAll($wherestr,$order,$start,$limit);
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
				if ($attrib=='') $select->from("base_courses");
				else $select->from("base_courses",$attrib);
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
			print "Error: Read Filter Course ".$e->getMessage();
		}
	}

	    /* Retorna el nro de veces que llevo un curso un alumno  */
    public function _getCoursesXStudentXV($where=null){
        if ($where['escid']==''|| $where['uid']==''|| $where['curid']=='' || $where['courseid']=='') return false;
            $sql=$this->_db->query("
                    select llevo_course('".$where['escid']."','".$where['uid']."','".$where['curid']."','".$where['courseid']."') as veces          
               ");
        $r = $sql->fetchAll();
        return $r;
    }

    public function _getCoursesXCurriculaXShool($eid='',$oid='',$curid='',$escid='')
    {
        try
        {
            if ($eid=='' || $oid=='' || $curid=='' || $escid=='') return false;
            $str="eid='$eid' and oid='$oid' and escid='$escid' and curid='$curid' and state='A'";
            $r = $this->fetchAll($str,"cast(semid as integer),courseid");
            if ($r) return $r->toArray ();
            return false;
        }  
        catch (Exception $ex)
        {
            print "Error: Leer todos los cursos de una curricula ".$ex->getMessage();
        }
    }

    public function _getCourseLlevo($where=null){
        if ($where['escid']==''|| $where['uid']==''|| $where['curid']=='' || $where['courseid']=='') return false;
            $sql=$this->_db->query("
                    select state_llevo('".$where['escid']."','".$where['uid']."','".$where['curid']."','".$where['courseid']."') as apto          
               ");
        $r = $sql->fetchAll();
        return $r;
    }


        public function _getCountCoursesxSemester($where)
    {
       try
       {
       	    if ($where['escid']==''|| $where['curid']=='') return false;

            $sql=$this->_db->query("
			SELECT C.SEMID,
            ((SELECT COUNT(*) CANTIDAD_CURSOS FROM BASE_COURSES
            WHERE ESCID='".$where['escid']."' AND CURID='".$where['curid']."' AND STATE='A' AND TYPE='O' AND SEMID=C.SEMID
            )+
            (CASE WHEN (SELECT COUNT(*) FROM BASE_COURSES 
            WHERE ESCID='".$where['escid']."' AND CURID='".$where['curid']."' AND TYPE='E'  AND STATE='A' AND SEMID=C.SEMID
            )>1 THEN 1 ELSE (SELECT COUNT(*) FROM BASE_COURSES 
            WHERE ESCID='".$where['escid']."' AND CURID='".$where['curid']."' AND TYPE='E'  AND STATE='A' AND SEMID=C.SEMID
            ) END)) AS CANTIDAD_CURSOS
            FROM BASE_COURSES AS C INNER JOIN BASE_SEMESTER AS S
            on C.EID=S.EID AND C.OID=S.OID AND  C.SEMID=S.SEMID  AND C.STATE='A'
            where C.ESCID='".$where['escid']."' AND C.CURID='".$where['curid']."' 
            group by C.SEMID order by C.SEMID
           ");
           
           $row=$sql->fetchAll();
           return $row;  
        } 
        catch (Exception $ex)
        {
            // print "Error: Leer todos los cursos de una curricula ".$ex->getMessage();
        }
    }


        public function _getCountCoursesxApproved($where)
    {
       try
       {
       	    if ($where['uid']==''|| $where['curid']=='') return false;

            $sql=$this->_db->query("
			SELECT C.SEMID, COUNT(*) CANTIDAD_CURSOS FROM BASE_COURSES AS C
            INNER JOIN  BASE_REGISTRATION_COURSE AS MC
            ON C.EID=MC.EID AND C.OID=MC.OID AND C.ESCID=MC.ESCID AND C.COURSEID=MC.COURSEID AND C.CURID=MC.CURID 
            WHERE UID='".$where['uid']."' AND CAST((CASE WHEN  NOTAFINAL='' THEN '0' ELSE NOTAFINAL END) AS INTEGER) > 10 AND MC.CURID='".$where['curid']."'
            GROUP BY C.SEMID ORDER BY C.SEMID
           ");
           
           $row=$sql->fetchAll();
           return $row;  
        } 
        catch (Exception $ex)
        {
            // print "Error: Leer todos los cursos de una curricula ".$ex->getMessage();
        }
    }
}
