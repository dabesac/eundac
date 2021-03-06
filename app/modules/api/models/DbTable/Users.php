<?php

class Api_Model_DbTable_Users extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_users';
	protected $_primary = array("eid","oid","uid","escid","subid","pid");

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['pid']=='' || $data['password']=='' ) return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save User ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']=='' || $pk['pid']==''  || $pk['uid']=='') return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."' and pid='".$pk['pid']."' and uid='".$pk['uid']."'";
			// if ()){
			// 	// $campus = new Api_Model_DbTable_Campususer();
			// 	// $where_ = array("username"=>$pk['uid']);
			// 	// $datac = array("password"=> $data['password']);
			// 	// if ($campus->_update($where_,$datac)) return true;
			// }
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			// print "Error: Update User".$e->getMessage();
		}
	}
	
	public function _delete($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['pid']=='' ) return false;
			$where = 	"eid = '".$data['eid']."' and oid='".$data['oid']."' and escid='".$data['escid']."' and subid='".$data['subid']."'
						and pid='".$data['pid']."'";			
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete User".$e->getMessage();
		}
	}
	
	public function _getInfoUser($where=null)
	{
		try{
			if ($where['eid']=="" || $where['oid']=="" || $where['escid']=="" || $where['subid']=="" 
				|| $where['uid']=="" || $where['pid']=="") return false;
			
			$select = $this->_db->select()
			->from(array('p' => 'base_person'),array('p.pid','p.typedoc','numdoc','p.last_name0','p.last_name1','p.first_name','p.birthday','p.photografy','p.sex'))
				->join(array('u' => 'base_users'),'u.eid= p.eid and u.pid=p.pid', 
						array('u.uid','u.uid'))
				->where('u.eid = ?', $where['eid'])->where('u.oid = ?', $where['oid'])
				->where('u.escid = ?', $where['escid'])->where('u.subid = ?', $where['subid'])
				->where('u.uid = ?', $where['uid'])->where('u.pid = ?', $where['pid'])
				->order('last_name0');
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;
		}catch (Exception $e){
			print "Error: Read UserInfo ".$e->getMessage();
		}
		
	}
	public function _getUserXRidXPid($where=null){
        try{
            if ($where['pid']=="" || $where['rid']=="" || $where['eid']=="" || $where['oid']=="" ) return false;
			
			$select = $this->_db->select()
			->from(array('p' => 'base_person'),array('p.pid','p.typedoc','numdoc','p.last_name0','p.last_name1','p.first_name','p.birthday','p.photografy'))
				->join(array('u' => 'base_users'),'u.eid= p.eid and u.pid=p.pid', 
						array('u.uid','u.uid','u.escid','u.eid','u.oid','u.subid'))
				->where('u.eid = ?', $where['eid'])
				->where('u.oid = ?', $where['oid'])
				->where('u.pid = ?', $where['pid'])
				->where('u.rid = ?', $where['rid'])
				->order('last_name0');
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;         
        }  catch (Exception $ex){
            print "Error: Obteniendo datos de un usuario deacuerdo a su codigo y a su rol".$ex->getMessage();
        }
    }

	public function _getUserXRidXUid($where=null){
        try{
            if ($where['uid']=="" || $where['rid']=="" || $where['eid']=="" || $where['oid']=="" ) return false;
			
			$select = $this->_db->select()
			->from(array('p' => 'base_person'),array('p.pid','p.typedoc','numdoc','p.last_name0','p.last_name1','p.first_name','p.birthday','p.photografy'))
				->join(array('u' => 'base_users'),'u.eid= p.eid and u.pid=p.pid', 
						array('u.uid','u.uid','u.escid','u.eid','u.oid','u.subid'))
				->where('u.eid = ?', $where['eid'])
				->where('u.oid = ?', $where['oid'])
				->where('u.uid = ?', $where['uid'])
				->where('u.rid = ?', $where['rid'])
				->order('last_name0');
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;         
        }  catch (Exception $ex){
            print "Error: Obteniendo datos de un usuario deacuerdo a su codigo y a su rol".$ex->getMessage();
        }
    }


    public function _getUserXUid($where=null){
        try{
            if ($where['uid']=="" || $where['eid']=="" || $where['oid']=="" ) return false;
			
			$select = $this->_db->select()
			->from(array('p' => 'base_person'),array('p.pid','p.typedoc','numdoc','p.last_name0','p.last_name1','p.first_name','p.birthday','p.photografy'))
				->join(array('u' => 'base_users'),'u.eid= p.eid and u.pid=p.pid', 
						array('u.uid','u.escid','u.eid','u.oid','u.subid','u.rid','u.state'))
				->where('u.eid = ?', $where['eid'])
				->where('u.oid = ?', $where['oid'])
				->where('u.uid = ?', $where['uid'])
				->order('last_name0');
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;         
        }  catch (Exception $ex){
            print "Error: Obteniendo datos de un usuario deacuerdo a su codigo y a su rol".$ex->getMessage();
        }
    }
    public function _getUserXUid_state($where=null){
        try{
            if ($where['uid']=="" || $where['eid']=="" || $where['oid']==""  and $where['state'] =='') return false;
			
			$select = $this->_db->select()
			->from(array('p' => 'base_person'),array('p.pid','p.typedoc','numdoc','p.last_name0','p.last_name1','p.first_name','p.birthday','p.photografy'))
				->join(array('u' => 'base_users'),'u.eid= p.eid and u.pid=p.pid', 
						array('u.uid','u.escid','u.eid','u.oid','u.subid','u.rid','u.state'))
				->where('u.eid = ?', $where['eid'])
				->where('u.oid = ?', $where['oid'])
				->where('u.uid = ?', $where['uid'])
				->where('u.state = ?', $where['state'])
				->order('last_name0');
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;         
        }  catch (Exception $ex){
            print "Error: Obteniendo datos de un usuario deacuerdo a su codigo y a su rol".$ex->getMessage();
        }
    }

    public function _getUserXPid($where=null){
        try{
            if ($where['pid']=="" || $where['eid']=="" || $where['oid']=="" ) return false;
			
			$select = $this->_db->select()
			->from(array('p' => 'base_person'),array('p.pid','p.typedoc','numdoc','p.last_name0','p.last_name1','p.first_name','p.birthday','p.photografy'))
				->join(array('u' => 'base_users'),'u.eid= p.eid and u.pid=p.pid', 
						array('u.uid','u.rid','u.escid','u.eid','u.oid','u.subid','u.state'))
				->where('u.eid = ?', $where['eid'])
				->where('u.oid = ?', $where['oid'])
				->where('u.pid = ?', $where['pid'])
				->order('last_name0');
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;         
        }  catch (Exception $ex){
            print "Error: get User".$ex->getMessage();
        }
    }

    public function _getUserXUidXEscidXRid($where=null){
        try{
            if ($where['uid']=="" || $where['eid']=="" || $where['oid']=="" || $where['rid']=="" || $where['escid']=="") return false;
			
			$select = $this->_db->select()
			->from(array('p' => 'base_person'),array('p.pid','numdoc','p.last_name0','p.last_name1','p.first_name','p.birthday'))
				->join(array('u' => 'base_users'),'u.eid= p.eid and u.pid=p.pid', array('u.uid','u.uid','u.escid','u.eid','u.oid','u.subid'))
				->where('u.eid = ?', $where['eid'])
				->where('u.oid = ?', $where['oid'])
				->where('u.uid = ?', $where['uid'])
				->where('u.rid = ?', $where['rid'])
				->where('u.escid = ?', $where['escid'])
				->order('last_name0');
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;         
        }  catch (Exception $ex){
            print "Error: Obteniendo datos de un usuario deacuerdo a su codigo, rol y escuela".$ex->getMessage();
        }
    }

    public function _getUsersXEscidXRidXState($where=null){
        try{
            if ($where['eid']=="" || $where['oid']=="" || $where['rid']=="" || $where['escid']=="" || $where['state']=="") return false;
			$select = $this->_db->select()
			->from(array('p' => 'base_person'),array('p.pid','numdoc','p.last_name0','p.last_name1','p.first_name','p.birthday'))
				->join(array('u' => 'base_users'),'u.eid= p.eid and u.pid=p.pid', array('u.uid','u.escid','u.eid','u.oid','u.subid','u.state'))
				->where('u.eid = ?', $where['eid'])
				->where('u.oid = ?', $where['oid'])
				->where('u.state = ?', $where['state'])
				->where('u.rid = ?', $where['rid'])
				->where('u.escid = ?', $where['escid'])
				->order('last_name0');
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;         
        }  catch (Exception $ex){
            print "Error: Obteniendo datos de un usuario deacuerdo a su codigo, rol y escuela".$ex->getMessage();
        }
    }

 	/*----------- PENDIENTE ------*/
     public function _getUsuarioXNombre($where=null){
        try{
           	$eid=$where['eid'];
        	$rid=$where['rid'];
        	$cad=$where['nom'];
        	// print_r($whereds);
          $sql=$this->_db->query("
                select  last_name0 || ' ' || last_name1 || ', ' || first_name as nombrecompleto
       			        ,u.uid,u.rid,u.subid,u.eid,u.oid,u.escid,u.pid,p.first_name,p.last_name0,p.last_name1,u.escid,u.state 
       					from base_users as u
       					inner join base_person as p
       					on u.pid=p.pid and u.eid=p.eid
       					where u.eid='$eid'and u.rid='$rid' and upper(last_name0) || ' ' || upper(last_name1) || ', ' || upper(first_name) like '%$cad%'
       					order by p.last_name0,p.last_name1,p.first_name
            ");
            $row=$sql->fetchAll();
           return $row;  
        }catch (Exception $ex) { 
            print "Error: Retornando los datos del alumno de acuerdo a una palabra ingresada".$ex->getMessage();
        }
    }

    public function _getUsuarioXNombreProAll($where=null){
        try{
           	$eid=$where['eid'];
        	$cad=$where['name'];
        	// print_r($whereds);
          $sql=$this->_db->query("
                select  last_name0 || ' ' || last_name1 || ' ' || first_name as full_name
       			        ,u.uid, u.subid, u.rid, u.eid, u.oid, u.escid, u.pid, p.first_name, p.last_name0, p.last_name1, u.escid, u.state 
       					from base_users as u
       					inner join base_person as p
       					on u.pid=p.pid and u.eid=p.eid
       					where u.eid='$eid' and upper(last_name0) || ' ' || upper(last_name1) || ', ' || upper(first_name) like '%$cad%'
            ");
            $row=$sql->fetchAll();
           return $row;  
        }catch (Exception $ex) { 
            print "Error: Retornando los datos del alumno de acuerdo a una palabra ingresada".$ex->getMessage();
        }
    }

    public function _getUsuarioXNombreProFacultySubid($where=null){
        try{
			$eid   = $where['eid'];
			$cad   = $where['name'];
			$facid = $where['facid'];
			$subid = $where['subid'];
			$lf = strlen($facid);
        	// print_r($whereds);
          	$sql=$this->_db->query("
                select  last_name0 || ' ' || last_name1 || ' ' || first_name as full_name
       			        ,u.uid, u.subid, u.rid, u.eid, u.oid, u.escid, u.pid, p.first_name, p.last_name0, p.last_name1, u.escid, u.state 
       					from base_users as u
       					inner join base_person as p
       					on u.pid=p.pid and u.eid=p.eid
       					where u.eid='$eid' and left(u.escid ,'$lf')='$facid' and subid='$subid' and upper(last_name0) || ' ' || upper(last_name1) || ', ' || upper(first_name) like '%$cad%'
            ");
            $row=$sql->fetchAll();
           return $row;  
        }catch (Exception $ex) { 
            print "Error: Retornando los datos del alumno de acuerdo a una palabra ingresada".$ex->getMessage();
        }
    }

    public function _getUsuarioXNombreProFaculty($where=null){
        try{
			$eid   = $where['eid'];
			$cad   = $where['name'];
			$facid = $where['facid'];
			$lf = strlen($facid);
        	// print_r($whereds);
          	$sql=$this->_db->query("
                select  last_name0 || ' ' || last_name1 || ' ' || first_name as full_name
       			        ,u.uid, u.subid, u.rid, u.eid, u.oid, u.escid, u.pid, p.first_name, p.last_name0, p.last_name1, u.escid, u.state 
       					from base_users as u
       					inner join base_person as p
       					on u.pid=p.pid and u.eid=p.eid
       					where u.eid='$eid' and left(u.escid ,'$lf')='$facid' and upper(last_name0) || ' ' || upper(last_name1) || ', ' || upper(first_name) like '%$cad%'
            ");
            $row=$sql->fetchAll();
           return $row;  
        }catch (Exception $ex) { 
            print "Error: Retornando los datos del alumno de acuerdo a una palabra ingresada".$ex->getMessage();
        }
    }

    public function _getUsuarioXNombreProSchool($where=null){
        try{
			$eid   = $where['eid'];
			$cad   = $where['name'];
			$escid = $where['escid'];
			$subid = $where['subid'];
        	// print_r($whereds);
          	$sql=$this->_db->query("
                select  last_name0 || ' ' || last_name1 || ' ' || first_name as full_name
       			        ,u.uid, u.subid, u.rid, u.eid, u.oid, u.escid, u.pid, p.first_name, p.last_name0, p.last_name1, u.escid, u.state 
       					from base_users as u
       					inner join base_person as p
       					on u.pid=p.pid and u.eid=p.eid
       					where u.eid='$eid' and u.escid='$escid' and subid='$subid' and upper(last_name0) || ' ' || upper(last_name1) || ', ' || upper(first_name) like '%$cad%'
            ");
            $row=$sql->fetchAll();
           	return $row;  
        }catch (Exception $ex) { 
            print "Error: Retornando los datos del alumno de acuerdo a una palabra ingresada".$ex->getMessage();
        }
    }

    public function _getUsersXNombre($nom='',$rid='',$eid='',$oid=''){
        try{
            $sql=$this->_db->query("
               select last_name0 || ' ' || last_name1 || ', ' || first_name as nombrecompleto
               ,u.uid,u.rid,u.subid,u.eid,u.oid,u.escid,u.pid,p.first_name,p.last_name0,p.last_name1,u.escid,u.state from base_users as u
               inner join base_person as p
               on u.pid=p.pid and u.eid=p.eid
               where u.eid='$eid' and u.oid ='$oid' and u.rid='$rid' AND upper(last_name0) || ' ' || upper(last_name1) || ', ' || upper(first_name) like '%$nom%'
               order by p.last_name0,p.last_name1,p.first_name
            ");
            $row=$sql->fetchAll();
           return $row;  
        }catch (Exception $ex) {
            print "Error: Retornando los datos del alumno deacuerdo a una palabra ingresada".$ex->getMessage();
        }
    }
    

    /*FALTA */
    public function _getUsuarioXNombreXsinRol($nom='',$eid='',$oid=''){
        try{
            $sql=$this->_db->query("
               select last_name0 || ' ' || last_name1 || ', ' || first_name as full_name
               ,u.uid,u.rid,u.subid,u.eid,u.oid,u.escid,u.pid,p.first_name,p.last_name0,p.last_name1,u.escid from base_users as u
               inner join base_person as p
               on u.pid=p.pid and u.eid=p.eid
               where u.eid='$eid' and u.oid ='$oid' and (u.rid<>'AL' and u.rid<>'DC') and u.state='A' and upper(last_name0) || ' ' || upper(last_name1) || ', ' || upper(first_name) like '%$nom%'
               order by p.last_name0,p.last_name1,p.first_name
            ");
            $row=$sql->fetchAll();
           return $row;  
        }catch (Exception $ex) {
            print "Error: Retornando los datos del alumno deacuerdo a una palabra ingresada".$ex->getMessage();
        }
    }

    /*PENDIENTE */
    public function _getUserXnameXsinRolAll($nom='',$eid='',$oid=''){
        try{
            $sql=$this->_db->query("
               select last_name0 || ' ' || last_name1 || ', ' || first_name as nombrecompleto
               ,u.uid,u.rid,u.state,u.subid,u.eid,u.oid,u.escid,u.pid,p.first_name,p.last_name0,p.last_name1,u.escid from base_users as u
               inner join base_person as p
               on u.pid=p.pid and u.eid=p.eid
               where u.eid='$eid' and u.oid ='$oid' and upper(last_name0) || ' ' || upper(last_name1) || ', ' || upper(first_name) like '%$nom%'
               order by p.last_name0,p.last_name1,p.first_name
            ");
            $row=$sql->fetchAll();
           return $row;  
        }catch (Exception $ex) {
            print "Error: Retornando los datos del alumno deacuerdo a una palabra ingresada".$ex->getMessage();
        }
    }



    public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_users");
				else $select->from("base_users",$attrib);
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
			print "Error: Read Filter Users ".$e->getMessage();
		}
	}

	public function _getUserXRidXEscidAll($where=null){
		try{
			if($where['eid']=='' || $where['oid']=='' || $where['escid']=='' || $where['rid']=='') return false;
				$whereesch['eid']=$where['eid'];
				$whereesch['oid']=$where['oid'];
				$whereesch['escid']=$where['escid'];
             	$esch = new Api_Model_DbTable_Speciality();
             	$dataesch = $esch->_getFilter($whereesch,$attrib=null,$orders=null);
             	if ($dataesch) $where['escid'] = ($dataesch[0]['parent']<>"" and $where['subid']=='1901')? $dataesch[0]['parent']: $where['escid'];
				$select = $this->_db->select()
				->from(array('p' => 'base_person'),array('p.pid','numdoc','p.last_name0','p.last_name1','p.first_name','p.birthday'))
				->join(array('u' => 'base_users'),'u.eid= p.eid and u.pid=p.pid', array('u.uid','u.escid','u.eid','u.oid','u.subid','u.state'))
				->where('u.eid = ?', $where['eid'])
				->where('u.oid = ?', $where['oid'])
				->where('u.rid = ?', $where['rid'])
				->where('u.escid = ?', $where['escid'])
				->where('u.state = ?', $where['state'])
				->order('last_name0');
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;  
		}catch (Exception $e){
			print "Error: Read Users ".$e->getMessage();
		}
	}

    public function _getUsers($eid="",$oid="",$pid='',$uid='',$nom=''){
        try{        
            $sql = $this->_db->query("
  select last_name0 || ' ' || last_name1 || ', ' || first_name as nombrecompleto
  ,u.uid,u.rid,u.state,u.subid,u.eid,u.oid,u.escid,p.pid,p.first_name,p.last_name0,p.last_name1,u.escid from base_users as u 
  right  JOIN base_person as p
  on u.pid=p.pid and u.eid=p.eid
  where p.pid ='$pid' and upper(last_name0) || ' ' || upper(last_name1) || ', ' || upper(first_name) like '%$nom%'
  order by nombrecompleto
                ");
            if ($sql) return $sql->fetchAll();
            return false;           
        }catch (Exception $ex){
            print "Error: Obteniendo los nombres completos y el codigo de usuario adscrito a una escuela del rol ingresado".$ex->getMessage();
        }
    }

    public function _getEgresadosxAnioxEscuela($escid, $anio){
        try{
            $sql = $this->_db->query("
		  	select * from egresados_anio('$escid','$anio') AS 
                (
                    ".'uid'." character varying,
                    ".'pid'." character varying,
                    ".'numdoc'." character varying,
                    ".'nombre'." text,
                    ".'escid'." character varying 
                    )  
            ");
            if ($sql) return $sql->fetchAll();
            return false;           
        }catch (Exception $ex){
            print "Error: Obteniendo los Egresados de Acuerdo a Año".$ex->getMessage();
        }
    }

    public function _getGraduatedXFacultyXSchoolXPeriod($where=array()){
        try {
            if ($where['eid']=='' || $where['oid']=='' || $where['perid']=='') return false;
            $eid = $where['eid'];
            $oid = $where['oid'];
            $perid = $where['perid'];
            $facid = $where['facid'];
            if ($facid<>"") $facid = " and left(u.escid,1)='$facid'";
            else $facid = "";
            if ($where['escid']<>"") {
            	$escid = $where['escid'];
	            if ($where['left']<>"") $escid = " and left(u.escid,3)='$escid'";
	            else $escid = " and u.escid='$escid'";
            }
            else $escid = "";
            $sql = $this->_db->query("
                select pe.last_name0 || ' ' || pe.last_name1 || ', ' || pe.first_name as fullname,u.pid,u.uid,u.escid,u.eid,u.oid,u.subid,ac.curid 
                from base_users u inner join  base_person pe on pe.pid=u.pid and pe.eid=u.eid
                inner join base_student_curricula ac
                on ac.uid=u.uid and ac.escid=u.escid and ac.subid=u.subid and ac.pid=u.pid
                where u.eid='$eid' and u.oid='$oid' and u.rid='EG' $facid $escid and (select max(perid) from base_registration_course
                where uid=u.uid and escid=u.escid and subid=u.subid and curid=ac.curid and (right(perid,1)='A' or right(perid,1)='B'))='$perid'
                order by u.escid,pe.last_name0");
            $row=$sql->fetchAll();
           return $row;
        } catch (Exception $e) {
            print "Error: Obteniendo datos ".$ex->getMessage();
        }
    }

    public function _getGraduatedXFacultyXSchoolXAnio($where=array()){
        try {
            if ($where['eid']=='' || $where['oid']=='' || $where['anio']=='') return false;
            $eid = $where['eid'];
            $oid = $where['oid'];
            $anio = $where['anio'];
            // $facid = $where['facid'];
            $facid=(isset($where['facid']))?$where['facid']:null;
            if ($facid<>"") $facid = " and left(u.escid,1)='$facid'";
            else $facid = "";
            if ($where['escid']<>"") {
            	$escid = $where['escid'];
	            if (isset($where['left'])) $escid = " and left(u.escid,3)='$escid'";
	            else $escid = " and u.escid='$escid'";
            }
            else $escid = "";
            $sql = $this->_db->query("
                select pe.last_name0 || ' ' || pe.last_name1 || ', ' || pe.first_name as fullname,u.pid,u.uid,u.escid,u.eid,u.oid,u.subid,ac.curid 
                from base_users u inner join  base_person pe on pe.pid=u.pid and pe.eid=u.eid
                inner join base_student_curricula ac
                on ac.uid=u.uid and ac.escid=u.escid and ac.subid=u.subid and ac.pid=u.pid
                where u.eid='$eid' and u.oid='$oid' and u.rid='EG' $facid $escid and (select max(left(perid,2)) from base_registration_course
                where uid=u.uid and escid=u.escid and subid=u.subid and curid=ac.curid and (right(perid,1)='A' or right(perid,1)='B'))='$anio'
                order by u.escid,pe.last_name0");
            $row=$sql->fetchAll();
           return $row;
        } catch (Exception $e) {
            print "Error: Obteniendo datos ".$ex->getMessage();
        }
    }

    public function _getTotalGraduatedXFacultyXSchoolXPeriod($where=array()){
        try {
            if ($where['eid']=='' || $where['oid']=='' || $where['perid']=='') return false;
            $eid = $where['eid'];
            $oid = $where['oid'];
            $perid = $where['perid'];
            $facid = $where['facid'];
           if ($facid<>"") $facid = " and left(u.escid,1)='$facid'";
            else $facid = "";
            if ($where['escid']<>"") {
            	$escid = $where['escid'];
	            if ($where['left']<>"") $escid = " and left(u.escid,3)='$escid'";
	            else $escid = " and u.escid='$escid'";
            }
            else $escid = "";
            $sql=$this->_db->query("
                 select u.escid, count(*) from base_users u inner join base_person pe 
                 on pe.pid=u.pid and pe.eid=u.eid
                 inner join base_student_curricula ac
                 on ac.uid=u.uid and ac.escid=u.escid and ac.subid=u.subid and ac.pid=u.pid
                 where u.eid='$eid' and u.oid='$oid' and rid='EG' $facid $escid and (select  max(perid) from base_registration_course
                 where uid=u.uid and escid=u.escid and subid=u.subid and curid=ac.curid and (right(perid,1)='A' or right(perid,1)='B'))='$perid'
                 group by u.escid order by u.escid");
            $row=$sql->fetchAll();
           return $row;
        } catch (Exception $e) {
            print "Error: Obteniendo datos ".$e->getMessage();
        }
    }

    public function _getTotalGraduatedXFacultyXSchoolXAnho($where=array()){
        try {
            if ($where['eid']=='' || $where['oid']=='' || $where['anio']=='') return false;
            $eid = $where['eid'];
            $oid = $where['oid'];
            $anho = $where['anio'];
            // $facid = $where['facid'];
            $facid=(isset($where['facid']))?$where['facid']:null;
            if ($facid<>"") $facid = " and left(u.escid,1)='$facid'";
            else $facid = "";
            if ($where['escid']<>"") {
            	$escid = $where['escid'];
	            if ($where['left']<>"") $escid = " and left(u.escid,3)='$escid'";
	            else $escid = " and u.escid='$escid'";
            }
            else $escid = "";
            $sql=$this->_db->query("
                select u.escid,count(*) from base_users u inner join base_person pe 
                on pe.pid=u.pid and pe.eid=u.eid
                inner join base_student_curricula ac
                on ac.uid=u.uid and ac.escid=u.escid and ac.subid=u.subid and ac.pid=u.pid
                where u.eid='$eid' and u.oid='$oid' and rid='EG' $facid $escid and (select max(left(perid,2)) from base_registration_course
                where uid=u.uid and escid=u.escid and subid=u.subid and curid=ac.curid and (right(perid,1)='A' or right(perid,1)='B'))= '$anho'
                group by u.escid order by u.escid");
            $row=$sql->fetchAll();
           return $row;
            
        } catch (Exception $e) {
            
        }
    }
}

