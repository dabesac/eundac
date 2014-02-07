<?php

class Api_Model_DbTable_Registrationxcourse extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_registration_course';
	protected $_primary = array("eid","oid","escid","subid","courseid","curid" ,"perid","turno","regid","pid","uid");

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['courseid']=='' || $data['curid']=='' || $data['regid']=='' || $data['turno']=='' || $data['pid']=='' || $data['uid']=='' || $data['perid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Registration ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk)
	{
		try{

			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']=='' ||
				 $pk['regid']=='' || $pk['pid']=='' || $pk['uid']=='' || 
				$pk['perid']=='') return false;
			$where = "eid = '".$pk['eid']."' and pid='".$pk['pid']."' and oid = '".$pk['oid']
			."' and escid = '".$pk['escid']."' and uid = '".$pk['uid']."' and subid = '"
			.$pk['subid']."' and regid = '".$pk['regid']."' and perid = '".$pk['perid']
			."' ";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Registration".$e->getMessage();
		}
	}

	public function _updatenoteregister($data,$pk)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']=='' || $pk['courseid']=='' || $pk['curid']=='' || $pk['regid']=='' || $pk['turno']=='' || $pk['pid']=='' || $pk['uid']=='' || $pk['perid']=='') return false;
			$where = "eid = '".$pk['eid']."' and pid='".$pk['pid']."' and oid = '".$pk['oid']."' and escid = '".$pk['escid']."' and uid = '".$pk['uid']."' and subid = '".$pk['subid']."' and regid = '".$pk['regid']."' and perid = '".$pk['perid']."' and turno = '".$pk['turno']."' and curid = '".$pk['curid']."' and courseid = '".$pk['courseid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Registration".$e->getMessage();
		}
	}


		public function _updatestateregister($data,$pk)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']==''   || $pk['regid']==''  || $pk['pid']=='' || $pk['uid']=='' || $pk['perid']=='') return false;
			$where = "eid = '".$pk['eid']."' and oid = '".$pk['oid']."' and pid='".$pk['pid']."'  and escid = '".$pk['escid']."' and subid = '".$pk['subid']."' and regid = '".$pk['regid']."' and uid = '".$pk['uid']."'  and perid = '".$pk['perid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update State Registration Course".$e->getMessage();
		}
	}
	

	
	public function _delete($data=array())
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' 
				|| $data['courseid']=='' || $data['curid']=='' || $data['regid']=='' 
				|| $data['turno']=='' || $data['pid']=='' || $data['uid']=='' 
				|| $data['perid']=='') return false;
			// print_r($data); exit();
			$where = "eid = '".$data['eid']."' and pid='".$data['pid']."' and oid = '".$data['oid'].
			"' and escid = '".$data['escid']."' and uid = '".$data['uid']."' and subid = '".
			$data['subid']."' and regid = '".$data['regid']."' and perid = '".
			$data['perid']."' and turno = '".$data['turno']."' and curid = '".
			$data['curid']."' and courseid = '".$data['courseid']."'";			
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Registration".$e->getMessage();
		}
	}

	public function _deleteAll($data=array()){
		try {
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' 
				|| $data['regid']=='' || $data['pid']=='' || $data['uid']=='' 
				|| $data['perid']=='') return false;

			$where = "eid = '".$data['eid']."' and pid='".$data['pid']."' and oid = '".$data['oid'].
			"' and escid = '".$data['escid']."' and uid = '".$data['uid']."' and subid = '".
			$data['subid']."' and regid = '".$data['regid']."' and perid = '".
			$data['perid']."'";

			return $this->delete($where);
			return false;

			
		} catch (Exception $e) {
			
		}
	}

	public function _deletecorseregister($pk)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']==''   || $pk['regid']==''  || $pk['pid']=='' || $pk['uid']=='' || $pk['perid']=='') return false;
			$where = "eid = '".$pk['eid']."' and oid = '".$pk['oid']."' and pid='".$pk['pid']."'  and escid = '".$pk['escid']."' and subid = '".$pk['subid']."' and regid = '".$pk['regid']."' and uid = '".$pk['uid']."'  and perid = '".$pk['perid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Update State Registration Course".$e->getMessage();
		}
	}
	
	
	public function _getOne($where=array()){
		try{
			if ($where['eid']=='' ||  $where['oid']=='' || $where['escid']=='' || $where['subid']=='' || $where['courseid']=='' || $where['curid']=='' || $where['regid']=='' || $where['turno']=='' || $where['pid']=='' || $where['uid']=='' || $where['perid']=='') return false;
			$wherestr = "eid = '".$where['eid']."' and pid='".$where['pid']."' and oid = '".$where['oid']."' and escid = '".$where['escid']."' and uid = '".$where['uid']."' and subid = '".$where['subid']."' and perid = '".$where['perid']."' and turno = '".$where['turno']."' and curid = '".$where['curid']."' and courseid = '".$where['courseid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Registration".$e->getMessage();
		}
	}


	
 	public function _getFilter($where=null,$attrib=null,$orders=null){
 		try {
 			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_registration_course");
				else $select->from("base_registration_course",$attrib);
				if ($where){
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
			print "Error: Read Filter REgister_Course ".$e->getMessage();
		}
	}


	public function _getAll($where=null,$order='',$start=0,$limit=0){
		try {

			if ($where['eid']=='' ||  $where['oid']=='' || $where['escid']=='' || $where['subid']=='' 
				|| $where['curid']=='' || $where['regid']=='' || $where['pid']=='' || $where['uid']=='' || $where['perid']=='') $wherestr=null;
			else{

			$wherestr = "eid = '".$where['eid']."' and pid='".$where['pid']."' and oid = '".
				$where['oid']."' and escid = '".$where['escid']."' and uid = '".$where['uid']."' 	
				and subid = '".$where['subid']."' and perid = '".$where['perid']."' and curid = '"
				.$where['curid']."'"; 
			}
			// print_r($wherestr); exit();

			if ($limit==0) $limit=null;
			if ($start==0) $start=null;

			$rows = $this->fetchAll($wherestr,$order,$start,$limit);
			if($rows) return $rows->toArray();
			return false;
			
		} catch (Exception $e) {
			print "Error: Read All Registration Subject".$e->getMessage();
		}
	}
			

	     public function _updatestr($data,$str)
	    {
	    try
	        {  if ($data=="") return false;
	            return $this->update($data,$str); }
	    catch (Exception $ex){
	            print "Error: Actualizar RegisterCourse".$ex->getMessage();
	        }
	    }

	public function _getStudentXcoursesXescidXperiods($where=null)
	{
		try{
			if ($where['eid']=='' || $where['oid']=='' || $where['perid']=='' || $where['curid']=="" ||
				 $where['escid']=="" || $where['subid']=="" || $where['courseid']=='' || $where['turno']=='') return false;
			$select = $this->_db->select()
			->from(array('p' => 'base_person'),array('p.last_name0','p.last_name1','p.first_name'))
				->join(array('rc' => 'base_registration_course'),'rc.pid=p.pid and p.eid=rc.eid', array('rc.*'))
				->where('rc.eid = ?', $where['eid'])->where('rc.oid = ?', $where['oid'])
				->where('rc.subid = ?', $where['subid'])->where('rc.escid = ?', $where['escid'])
				->where('rc.curid = ?', $where['curid'])->where('rc.perid = ?', $where['perid'])
				->where('rc.courseid = ?', $where['courseid'])->where('rc.turno = ?', $where['turno'])
				->where('rc.state = ?','M')
				->order('p.last_name0');
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;
		}catch (Exception $e){
			print "Error: Read UserInfo ".$e->getMessage();
		}
	}
	public function _getStudentXcoursesXescidXperiods_sql($where=null)
	{
		try{
			if ($where['eid']=='' || $where['oid']=='' || $where['perid']=='' || $where['curid']=="" ||
				 $where['escid']=="" || $where['subid']=="" || $where['courseid']=='' || $where['turno']=='') return false;
			$results = $this->_db->query("
				select p.last_name0 || ' ' || p.last_name1 || ', '|| p.first_name as name_complet, rc.*from base_person p
				inner join base_registration_course rc
				on rc.pid=p.pid and p.eid=rc.eid
				where 
				rc.eid='".$where['eid']."' and rc.oid='".$where['oid']."' and
				rc.subid='".$where['subid']."' and rc.escid='".$where['escid']."' and
				rc.curid ='".$where['curid']."' and rc.perid='".$where['perid']."' and
				rc.courseid='".$where['courseid']."' and rc.turno='".$where['turno']."' and
				rc.state='M'
				order by name_complet
			");			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;
		}catch (Exception $e){
			print "Error: Read UserInfo ".$e->getMessage();
		}
	}

	   /*Devuelve el record segun la funcion Record de Notas */
    public function _getRecordNotasAlumno($escid,$uid,$eid,$oid,$subid,$pid){
         try{    
            $sql = $this->_db->query("
            	                
                select * from record_notes('$escid','$uid','$eid','$oid','$subid','$pid') AS 
                (
                    ".'escid'." character varying,
                    ".'regid'." character varying,
                    ".'perid'." character varying,
                    ".'courseid'." character varying,
                    ".'turno'." character varying, 
                    ".'name'." character varying,
                    ".'nota'." character varying,
                    ".'semid'." integer,
                    ".'curid'." character varying,
                    ".'creditos'." double precision,
                    ".'requisito'." character varying
                    )               
                ");
            // print_r($sql);
            if ($sql) return $sql->fetchAll();
            return false;           
        }  catch (Exception $ex){
            print "Error: Obteniendo datos de tabla 'Matricula Curso'".$ex->getMessage();
        }
    }


    /*Devuelve el record segun la funcion Record de Notas */
    public function _getRecordNotasAlumno_H($escid,$uid,$eid,$oid,$subid,$pid){
    	try{
    		$sql = $this->_db->query("
    				 select c.semid, c.name,rc.regid,rc.courseid, rc.curid, rc.perid, rc.turno, rc.notafinal as nota,
						c.credits as creditos
					from base_registration_course rc, base_courses c
					where 
					rc.eid=c.eid and rc.oid=c.oid and rc.escid=c.escid and rc.subid=c.subid 
    				and rc.courseid = c.courseid and rc.curid=c.curid and 
					rc.uid='$uid' and rc.escid='$escid' and rc.pid='$pid' and rc.subid='$subid'
					and rc.eid='$eid' and rc.oid='$oid' and (rc.state='M' or rc.state='C')
					order by c.semid, c.courseid, c.name
    				");
    				// print_r($sql);
            if ($sql) return $sql->fetchAll();
            return false;
        }  catch (Exception $ex){
                		print "Error: Obteniendo datos de tabla 'Matricula Curso'".$ex->getMessage();
    	}
    	}
    /*
     * 
     * */
    public function _getInfoCourse($where=null,$attrib=null,$order=null){
		try {
			if ($where=='' && $attrib=='' ) return false;
				$base_course = new Api_Model_DbTable_Course();
				$data_course = $base_course->_getFilter($where,$attrib,$order);
			if($data_course) return $data_course;
			return false;
		} catch (Exception $e) {
			print "Error: Read info Course ";

		}
	}



         /* Retorna la cantidad de alumnos matriculados deacuerdo a la escuela, curso, turno */
    public function _getCantRegistration($where=null){
         try{
                   // if ($where['eid']=='' || $where['oid']==''  || $where['courseid']=='' || $where['curid']=='' || $where['perid']=='' || $where['escid']=='' || $where['subid']=='' || $where['turno']) return false;

            $wherestr = "eid = '".$where['eid']."' and oid='".$where['oid']."' and courseid = '".$where['courseid']."'
             and curid = '".$where['curid']."' and perid = '".$where['perid']."' and escid = '".$where['escid']."'  
             and subid = '".$where['subid']."'  and turno = '".$where['turno']."' and  (state = 'M' or state = 'C')";

            $r = $this->fetchAll($wherestr);
            if ($r) return count($r->toArray());
            return false;
         }  catch (Exception $ex){
             print "Error en retornar la cantidad de alumnos matriculados".$ex->getMessage();
         }
    } 
      /* Retorna la cantidad de alumnos prematriculados deacuerdo a la escuela, curso, turno */
    public function _getCantiPreResgistration($where=null){
         try{
             // if ($where['eid']=='' || $where['oid']==''|| $where['courseid']==''||$where['curid']==''||$where['perid']==''||$where['escid']==''||$where['subid']==''||$where['turno']=='') return false;
            $wherestr = "eid = '".$where['eid']."' and oid='".$where['oid']."' and courseid = '".$where['courseid']."'
             and curid = '".$where['curid']."' and perid = '".$where['perid']."' and escid = '".$where['escid']."'  
             and subid = '".$where['subid']."'  and turno = '".$where['turno']."' and  (state = 'I')";

            $r = $this->fetchAll($wherestr);
            if ($r) return count($r->toArray());
            return false;
         }  catch (Exception $ex){
             print "Error en retornar la cantidad de alumnos Pre matriculados";
         }
     } 


    public function _getCountRegisterCourse($where=null){
    	try{
    		
    		if ($where['eid']=='' || $where['oid']=='' || $where['perid']=='' || $where['curid']=="" || 
    			$where['escid']=="" || $where['courseid']=='' || $where['turno']==''|| $where['subid']=='') return false;
    		$sql= "eid = '".$where['eid']."' and oid = '".$where['oid']."' and escid = '".$where['escid']."' 
    				and subid = '".$where['subid']."' and perid = '".$where['perid']."' and turno = '".$where['turno']."' 
    				and curid = '".$where['curid']."' and courseid = '".$where['courseid']."' and state='M'";
    		$rows = $this->fetchAll($sql);
    		if($rows) return count($rows->toArray());
    		return false;
    	}catch (Exception $ex){
    		print " Error : ".$ex->getMessage();
    	}
    }

    public function _get_total_students_x_course($where=array())
    {
	    try {

	    	if ($where['eid']== '' || $where['oid']=='' || $where['courseid'] =='' || $where['turno'] =='' ||
	    		$where['curid']=='' || $where['subid']=='' || $where['perid']=='' || $where['escid']=='') return false;
	    	$sql = $this->_db->query("
						select count(*) from base_registration_course 
                        where eid='".$where['eid']."' and oid='".$where['oid']."' and
                         perid='".$where['perid']."' and curid='".$where['curid']."' and 
                         escid='".$where['escid']."' and courseid='".$where['courseid']."' and 
                         turno='".$where['turno']."' and subid='".$where['subid']."' 
                        AND state='M'
                    ");

            if ($sql) return $sql->fetchAll();
            return false;

	    } catch (Exception $e) {
    		print " Error : _get_total_students_x_course ".$ex->getMessage();
	 	   	
	    }
    }

    public function _get_approved($where=array())
    {
	    try {
	    	if ($where['eid']=='' || $where['oid']=='' || $where['courseid'] =='' || $where['turno'] =='' ||
	    		$where['curid']=='' || $where['subid']=='' || $where['perid']=='' || $where['escid']=='') return false;
	    	
	    	$sql = $this->_db
                    ->query("
                            select count(*) from base_registration_course AS MC
							inner join base_registration as m
							 on mc.regid=m.regid and mc.pid =m.Pid and mc.escid=m.escid and 
							 mc.uid=m.uid and mc.perid=m.perid and mc.eid=m.eid and mc.oid=m.oid
							  and mc.subid=m.subid 
							  where MC.eid='".$where['eid']."' and MC.oid='".$where['oid']."' and 
							  MC.perid='".$where['perid']."' and MC.curid='".$where['curid']."' and MC.escid='".$where['escid']."' and 
							  MC.courseid='".$where['courseid']."' and MC.turno='".$where['turno']."' and MC.subid='".$where['subid']."' 
							 AND MC.state='M' 
							and (cast((case when notafinal='' or notafinal is null then '0' else 
							notafinal end) as integer)>10)        
                    ");
                if ($sql) return $sql->fetchAll();
            return false;

	    } catch (Exception $e) {
    		print " Error :  _get_disapproved_x_course".$ex->getMessage();
	 	   	
	    }
    }

    public function _get_disapproved_x_course($where=array())
    {
	    try {
	    	if ($where['eid']=='' || $where['oid']=='' || $where['courseid'] =='' || $where['turno'] =='' ||
	    		$where['curid']=='' || $where['subid']=='' || $where['perid']=='' || $where['escid']=='') return false;
	    	
	    	$sql = $this->_db
                    ->query("
                            select count(*) from base_registration_course AS MC
							inner join base_registration as m
							on mc.regid=m.regid and mc.pid =m.Pid and mc.escid=m.escid and
							 mc.uid=m.uid and mc.perid=m.perid and mc.eid=m.eid and 
							 mc.oid=m.oid and mc.subid=m.subid
							where MC.eid='".$where['eid']."' and MC.oid='".$where['oid']."' and MC.perid='".$where['perid']."' and 
							MC.curid='".$where['curid']."' and MC.escid='".$where['escid']."' and MC.courseid='".$where['courseid']."'
							and MC.turno='".$where['turno']."' and MC.subid='".$where['subid']."'  AND MC.state='M' and 
							(cast((case when notafinal='' or notafinal is null then '0'
							 else notafinal end) as integer)<=10) and not (cast((case when 
							 notafinal='' or notafinal is null then '0' else notafinal end) 
							 as integer)=-3) and not (cast((case when 
							 notafinal='' or notafinal is null then '0' else notafinal end) 
							 as integer)=-2)              
                    ");
                if ($sql) return $sql->fetchAll();
            return false;

	    } catch (Exception $e) {
    		print " Error :  _get_disapproved_x_course".$ex->getMessage();
	 	   	
	    }
    }

    public function _get_retired_x_course($where=array())
    {
	    try {
	    	if ($where['eid']=='' || $where['oid']=='' || $where['courseid'] =='' || $where['turno'] =='' ||
	    		$where['curid']=='' || $where['subid']=='' || $where['perid']=='' || $where['escid']=='') return false;
	    	
	    	$sql = $this->_db
                    ->query("
                   		select count(*) from base_registration_course AS MC
						inner join base_registration as m
						on mc.regid=m.regid and mc.pid =m.Pid and mc.escid=m.escid and 
						mc.uid=m.uid and mc.perid=m.perid and mc.eid=m.eid and mc.oid=m.oid and 
						mc.subid=m.subid 
						where MC.eid='".$where['eid']."' and MC.oid='".$where['oid']."' and 
						MC.perid='".$where['perid']."' and MC.curid='".$where['curid']."'
					    and MC.escid='".$where['escid']."' and MC.courseid='".$where['courseid']."'
					    and MC.turno='".$where['turno']."' and MC.subid='".$where['subid']."' AND MC.state='M' 
						and (cast((case when notafinal='' or notafinal is null then '0' else notafinal end) as integer)=-3)  
                    ");
                if ($sql) return $sql->fetchAll();
            return false;

	    } catch (Exception $e) {
    		print " Error :  _get_disapproved_x_course".$ex->getMessage();
	 	   	
	    }
    }

     public function _get_NSP_x_course($where=array())
    {
	    try {
	    	if ($where['eid']=='' || $where['oid']=='' || $where['courseid'] =='' || $where['turno'] =='' ||
	    		$where['curid']=='' || $where['subid']=='' || $where['perid']=='' || $where['escid']=='') return false;
	    	
	    	$sql = $this->_db
                    ->query("
                         select count(*) from base_registration_course AS MC
						inner join base_registration as m
						on mc.regid=m.regid and mc.pid =m.Pid and mc.escid=m.escid and 
						mc.uid=m.uid and mc.perid=m.perid and mc.eid=m.eid and mc.oid=m.oid and 
						mc.subid=m.subid 
						where MC.eid='".$where['eid']."' and MC.oid='".$where['oid']."' and 
						MC.perid='".$where['perid']."' and MC.curid='".$where['curid']."'
					    and MC.escid='".$where['escid']."' and MC.courseid='".$where['courseid']."'
					    and MC.turno='".$where['turno']."' and MC.subid='".$where['subid']."' AND MC.state='M' 
						and (cast((case when notafinal='' or notafinal is null then '0' else notafinal end) as integer)=-2)
                    ");
                if ($sql) return $sql->fetchAll();
            return false;

	    } catch (Exception $e) {
    		print " Error :  _get_disapproved_x_course".$ex->getMessage();
	 	   	
	    }
    }

        public function _register($where=null){
         try{
			 $select = $this->_db->select()
			 	->from(array('mc' => 'base_registration_course'),array('courseid','turno','perid'))
                ->where("perid != ?", $where['perid'])->where("uid = ?", $where['uid'])->where("notafinal != ?", '-2')->where("notafinal != ?", '-3')->where("state != ?", 'I')->where("state != ?", 'B'); 
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;	
         }catch (Exception $ex) {
             print $ex->getMessage();
         }
     }

     /*************************************/
     public function _closuretarget($where=array()){
     	try {
     		if ($where['eid']=='' || $where['oid']=='' || $where['courseid'] =='' || $where['turno'] =='' ||
	    		$where['curid']=='' || $where['subid']=='' || $where['perid']=='' || $where['escid']=='') return false;
	    	
	    	$sql = $this->_db
                    ->query("
                         	    select
		            sum(num_reg) as num_reg, 
		            sum(nota1_i) as nota1_i, 
		            sum(nota2_i) as nota2_i, 
		            sum(nota3_i) as nota3_i, 
		            sum(nota4_i) as nota4_i, 
		            sum(nota5_i) as nota5_i, 
		            sum(nota6_i) as nota6_i, 
		            sum(nota7_i) as nota7_i, 
		            sum(nota8_i) as nota8_i, 
		            sum(nota9_i) as nota9_i,
		            sum(nota1_ii) as nota1_ii, 
		            sum(nota2_ii) as nota2_ii, 
		            sum(nota3_ii) as nota3_ii, 
		            sum(nota4_ii) as nota4_ii, 
		            sum(nota5_ii) as nota5_ii, 
		            sum(nota6_ii) as nota6_ii, 
		            sum(nota7_ii) as nota7_ii, 
		            sum(nota8_ii) as nota8_ii, 
		            sum(nota9_ii) as nota9_ii
		            from
		            (
		            select
		            (
		            count(*) -
		            sum(
		            CASE 
		            WHEN promedio1 = '-3' or promedio1 = '-2' or promedio2 = '-3' or promedio2 = '-2'  THEN 1
		            ELSE 0
		            END
		            )
		            ) as num_reg ,
		            sum(
		            CASE 
		            WHEN length(nota1_i) = 0 or nota1_i is null  THEN 0
		            ELSE 1
		            END) as nota1_i, 
		            sum(
		            CASE 
		            WHEN length(nota2_i) = 0 or nota2_i is null  THEN 0
		            ELSE 1
		            END) as nota2_i, 
		            sum(
		            CASE 
		            WHEN length(nota3_i) = 0 or nota3_i is null THEN 0
		            ELSE 1
		            END) as nota3_i, 
		            sum(
		            CASE 
		            WHEN length(nota4_i) = 0 or nota4_i is null THEN 0
		            ELSE 1
		            END) as nota4_i, 
		            sum(
		            CASE 
		            WHEN length(nota5_i) = 0 or nota5_i is null THEN 0
		            ELSE 1
		            END) as nota5_i, 
		            sum(
		            CASE 
		            WHEN length(nota6_i) = 0 or nota6_i is null THEN 0
		            ELSE 1
		            END) as nota6_i, 
		            sum(
		            CASE 
		            WHEN length(nota7_i) = 0 or nota7_i is null THEN 0
		            ELSE 1
		            END) as nota7_i, 
		            sum(
		            CASE 
		            WHEN length(nota8_i) = 0 or nota8_i is null THEN 0
		            ELSE 1
		            END) as nota8_i, 
		            sum(
		            CASE 
		            WHEN length(nota9_i) = 0 or nota9_i is null  THEN 0
		            ELSE 1
		            END) as nota9_i ,
		            sum(
		            CASE 
		            WHEN length(nota1_ii) = 0 or nota1_ii is null THEN 0
		            ELSE 1
		            END) as nota1_ii, 
		            sum(CASE WHEN length(nota2_ii) = 0 or nota2_ii is null THEN 0
		            ELSE 1
		            END) as nota2_ii, 
		            sum(CASE WHEN length(nota3_ii) = 0 or nota3_ii is null THEN 0
		            ELSE 1
		            END) as nota3_ii, 
		            sum(CASE WHEN length(nota4_ii) = 0 or nota4_ii is null THEN 0
		            ELSE 1
		            END) as nota4_ii, 
		            sum(CASE WHEN length(nota5_ii) = 0 or nota5_ii is null THEN 0
		            ELSE 1
		            END) as nota5_ii, 
		            sum(CASE WHEN length(nota6_ii) = 0 or nota6_ii is null THEN 0
		            ELSE 1
		            END) as nota6_ii, 
		            sum(CASE WHEN length(nota7_ii) = 0 or nota7_ii is null THEN 0
		            ELSE 1
		            END) as nota7_ii, 
		            sum(CASE WHEN length(nota8_ii) = 0 or nota8_ii is null THEN 0
		            ELSE 1
		            END) as nota8_ii, 
		            sum(CASE WHEN length(nota9_ii) = 0 or nota9_ii is null THEN 0
		            ELSE 1
		            END) as nota9_ii
		            from base_registration_course
		            where
		            curid = '".$where['curid']."' and 
		            escid = '".$where['escid']."' and 
		            courseid ='".$where['courseid']."' and 
		            perid ='".$where['perid']."' and 
		            turno ='".$where['turno']."' and 
		           	eid = '".$where['eid']."' and
		           	oid = '".$where['oid']."' and
		            state = 'M' and  
		            subid = '".$where['subid']."' 
		            group by 
		            nota1_i, 
		            nota2_i, 
		            nota3_i, 
		            nota4_i, 
		            nota5_i, 
		            nota6_i, 
		            nota7_i, 
		            nota8_i, 
		            nota9_i,
		            nota1_ii, 
		            nota2_ii, 
		            nota3_ii, 
		            nota4_ii, 
		            nota5_ii, 
		            nota6_ii, 
		            nota7_ii, 
		            nota8_ii, 
		            nota9_ii
		            ) temporal
                    ");
                if ($sql) return $sql->fetchAll();
            return false;
     	} catch (Exception $e) {
     		print $e->getMessage();
     	}
     }

     public function _closureconpetency($where=array()){
     	try {
     		if ($where['eid']=='' || $where['oid']=='' || $where['courseid'] =='' || $where['turno'] =='' ||
	    		$where['curid']=='' || $where['subid']=='' || $where['perid']=='' || $where['escid']=='') return false;
	    	
	    	$sql = $this->_db->query("
                select
	            sum(num_reg) as num_reg, 
	            sum(nota1_i) as nota1_i, 
	            sum(nota2_i) as nota2_i, 
	            sum(nota3_i) as nota3_i,  
	            sum(nota6_i) as nota6_i, 
	            sum(nota7_i) as nota7_i, 
	            sum(nota8_i) as nota8_i, 
	            sum(nota1_ii) as nota1_ii, 
	            sum(nota2_ii) as nota2_ii, 
	            sum(nota3_ii) as nota3_ii, 
	            sum(nota6_ii) as nota6_ii, 
	            sum(nota7_ii) as nota7_ii, 
	            sum(nota8_ii) as nota8_ii
	            from
	            (
	            select
	            (
	            count(*) -
	            sum(
	            CASE 
	            WHEN nota4_i = '-3'  or  nota4_i = '-2'  or  nota4_ii = '-3'  or  nota4_ii = '-2'  THEN 1
	            ELSE 0
	            END
	            )
	            ) as num_reg ,
	            sum(CASE WHEN nota1_i  is null or length(trim(nota1_i,'')) = 0 THEN 0
	             ELSE 1
	            END) as nota1_i, 
	            sum(CASE WHEN nota2_i  is null or length(trim(nota2_i,'')) = 0 THEN 0
	             ELSE 1
	            END) as nota2_i, 
	            sum(CASE WHEN nota3_i  is null or length(trim(nota3_i,'')) = 0 THEN 0
	             ELSE 1
	            END) as nota3_i, 
	            sum(CASE WHEN nota6_i  is null or length(trim(nota6_i,'')) = 0 THEN 0
	             ELSE 1
	            END) as nota6_i, 
	            sum(CASE WHEN nota7_i  is null or length(trim(nota7_i,'')) = 0 THEN 0
	             ELSE 1
	            END) as nota7_i, 
	            sum(CASE WHEN nota8_i  is null or length(trim(nota8_i,'')) = 0 THEN 0
	             ELSE 1
	            END) as nota8_i, 
	            sum(CASE WHEN nota1_ii  is null or length(trim(nota1_ii,'')) = 0 THEN 0
	             ELSE 1
	            END) as nota1_ii, 
	            sum(CASE WHEN nota2_ii  is null or length(trim(nota2_ii,'')) = 0 THEN 0
	             ELSE 1
	            END) as nota2_ii, 
	            sum(CASE WHEN nota3_ii  is null or length(trim(nota3_ii,'')) = 0 THEN 0
	             ELSE 1
	            END) as nota3_ii,  
	            sum(CASE WHEN nota6_ii  is null or length(trim(nota6_ii,'')) = 0 THEN 0
	             ELSE 1
	            END) as nota6_ii, 
	            sum(CASE WHEN nota7_ii  is null or length(trim(nota7_ii,'')) = 0 THEN 0
	             ELSE 1
	            END) as nota7_ii, 
	            sum(CASE WHEN nota8_ii  is null or length(trim(nota8_ii,'')) = 0 THEN 0
	             ELSE 1
	            END) as nota8_ii
	            from base_registration_course
	            where
	            curid = '".$where['curid']."' and 
	            escid = '".$where['escid']."' and 
	            courseid ='".$where['courseid']."' and 
	            perid ='".$where['perid']."' and 
	            turno ='".$where['turno']."' and 
	            eid = '".$where['eid']."' and 
	            oid= '".$where['oid']."' and
	            state = 'M' and  
	            subid = '".$where['subid']."' 
	            group by 
	            nota1_i, 
	            nota2_i, 
	            nota3_i, 
	            nota6_i, 
	            nota7_i, 
	            nota8_i, 
	            nota1_ii, 
	            nota2_ii, 
	            nota3_ii,  
	            nota6_ii, 
	            nota7_ii, 
	            nota8_ii
	            ) temporal
                    ");
                if ($sql) return $sql->fetchAll();
            return false;
     	} catch (Exception $e) {
     		print $e->getMessage();
     	}
     }

     public function _generateDeferred($where=null){
     	try {
     		$perid = $where['perid'];
     		$escid = $where['escid'];
     		$perid_apla = $where['perid_apla'];
     		$sql = $this->_db->query("
            	select * from aplazados2('$perid','$escid','$perid_apla');");
            if ($sql) return $sql->fetchAll();
            return false;
     	} catch (Exception $e) {
     		print "Error: ".$e->getMessage();
     	}
     }
}
