<?php

class Api_Model_DbTable_Curricula extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_curricula';
	protected $_primary = array("eid","oid","escid","subid","curid");

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['curid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: curricula ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk)
	{
		try{
			if ($pk['eid']=='' ||   $pk['oid']=='' ||  $pk['escid']=='' ||  $pk['curid']=='' || $pk['subid']=='') return false;
			$where = "eid = '".$pk['eid']."'  and oid = '".$pk['oid']."' and escid = '".$pk['escid']."' and curid = '".$pk['curid']."' and subid = '".$pk['subid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Curricula".$e->getMessage();
		}
	}
	
	public function _delete($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['curid']=='') return false;
			$where = 	"eid = '".$data['eid']."' and oid='".$data['oid']."' and escid='".$data['escid']."' and subid='".$data['subid']."' and curid='".$data['curid']."' ";			
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Curricula".$e->getMessage();
		}
	}

	
	public function _getOne($where=array()){
		try{
			if ($where['eid']=='' ||  $where['oid']=='' || $where['escid']=='' || $where['subid']=='' || $where['curid']=='') return false;
			$wherestr = "eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".$where['escid']."' and subid='".$where['subid']."' and curid='".$where['curid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Curricula ".$e->getMessage();
		}
	}

	
 	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_curricula");
				else $select->from("base_curricula",$attrib);
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
			print "Error: Read Filter Curricula ".$e->getMessage();
		}
	}

	public function _getCourses($where=null,$attrib=null){
		try {
			$base_courses = new Api_Model_DbTable_Course();
			if($where=='' && $attrib=='') return false;
			 $data_courses = $base_courses->_getFilter($where,$attrib);
			 if ($data_courses) return $data_courses;
			 return false;
		} catch (Exception $e) {
			print "Error: Read Course ";
		}
	}

    public function _getAmountCourses($curid="",$subid="",$escid="",$oid="",$eid=""){
        try {
                $sql = $this->_db->query("
                    SELECT DISTINCT CAST (c.SEMID AS INTEGER),s.name FROM base_courses as c
                    inner join base_semester as s
                    on c.semid=s.semid and c.eid=s.eid and c.oid=s.oid
                    WHERE CURID='$curid' and escid='$escid' and subid='$subid' and c.state='A'
                    ORDER BY CAST(c.SEMID AS INTEGER)
                ");
                if ($sql) return $sql->fetchAll();
                return false;  
        } catch (Exception $e) {
            print "Error: Read AmountCourses".$e->getMessage();
        }
    }


//FALTA
   //      public function _getPerformance($where=null){
   //       try{
			//  $sub_select = $select = $this->_db->select()
			// ->from(array('MC' => 'BASE_REGISTRATION_COURSE'))
			// 	->join(array('M' => 'BASE_REGISTRATION'),'MC.REGID=M.REGID AND MC.PID=M.PID AND MC.ESCID=M.ESCID AND MC.UID=M.UID AND MC.PERID=M.PERID AND MC.EID=M.EID AND MC.OID=M.OID AND MC.SUBID=M.SUBID AND MC.STATE="M" AND MC.PERID=PC.PERID AND MC.CURID=PC.CURID AND MC.ESCID=PC.ESCID AND MC.COURSEID=PC.COURSEID AND MC.EID=PC.EID AND MC.OID=PC.OID AND MC.SUBID=PC.SUBID AND M.STATE="M" AND (CASE WHEN NOTAFINAL ='' THEN 0 ELSE CAST(NOTAFINAL AS INTEGER) END) > 10', 
			// 			array('COUNT' => 'COUNT(*)')); 
			 

			//  $select = $this->_db->select()->distinct()
			// ->from(array('PC' => 'BASE_PERIODS_COURSES'),array('PC.SEMID','NAME','PC.COURSEID'),array('APROBADOS'=>$sub_select))
			// 	->join(array('C' => 'BASE_COURSES'),'PC.COURSEID=C.COURSEID AND PC.ESCID=C.ESCID AND PC.CURID=C.CURID AND PC.OID=C.OID AND PC.EID=C.EID AND PC.SUBID=C.SUBID')
			// 	->where('PERID = ?', $where['PERID'])->where('PC.ESCID = ?', $where['ESCID'])->where('PC.CURID = ?', $where['CURID'])
			// 	->where("u.uid NOT IN ?", $sub_select) ;
			// $results = $select->query();			
			// $rows = $results->fetchAll();
			// if($rows) return $rows;
			// return false;	
   //       }catch (Exception $ex) {
   //           print $ex->getMessage();
   //       }
   //   }

	 public function _getPerformance($escid,$curid,$perid,$eid,$oid){
            try{
               if ($escid=="" || $curid=="" || $perid=="" || $eid=="" || $oid=="") return false;
               else
               {
                $sql=$this->_db->query(" 
			SELECT DISTINCT PC.SEMID,NAME,PC.COURSEID,
                (
                    SELECT COUNT(*) FROM BASE_REGISTRATION_COURSE AS MC
                    INNER JOIN BASE_REGISTRATION AS M
                    ON MC.REGID=M.REGID AND MC.PID=M.PID AND MC.ESCID=M.ESCID AND MC.UID=M.UID AND MC.PERID=M.PERID AND MC.EID=M.EID AND MC.OID=M.OID AND MC.SUBID=M.SUBID AND MC.STATE='M'
                    WHERE MC.PERID=PC.PERID AND MC.CURID=PC.CURID AND MC.ESCID=PC.ESCID AND MC.COURSEID=PC.COURSEID AND MC.EID=PC.EID AND MC.OID=PC.OID AND MC.SUBID=PC.SUBID AND M.STATE='M' AND (CASE WHEN NOTAFINAL ='' THEN 0 ELSE CAST(NOTAFINAL AS INTEGER) END) > 10
                ) AS APROBADOS,
                ROUND(
                    (
                    SELECT COUNT(*) FROM BASE_REGISTRATION_COURSE AS MC
                    INNER JOIN BASE_REGISTRATION AS M
                    ON MC.REGID=M.REGID AND MC.PID=M.PID AND MC.ESCID=M.ESCID AND MC.UID=M.UID AND MC.PERID=M.PERID AND MC.EID=M.EID AND MC.OID=M.OID AND MC.SUBID=M.SUBID AND MC.STATE='M'
                    WHERE MC.PERID=PC.PERID AND MC.CURID=PC.CURID AND MC.ESCID=PC.ESCID AND MC.COURSEID=PC.COURSEID AND MC.EID=PC.EID AND MC.OID=PC.OID AND MC.SUBID=PC.SUBID AND M.STATE='M' AND (CASE WHEN NOTAFINAL ='' THEN 0 ELSE CAST(NOTAFINAL AS INTEGER) END) > 10
                    ) /
                    ((
                    SELECT COUNT(*) FROM BASE_REGISTRATION_COURSE AS MC
                    INNER JOIN BASE_REGISTRATION AS M
                    ON MC.REGID=M.REGID AND MC.PID=M.PID AND MC.ESCID=M.ESCID AND MC.UID=M.UID AND MC.PERID=M.PERID AND MC.EID=M.EID AND MC.OID=M.OID AND MC.SUBID=M.SUBID AND MC.STATE='M'
                    WHERE MC.PERID=PC.PERID AND MC.CURID=PC.CURID AND MC.ESCID=PC.ESCID AND MC.COURSEID=PC.COURSEID AND MC.EID=PC.EID AND MC.OID=PC.OID AND MC.SUBID=PC.SUBID AND M.STATE='M'
                    ) * 0.01),2
                ) AS PORC_AP,
                (
                    SELECT COUNT(*) FROM BASE_REGISTRATION_COURSE AS MC
                    INNER JOIN BASE_REGISTRATION AS M
                    ON MC.REGID=M.REGID AND MC.PID=M.PID AND MC.ESCID=M.ESCID AND MC.UID=M.UID AND MC.PERID=M.PERID AND MC.EID=M.EID AND MC.OID=M.OID AND MC.SUBID=M.SUBID AND MC.STATE='M'
                    WHERE MC.PERID=PC.PERID AND MC.CURID=PC.CURID AND MC.ESCID=PC.ESCID AND MC.COURSEID=PC.COURSEID AND MC.EID=PC.EID AND MC.OID=PC.OID AND MC.SUBID=PC.SUBID AND M.STATE='M' AND (CASE WHEN NOTAFINAL ='' THEN 0 ELSE CAST(NOTAFINAL AS INTEGER) END) <= 10 AND (CASE WHEN NOTAFINAL ='' THEN 0 ELSE CAST(NOTAFINAL AS INTEGER) END) <= 10 AND (CASE WHEN NOTAFINAL ='' THEN 0 ELSE CAST(NOTAFINAL AS INTEGER) END) >= 0
                ) AS DESAPROBADOS,
                ROUND(
                    (
                    SELECT COUNT(*) FROM BASE_REGISTRATION_COURSE AS MC
                    INNER JOIN BASE_REGISTRATION AS M
                    ON MC.REGID=M.REGID AND MC.PID=M.PID AND MC.ESCID=M.ESCID AND MC.UID=M.UID AND MC.PERID=M.PERID AND MC.EID=M.EID AND MC.OID=M.OID AND MC.SUBID=M.SUBID AND MC.STATE='M'
                    WHERE MC.PERID=PC.PERID AND MC.CURID=PC.CURID AND MC.ESCID=PC.ESCID AND MC.COURSEID=PC.COURSEID AND MC.EID=PC.EID AND MC.OID=PC.OID AND MC.SUBID=PC.SUBID AND M.STATE='M' AND (CASE WHEN NOTAFINAL ='' THEN 0 ELSE CAST(NOTAFINAL AS INTEGER) END) <= 10 AND (CASE WHEN NOTAFINAL ='' THEN 0 ELSE CAST(NOTAFINAL AS INTEGER) END) <= 10 AND (CASE WHEN NOTAFINAL ='' THEN 0 ELSE CAST(NOTAFINAL AS INTEGER) END) >= 0
                    ) /
                    ((
                    SELECT COUNT(*) FROM BASE_REGISTRATION_COURSE AS MC
                    INNER JOIN BASE_REGISTRATION AS M
                    ON MC.REGID=M.REGID AND MC.PID=M.PID AND MC.ESCID=M.ESCID AND MC.UID=M.UID AND MC.PERID=M.PERID AND MC.EID=M.EID AND MC.OID=M.OID AND MC.SUBID=M.SUBID AND MC.STATE='M'
                    WHERE MC.PERID=PC.PERID AND MC.CURID=PC.CURID AND MC.ESCID=PC.ESCID AND MC.COURSEID=PC.COURSEID AND MC.EID=PC.EID AND MC.OID=PC.OID AND MC.SUBID=PC.SUBID AND M.STATE='M'
                    )*0.01),2
                ) AS PORC_DESAP,
                (
                    SELECT COUNT(*) FROM BASE_REGISTRATION_COURSE AS MC
                    INNER JOIN BASE_REGISTRATION AS M
                    ON MC.REGID=M.REGID AND MC.PID=M.PID AND MC.ESCID=M.ESCID AND MC.UID=M.UID AND MC.PERID=M.PERID AND MC.EID=M.EID AND MC.OID=M.OID AND MC.SUBID=M.SUBID AND MC.STATE='M'
                    WHERE MC.PERID=PC.PERID AND MC.CURID=PC.CURID AND MC.ESCID=PC.ESCID AND MC.COURSEID=PC.COURSEID AND MC.EID=PC.EID AND MC.OID=PC.OID AND MC.SUBID=PC.SUBID AND M.STATE='M' AND (CASE WHEN NOTAFINAL ='' THEN 0 ELSE CAST(NOTAFINAL AS INTEGER) END) = -3
                ) AS RETIRADOS,
                ROUND(
                    (
                    SELECT COUNT(*) FROM BASE_REGISTRATION_COURSE AS MC
                    INNER JOIN BASE_REGISTRATION AS M
                    ON MC.REGID=M.REGID AND MC.PID=M.PID AND MC.ESCID=M.ESCID AND MC.UID=M.UID AND MC.PERID=M.PERID AND MC.EID=M.EID AND MC.OID=M.OID AND MC.SUBID=M.SUBID AND MC.STATE='M'
                    WHERE MC.PERID=PC.PERID AND MC.CURID=PC.CURID AND MC.ESCID=PC.ESCID AND MC.COURSEID=PC.COURSEID AND MC.EID=PC.EID AND MC.OID=PC.OID AND MC.SUBID=PC.SUBID AND M.STATE='M' AND (CASE WHEN NOTAFINAL ='' THEN 0 ELSE CAST(NOTAFINAL AS INTEGER) END) = -3
                    ) /
                    ((
                    SELECT COUNT(*) FROM BASE_REGISTRATION_COURSE AS MC
                    INNER JOIN BASE_REGISTRATION AS M
                    ON MC.REGID=M.REGID AND MC.PID=M.PID AND MC.ESCID=M.ESCID AND MC.UID=M.UID AND MC.PERID=M.PERID AND MC.EID=M.EID AND MC.OID=M.OID AND MC.SUBID=M.SUBID AND MC.STATE='M'
                    WHERE MC.PERID=PC.PERID AND MC.CURID=PC.CURID AND MC.ESCID=PC.ESCID AND MC.COURSEID=PC.COURSEID AND MC.EID=PC.EID AND MC.OID=PC.OID AND MC.SUBID=PC.SUBID AND M.STATE='M'
                    ) * 0.01),2
                ) AS PORC_RET,
                (
                    SELECT COUNT(*) FROM BASE_REGISTRATION_COURSE AS MC
                    INNER JOIN BASE_REGISTRATION AS M
                    ON MC.REGID=M.REGID AND MC.PID=M.PID AND MC.ESCID=M.ESCID AND MC.UID=M.UID AND MC.PERID=M.PERID AND MC.EID=M.EID AND MC.OID=M.OID AND MC.SUBID=M.SUBID AND MC.STATE='M'
                    WHERE MC.PERID=PC.PERID AND MC.CURID=PC.CURID AND MC.ESCID=PC.ESCID AND MC.COURSEID=PC.COURSEID AND MC.EID=PC.EID AND MC.OID=PC.OID AND MC.SUBID=PC.SUBID AND M.STATE='M'
                ) AS TOTALES
                FROM BASE_PERIODS_COURSES AS PC
                INNER JOIN BASE_COURSES AS C
                ON PC.COURSEID=C.COURSEID AND PC.ESCID=C.ESCID AND PC.CURID=C.CURID AND PC.OID=C.OID AND PC.EID=C.EID AND PC.SUBID=C.SUBID
                WHERE PERID='$PERID' AND PC.ESCID='$ESCID' AND PC.CURID='$CURID' AND
                (
                (
                    SELECT COUNT(*) FROM BASE_REGISTRATION_COURSE AS MC
                    INNER JOIN BASE_REGISTRATION AS M
                    ON MC.REGID=M.REGID AND MC.PID=M.PID AND MC.ESCID=M.ESCID AND MC.UID=M.UID AND MC.PERID=M.PERID AND MC.EID=M.EID AND MC.OID=M.OID AND MC.SUBID=M.SUBID AND MC.STATE='M'
                    WHERE MC.PERID=PC.PERID AND MC.CURID=PC.CURID AND MC.ESCID=PC.ESCID AND MC.COURSEID=PC.COURSEID AND MC.EID=PC.EID AND MC.OID=PC.OID AND MC.SUBID=PC.SUBID AND M.STATE='M'
                )>0 
                )
                ORDER BY SEMID,COURSEID
            ");
            return $sql->fetchAll(); 
               }
            }  catch (Exception $ex){
                print "Error: Lecturando cursos con estadisticas de rendimientos ".$ex->getMessage();
            }
        }

    public function _getSemesterXCurricula($curid="",$subid="",$escid="",$oid="",$eid="")
    {
        try
        {            
            $sql = $this->_db->query("
            SELECT DISTINCT CAST (c.SEMID AS INTEGER),s.name FROM base_courses as c
            inner join base_semester as s
            on c.semid=s.semid and c.eid=s.eid and c.oid=s.oid
            WHERE CURID='$curid' and escid='$escid' and subid='$subid' and c.eid='$eid' and c.oid='$oid' and c.state='A'
            ORDER BY CAST(c.SEMID AS INTEGER)
            ");
            if ($sql) return $sql->fetchAll();
            return false;           
        }  
        catch (Exception $ex)
        {
            print "Error: Obteniendo datos de tabla 'Matricula Curso'".$ex->getMessage();
        }
    }

    public function _getCurriculaAnterior($curid="",$escid="")
    {
        try
        {            
            $sql = $this->_db->query("
            select curricula_ant('$curid','$escid');
            ");
            if ($sql) return $sql->fetchAll();
            return false;           
        }  
        catch (Exception $ex)
        {
            print "Error: Obteniendo Curricula anterior".$ex->getMessage();
        }
    }


}
