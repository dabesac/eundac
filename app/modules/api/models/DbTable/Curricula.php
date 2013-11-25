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

    // public function _getCurriculasXSchoolXstateAT($eid='',$oid='',$escid='')
    // {
    //     try
    //     {
    //         if ($oid=="" || $eid=="" ||  $escid=="" ) return false;
    //         $f = $this->fetchAll("eid='$eid' and oid='$oid' and escid='$escid' and (estado='A' or estado='T') ");
    //         if ($f) return $f->toArray ();
    //         return false;
    //     }
    //     catch (Exception $e)
    //     {
    //         print "Error: Al momento de leer las curriculas de escuela con estado A y T".$e->getMessage();
    //     }
    // }

    public function _getCurriculasXSchoolXstateAT($where=null,$order='',$start=0,$limit=0){
        try{
            if($where['eid']=='' || $where['oid']=='' || $where['escid']=='')
                $wherestr=null;
            else
                $wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".$where['escid']."' and (state='A' or state='T')";
            if ($limit==0) $limit=null;
            if ($start==0) $start=null;
            
            $rows=$this->fetchAll($wherestr,$order,$start,$limit);
            if($rows) return $rows->toArray();
            return false;
        }catch (Exception $e){
            print "Error: Read All Course ".$e->getMessage();
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
				if ($where ){
					foreach ($where as $atri=>$value){
						$select->where("$atri = ?", $value);
					}
				}
				if ($orders){
					foreach ($orders as $key => $order) {
							$select->order($order);
					}
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

        public function _getSemesterXcurr($where=null)
        {
            //print(utf8_encode($perid).utf8_decode($escid).utf8_decode($curid).$eid.$oid);
            try{
                if ($where['perid']=="" || $where['escid']=="" || $where['curid']=="" || $where['eid']=="" || $where['oid']=="") return false;
                else
                {
                    $sql=$this->_db->query(" 
                    select distinct curid,cast(semid as integer),perid,escid from base_periods_courses
                    where perid='".$where['perid']."' AND ESCID='".$where['escid']."' AND CURID='".$where['curid']."'  AND eid='".$where['eid']."' AND oid='".$where['oid']."'
                    ORDER BY SEMID asc
                    ");
               return $sql->fetchAll(); 
                }
            }  catch (Exception $ex){
                print "Error: Lecturando semestre de curricula ".$ex->getMessage();
            }   
                       
        }


	 public function _getPerformance($where=null){
            try{
               if ($where['escid']=="" || $where['curid']=="" || $where['perid']=="" || $where['eid']=="" || $where['oid']=="") return false;
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
                WHERE PERID='".$where['perid']."' AND PC.ESCID='".$where['escid']."' AND PC.CURID='".$where['curid']."' AND
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
    //Lista las curriculas usadas por escuela de un determinado periodo
    public function _getCurriculasXSchool($where=null)
    {
        try
        {
            if ($where['escid']=="" || $where['perid']=="" || $where['eid']=="" || $where['oid']=="") return false;
            $sql=$this->_db->query(" 
                select curid,name,escid,state from base_curricula 
                where escid='".$where['escid']."'  and curid in (select curid from base_periods_courses
                where perid='".$where['perid']."' and escid='".$where['escid']."')
            ");
            return $sql->fetchAll(); 
        }  
        catch (Exception $ex)
        {
            print $ex->getMessage();
        }
    }
            public function _get3superiorXcurricula($where=null)
        {
            //print(utf8_encode($perid).utf8_decode($escid).utf8_decode($curid).$eid.$oid);
            try{
                if ($where['perid']=="" || $where['escid']==""|| $where['eid']==""|| $where['oid']=="") return false;
                else
                {
                    $perid=$where['perid'];
                    $escid=$where['escid'];
                    $sql=$this->_db->query(" 
                   SELECT REGID,M.UID,LAST_NAME0,LAST_NAME1,FIRST_NAME,SEMID,
                    ROUND(
                    (
                    SELECT SUM(CAST((CASE WHEN NOTAFINAL='-3' THEN '0' WHEN NOTAFINAL='' THEN '0' ELSE NOTAFINAL END) AS INTEGER) * CREDITS) from BASE_REGISTRATION_COURSE AS MC1
                    INNER JOIN BASE_COURSES AS C1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    WHERE REGID=M.REGID AND CAST(SEMID AS INTEGER)=M.SEMID AND MC1.PERID=M.PERID
                    ) /
                    (
                    SELECT SUM(CREDITS) FROM BASE_REGISTRATION_COURSE AS MC1
                    INNER JOIN BASE_COURSES AS C1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    WHERE REGID=M.REGID AND CAST(SEMID AS INTEGER)=M.SEMID AND MC1.PERID=M.PERID
                    )
                    ) AS PROM_POND,
                    (
                    SELECT SUM(CREDITS) FROM BASE_REGISTRATION_COURSE AS MC1
                    INNER JOIN BASE_COURSES AS C1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    WHERE REGID=M.REGID AND CAST(SEMID AS INTEGER)=M.SEMID AND MC1.PERID=M.PERID AND CAST((CASE WHEN NOTAFINAL='' THEN '0' ELSE NOTAFINAL END) AS INTEGER) > 10
                    ) AS CRED_APROB
                    FROM BASE_REGISTRATION AS M
                    INNER JOIN BASE_PERSON AS P
                    ON M.PID=P.PID AND M.STATE='M' INNER JOIN BASE_STUDENT_CURRICULA AS AC
                    ON M.UID=AC.UID AND M.ESCID=AC.ESCID
                    WHERE PERID='$perid' AND M.ESCID='$escid' AND (SELECT SUM(CREDITS) FROM BASE_REGISTRATION_COURSE AS MC1
                    INNER JOIN BASE_COURSES AS C1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    WHERE REGID=M.REGID AND CAST(SEMID AS INTEGER)=M.SEMID AND MC1.PERID=M.PERID AND 
                    CAST((CASE WHEN NOTAFINAL='' THEN '0' ELSE NOTAFINAL END) AS INTEGER) > 10)>=(SELECT sum(credits) FROM BASE_COURSES WHERE ESCID=M.ESCID AND cast(semid as integer)=m.semid AND CURID=AC.CURID )
                    AND ROUND(
                    (
                    SELECT SUM(CAST((CASE WHEN NOTAFINAL='-3' THEN '0' WHEN NOTAFINAL='' THEN '0' ELSE NOTAFINAL END) AS INTEGER) * CREDITS) from BASE_REGISTRATION_COURSE AS MC1
                    INNER JOIN BASE_COURSES AS C1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    WHERE REGID=M.REGID AND CAST(SEMID AS INTEGER)=M.SEMID AND MC1.PERID=M.PERID
                    ) /
                    (
                    SELECT SUM(CREDITS) FROM BASE_REGISTRATION_COURSE AS MC1
                    INNER JOIN BASE_COURSES AS C1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    WHERE REGID=M.REGID AND CAST(SEMID AS INTEGER)=M.SEMID AND MC1.PERID=M.PERID
                    )
                    ) > 12
                    ORDER BY PROM_POND DESC ,LAST_NAME0,LAST_NAME1,FIRST_NAME  LIMIT (SELECT ROUND(COUNT(*)*0.3) FROM BASE_REGISTRATION AS M
                    INNER JOIN BASE_PERSON AS P
                    ON M.PID=P.PID AND M.STATE='M' INNER JOIN BASE_STUDENT_CURRICULA AS AC
                    ON M.UID=AC.UID AND M.ESCID=AC.ESCID
                    WHERE PERID='$perid' AND M.ESCID='$escid'  
                    AND (SELECT SUM(CREDITS) FROM BASE_REGISTRATION_COURSE AS MC1
                    INNER JOIN BASE_COURSES AS C1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    WHERE REGID=M.REGID AND CAST(SEMID AS INTEGER)=M.SEMID AND MC1.PERID=M.PERID AND 
                    CAST((CASE WHEN NOTAFINAL='' THEN '0' ELSE NOTAFINAL END) AS INTEGER) > 10)>=(SELECT sum(credits) FROM BASE_COURSES WHERE ESCID=M.ESCID AND cast(semid as integer)=m.semid AND CURID=AC.CURID ) AND
                    ROUND(
                    (
                    SELECT SUM(CAST((CASE WHEN NOTAFINAL='-3' THEN '0' WHEN NOTAFINAL='' THEN '0' ELSE NOTAFINAL END) AS INTEGER) * CREDITS) from BASE_REGISTRATION_COURSE AS MC1
                    INNER JOIN BASE_COURSES AS C1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    WHERE REGID=M.REGID AND CAST(SEMID AS INTEGER)=M.SEMID AND MC1.PERID=M.PERID
                    ) /
                    (
                    SELECT SUM(CREDITS) FROM BASE_REGISTRATION_COURSE AS MC1
                    INNER JOIN BASE_COURSES AS C1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    WHERE REGID=M.REGID AND CAST(SEMID AS INTEGER)=M.SEMID AND MC1.PERID=M.PERID
                    )
                    ) > 12)
                    OFFSET 0            
                    ");                
                 return $sql->fetchAll(); 
                }
            }  catch (Exception $ex){
                ///print "Error: Lecturando semestre de curricula ".$ex->getMessage();
                ?> 
                <script type="text/javascript"> 
                        alert("Para mostrar registro Falta ingresar notas de los direrentes cursos"); 
                </script> 
                <?php
            }   
                       
        }


        public function _get5superiorXcurricula($where=null)
        {
            //print(utf8_encode($perid).utf8_decode($escid).utf8_decode($curid).$eid.$oid);
            try{
                if ($where['perid']=="" || $where['escid']==""|| $where['eid']==""|| $where['oid']=="") return false;
                else
                {
                    $perid=$where['perid'];
                    $escid=$where['escid'];
                    $sql=$this->_db->query(" 
                   SELECT REGID,M.UID,LAST_NAME0,LAST_NAME1,FIRST_NAME,SEMID,
                    ROUND(
                    (
                    SELECT SUM(CAST((CASE WHEN NOTAFINAL='-3' THEN '0' WHEN NOTAFINAL='' THEN '0' ELSE NOTAFINAL END) AS INTEGER) * CREDITS) from BASE_REGISTRATION_COURSE AS MC1
                    INNER JOIN BASE_COURSES AS C1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    WHERE REGID=M.REGID AND CAST(SEMID AS INTEGER)=M.SEMID AND MC1.PERID=M.PERID
                    ) /
                    (
                    SELECT SUM(CREDITS) FROM BASE_REGISTRATION_COURSE AS MC1
                    INNER JOIN BASE_COURSES AS C1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    WHERE REGID=M.REGID AND CAST(SEMID AS INTEGER)=M.SEMID AND MC1.PERID=M.PERID
                    )
                    ) AS PROM_POND,
                    (
                    SELECT SUM(CREDITS) FROM BASE_REGISTRATION_COURSE AS MC1
                    INNER JOIN BASE_COURSES AS C1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    WHERE REGID=M.REGID AND CAST(SEMID AS INTEGER)=M.SEMID AND MC1.PERID=M.PERID AND CAST((CASE WHEN NOTAFINAL='' THEN '0' ELSE NOTAFINAL END) AS INTEGER) > 10
                    ) AS CRED_APROB
                    FROM BASE_REGISTRATION AS M
                    INNER JOIN BASE_PERSON AS P
                    ON M.PID=P.PID AND M.STATE='M' INNER JOIN BASE_STUDENT_CURRICULA AS AC
                    ON M.UID=AC.UID AND M.ESCID=AC.ESCID
                    WHERE PERID='$perid' AND M.ESCID='$escid' AND (SELECT SUM(CREDITS) FROM BASE_REGISTRATION_COURSE AS MC1
                    INNER JOIN BASE_COURSES AS C1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    WHERE REGID=M.REGID AND CAST(SEMID AS INTEGER)=M.SEMID AND MC1.PERID=M.PERID AND 
                    CAST((CASE WHEN NOTAFINAL='' THEN '0' ELSE NOTAFINAL END) AS INTEGER) > 10)>=(SELECT sum(credits) FROM BASE_COURSES WHERE ESCID=M.ESCID AND cast(semid as integer)=m.semid AND CURID=AC.CURID )
                    AND ROUND(
                    (
                    SELECT SUM(CAST((CASE WHEN NOTAFINAL='-3' THEN '0' WHEN NOTAFINAL='' THEN '0' ELSE NOTAFINAL END) AS INTEGER) * CREDITS) from BASE_REGISTRATION_COURSE AS MC1
                    INNER JOIN BASE_COURSES AS C1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    WHERE REGID=M.REGID AND CAST(SEMID AS INTEGER)=M.SEMID AND MC1.PERID=M.PERID
                    ) /
                    (
                    SELECT SUM(CREDITS) FROM BASE_REGISTRATION_COURSE AS MC1
                    INNER JOIN BASE_COURSES AS C1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    WHERE REGID=M.REGID AND CAST(SEMID AS INTEGER)=M.SEMID AND MC1.PERID=M.PERID
                    )
                    ) > 12
                    ORDER BY PROM_POND DESC ,LAST_NAME0,LAST_NAME1,FIRST_NAME  LIMIT (SELECT ROUND(COUNT(*)*0.5) FROM BASE_REGISTRATION AS M
                    INNER JOIN BASE_PERSON AS P
                    ON M.PID=P.PID AND M.STATE='M' INNER JOIN BASE_STUDENT_CURRICULA AS AC
                    ON M.UID=AC.UID AND M.ESCID=AC.ESCID
                    WHERE PERID='$perid' AND M.ESCID='$escid'  
                    AND (SELECT SUM(CREDITS) FROM BASE_REGISTRATION_COURSE AS MC1
                    INNER JOIN BASE_COURSES AS C1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    WHERE REGID=M.REGID AND CAST(SEMID AS INTEGER)=M.SEMID AND MC1.PERID=M.PERID AND 
                    CAST((CASE WHEN NOTAFINAL='' THEN '0' ELSE NOTAFINAL END) AS INTEGER) > 10)>=(SELECT sum(credits) FROM BASE_COURSES WHERE ESCID=M.ESCID AND cast(semid as integer)=m.semid AND CURID=AC.CURID ) AND
                    ROUND(
                    (
                    SELECT SUM(CAST((CASE WHEN NOTAFINAL='-3' THEN '0' WHEN NOTAFINAL='' THEN '0' ELSE NOTAFINAL END) AS INTEGER) * CREDITS) from BASE_REGISTRATION_COURSE AS MC1
                    INNER JOIN BASE_COURSES AS C1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    WHERE REGID=M.REGID AND CAST(SEMID AS INTEGER)=M.SEMID AND MC1.PERID=M.PERID
                    ) /
                    (
                    SELECT SUM(CREDITS) FROM BASE_REGISTRATION_COURSE AS MC1
                    INNER JOIN BASE_COURSES AS C1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    WHERE REGID=M.REGID AND CAST(SEMID AS INTEGER)=M.SEMID AND MC1.PERID=M.PERID
                    )
                    ) > 12)
                    OFFSET 0            
           

                    ");
               return $sql->fetchAll(); 
                }
            }  catch (Exception $ex){
                ///print "Error: Lecturando semestre de curricula ".$ex->getMessage();
                ?> 
                <script type="text/javascript"> 
                        alert("Para mostrar registro Falta ingresar notas de los direrentes cursos"); 
                </script> 
                <?php
            }   
                       
        }



                public function _getPrimerospuestos($where=null)
        {
            try{
                if ($where['perid']=="" || $where['escid']=="" || $where['curid']=="" || $where['semid']=="" || $where['eid']=="" || $where['oid']="") return false;
                else
                {
                $perid=$where['perid'];
                $escid=$where['escid'];
                $curid=$where['curid'];
                $semid=$where['semid'];
                $sql=$this->_db->query(" 
               select m.UID,LAST_NAME0 || ' ' || LAST_NAME1 || ', ' || FIRST_NAME AS nom,m.semid,mc.regid,mc.curid,sum(c.credits),
                (
                    select sum(credits) from base_registration_course  as mc1
                    inner join base_courses as c1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    where regid=mc.regid and cast(semid as integer)=m.semid and mc1.curid=mc.curid and mc1.perid=mc.perid
                    and cast(notafinal as integer) > 10
                ) as cred_apr,
                ((
                (
                    select sum(cast((CASE WHEN notafinal='-3' THEN '0' ELSE NOTAFINAL END) as integer) * credits) from base_registration_course  as mc1
                    inner join base_courses as c1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    where regid=mc.regid and cast(semid as integer)=m.semid and mc1.curid=mc.curid and mc1.perid=mc.perid
                ) /
                (
                    select sum(credits) from base_registration_course  as mc1
                    inner join base_courses as c1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    where regid=mc.regid and cast(semid as integer)=m.semid and mc1.curid=mc.curid and mc1.perid=mc.perid
                ) 
                )) as prom_pon
                 from base_registration as m inner join base_registration_course as mc
                on m.regid=mc.regid and m.perid=mc.perid 
                inner join base_person  p
                on m.pid = p.pid 
                INNER JOIN base_courses AS C
                ON MC.CURID=C.CURID AND MC.COURSEID=C.COURSEID AND MC.ESCID=C.ESCID
                where mc.curid='$curid' and mc.perid='$perid' and mc.escid='$escid' and m.semid='$semid' and m.state='M'

                                group by m.uid,nom,m.semid,mc.regid,mc.curid,mc.perid
                                order by semid,case when 
                                (
                    select sum(credits) from base_registration_course  as mc1
                    inner join base_courses as c1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID
                    where regid=mc.regid and cast(semid as integer)=m.semid and mc1.curid=mc.curid and mc1.perid=mc.perid
                    and cast(notafinal as integer) > 10
                )
                                 is null then 0 else (
                    select sum(credits) from base_registration_course  as mc1
                    inner join base_courses as c1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    where regid=mc.regid  and cast(semid as integer)=m.semid and mc1.curid=mc.curid and mc1.perid=mc.perid
                    and cast(notafinal as integer) > 10 
                ) end desc,prom_pon desc
                
                    ");
               return $sql->fetchAll();
                }
            }  catch (Exception $ex){
                // print "Error:Para mostrar registro Falta ingresar notas de los direrentes cursos";
                ?> 
                <script type="text/javascript"> 
                        alert("Para mostrar registro Falta ingresar notas de los direrentes cursos"); 
                    </script> 
                <?php 
            }   
                       
        }

        public function _getPromedioPonderadoXUid($where=null)
        {
            try{
                            
                $perid=$where['perid'];
                $escid=$where['escid'];
                $curid=$where['curid'];
                $semid=$where['semid'];
                $uid=$where['uid'];

                $sql=$this->_db->query(" 
               select m.UID,LAST_NAME0 || ' ' || LAST_NAME1 || ', ' || FIRST_NAME AS nom,
                (
                    select sum(credits) from base_registration_course  as mc1
                    inner join base_courses as c1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    where regid=mc.regid and cast(semid as integer)=m.semid and mc1.curid=mc.curid and mc1.perid=mc.perid
                    and cast(notafinal as integer) > 10
                ) as cred_apr,
                ((
                (
                    select sum(cast((CASE WHEN notafinal='-3' THEN '0' ELSE NOTAFINAL END) as integer) * credits) from base_registration_course  as mc1
                    inner join base_courses as c1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    where regid=mc.regid and cast(semid as integer)=m.semid and mc1.curid=mc.curid and mc1.perid=mc.perid
                ) /
                (
                    select sum(credits) from base_registration_course  as mc1
                    inner join base_courses as c1
                    ON MC1.CURID=C1.CURID AND MC1.COURSEID=C1.COURSEID AND MC1.ESCID=C1.ESCID AND (MC1.STATE='M' or MC1.STATE='C')
                    where regid=mc.regid and cast(semid as integer)=m.semid and mc1.curid=mc.curid and mc1.perid=mc.perid
                ) 
                )) as prom_pon
                 from base_registration as m inner join base_registration_course as mc
                on m.regid=mc.regid and m.perid=mc.perid 
                inner join base_person  p
                on m.pid = p.pid 
                INNER JOIN base_courses AS C
                ON MC.CURID=C.CURID AND MC.COURSEID=C.COURSEID AND MC.ESCID=C.ESCID
                where mc.curid='$curid' and mc.perid='$perid' and mc.escid='$escid' and m.semid='$semid' and m.state='M' and m.uid='$uid'

                                group by m.uid,nom,m.semid,mc.regid,mc.curid,mc.perid
                                                
                    ");
               return $sql->fetchAll();
    
            }  catch (Exception $ex){
                print "Error:Para mostrar registro Falta ingresar notas de los direrentes cursos";
           
            }   
                       
        }


}
