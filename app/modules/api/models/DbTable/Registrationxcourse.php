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

	public function _getFilter($where=null,$attrib=null,$orders=null){

		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_registration_course");
				else $select->from("base_registration_course",$attrib);
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
			print "Error: Read Filter Periods_Courses ".$e->getMessage();
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
			if ($where['eid']=='' || $where['oid']=='' || $where['perid']=='' || $where['curid']=="" || $where['escid']=="" || $where['courseid']=='' || $where['turno']=='') return false;
			$select = $this->_db->select()
			->from(array('p' => 'base_person'),array('p.last_name0','p.last_name1','p.first_name'))
				->join(array('rc' => 'base_registration_course'),'rc.pid=p.pid and p.eid=rc.eid', array('rc.*'))
				->where('rc.eid = ?', $where['eid'])->where('rc.oid = ?', $where['oid'])
				->where('rc.subid = ?', $where['subid'])->where('rc.escid = ?', $where['escid'])
				->where('rc.curid = ?', $where['curid'])->where('rc.perid = ?', $where['perid'])
				->where('rc.courseid = ?', $where['courseid'])->where('rc.turno = ?', $where['turno'])
				->where('rc.state = ?','M')->orwhere('rc.state = ?','C')
				->order('p.last_name0');
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;
		}catch (Exception $e){
			print "Error: Read UserInfo ".$e->getMessage();
		}
	}

	   /*Devuelve el record segun la funcion Record de Notas */
    public function _getRecordNotasAlumno($escid,$uid){
         try{    
            $sql = $this->_db->query("
                
                select * from record_notes('$escid','$uid') AS 
                (
                    ".'escid'." character varying,
                    ".'matid'." character varying,
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
            if ($sql) return $sql->fetchAll();
            return false;           
        }  catch (Exception $ex){
            print "Error: Obteniendo datos de tabla 'Matricula Curso'".$ex->getMessage();
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

}
