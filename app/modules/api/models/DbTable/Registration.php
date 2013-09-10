<?php

class Api_Model_DbTable_Registration extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_registration';
	protected $_primary = array("eid","oid","escid","subid","regid","pid","uid","perid");

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['regid']=='' || $data['pid']=='' || $data['uid']=='' || $data['perid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Registration ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk)
	{
		try{

			if ($pk['eid']=='' ||   $pk['oid']=='' ||  $pk['escid']=='' ||  $pk['regid']=='' || $pk['subid']=='' || $pk['pid']=='' || $pk['uid']=='' || $pk['perid']=='') return false;
			$where = "eid = '".$pk['eid']."' and pid='".$pk['pid']."' and oid = '".$pk['oid']."' and escid = '".$pk['escid']."' and uid = '".$pk['uid']."' and subid = '".$pk['subid']."' and regid = '".$pk['regid']."' and perid = '".$pk['perid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Registration".$e->getMessage();
		}
	}
	
	public function _delete($data=array())
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || 
				$data['regid']==''|| $data['pid']==''|| $data['uid']=='' || $data['perid']=='') return false;
			$where = 	"eid = '".$data['eid']."' and oid='".$data['oid']."' and escid='".
			$data['escid']."' and subid='".$data['subid']."' and regid='".$data['regid'].
			"' and pid='".$data['pid']."' and uid='".$data['uid']."' and perid='".$data['perid']."'";			
			
			return $this->delete($where);
			return false;
			
		}catch (Exception $e){
			print "Error: Delete Registration".$e->getMessage();
		}
	}

	
	public function _getOne($where=array()){
		try{
			if ($where['eid']=='' ||  $where['oid']=='' || $where['escid']=='' || $where['subid']=='' || $where['regid']=='' || $where['pid']=='' || $where['uid']=='' || $where['perid']=='') return false;
			$wherestr = "eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".$where['escid']."' and subid='".$where['subid']."' and regid='".$where['regid']."' and pid='".$where['pid']."' and uid='".$where['uid']."' and perid='".$where['perid']."'";

			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Registration".$e->getMessage();
		}
	}

	
 public function _getFilter($where=array()){
		try{
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and escid = '".$where['escid']."' and subid='".$where['subid']."'";
			$row = $this->fetchAll($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read Filter Registration".$e->getMessage();
		}
	}

public function _getPaymentsStudent($where=null,$attrib=null,$order=null){
	try {
		if($where=='' && $attrib=='') return false;
		$base_PaymentsDetail = new Api_Model_DbTable_PaymentsDetail();
		$data_payments = $base_PaymentsDetail->_getFilter($where,$attrib,$orders);
		if($data_payments) return $data_payments;
		return false;
	} catch (Exception $e) {
		print "Error: Read PaymentStudent".$e->getMessage();
	}
}



 public function _totalSchoolEnrollment($where=null)
	{
		try{
			if ($where['eid']=='' ||  $where['oid']=='' || $where['perid']=='' || $where['facid']=='') return false;
			 $select = $this->_db->select()
			->from(array('m' => 'base_registration'),array('e.name'))
				->join(array('e' => 'base_speciality'),'e.eid=m.eid and e.oid=m.oid and m.escid=e.escid', 
						array('conteo' => 'COUNT(*)'))
				->where('perid = ?', $where['perid'])->where('facid = ?', $where['facid'])->where('m.state = ?','M')
				->where('m.oid = ?', $where['oid'])->where('m.eid = ?', $where['eid'])
				->group('e.name');
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;	
		}catch (Exception $e){
			print "Error: Read UserInfo ".$e->getMessage();
		}
		
	}

      /*Retorna el registro de un usuario activo segun la org y entidad*/
    public function _getRegister($where=null){
        try{
            $wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and uid = '".$where['uid']."' and escid = '".$where['escid']."' and perid = '".$where['perid']."' and subid='".$where['subid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
        }  catch (Exception $ex){
            print "Error: Leer informacion de Register ".$ex->getMessage();
        }
     }


    public function _get_Credits_Asignated($escid='',$curid='',$perid='',$semid=''){
    	try {

    		if ($escid==''|| $curid==''|| $perid==''|| $semid=='') return false;

	 		$sql = $this->_db->query(" select semester_creditsz('$escid','$curid','$perid','$semid') ");
			$row=$sql->fetchAll();
			if ($row) return $row;
	       	return false;
	      	// if ($sql) ; ;

    	} catch (Exception $e) {
    		print "Error: Credits Asignated".$e->getMessage();
    	}
    }

        /* Retorna los alumnos deacuerdo a un estado de matricula($estados), de toda una escuela($escidd) en un periodo($perid) */
    public function _getAlumnosXMatriculaXTodasescuelasXEstado($eid='', $oid='',$str='',$escid='',$perid='',$estados=''){
    try {
        if ($eid==''|| $oid==''|| $perid=='' || $str=='') return false;
            $sql=$this->_db->query("
            select 
            m.regid,m.semid,m.credits,m.date_register, m.state as estmat,m.perid,
            u.subid,u.uid,u.eid,u.oid,u.escid,u.pid,p.first_name,
            p.last_name0,p.last_name1
            from base_registration as m
            inner join base_users as u
            on m.uid=u.uid and m.escid=u.escid and m.pid=u.pid and m.eid=u.eid and m.oid=u.oid
            and m.subid=u.subid
            inner join base_person as p
            on u.pid=p.pid and u.eid=p.eid 
            where u.eid='$eid' and u.oid ='$oid' and u.rid='AL'  and m.perid = '$perid' and m.escid like '$escid%' and m.state = '$estados' $str
            order by u.escid,m.date_register, m.semid,m.credits 
            ");
        $r = $sql->fetchAll();
        return $r;
        }  catch (Exception $ex){
            print "Error: Retornando los alumnos de una escuela en un periodo".$ex->getMessage();
        }
    }
      

          /* Retorna los alumnos deacuerdo a su estado de matricula($estados), escuela($escid) y periodo($perid) */
    public function _getAlumnosXMatriculaXEstado($eid='',$oid='',$str='',$escid='',$perid='',$estados=''){
    try {
        if ($eid==''|| $oid==''|| $perid=='' || $str=='') return false;
            $sql=$this->_db->query("
            select 
            m.regid,m.semid,m.credits,m.date_register, m.state as estmat,m.perid,
            u.subid,u.uid,u.eid,u.oid,u.escid,u.pid,p.first_name,
            p.last_name0,p.last_name1
            from base_registration as m
            inner join base_users as u
            on m.uid=u.uid and m.escid=u.escid and m.pid=u.pid and m.eid=u.eid and m.oid=u.oid
            and m.subid=u.subid
            inner join base_person as p
            on u.pid=p.pid and u.eid=p.eid
            where u.eid='$eid' and u.oid ='$oid' and u.rid='AL'  and m.perid = '$perid' and m.escid='$escid' and m.state = '$estados' $str
            order by u.escid,m.semid,m.credits 
            ");
        $r = $sql->fetchAll();
        return $r;
        }  catch (Exception $ex){
            print "Error: Retornando los alumnos deacuerdo al estado de matricula ingresado, escuela y periodo".$ex->getMessage();
        }
    }

        
    public function _getSpecialityXPeriodsXMat($where=null)
    {
        try
        {
            $sql=$this->_db->query("
            select m.escid,name from base_registration as m inner join base_speciality as e
            on m.escid=e.escid where perid='".$where['perid']."' and m.eid='".$where['eid']."' and m.oid='".$where['oid']."'
            group by m.escid,name order by m.escid;
           ");
           
           $row=$sql->fetchAll();
           return $row;  
        }
        catch (Exception $ex) 
        {
            print $ex->getMessage();
        }
    }


    public function _getFacultyXPeriodsXMat($where=null)
    {
        try
        {
            $sql=$this->_db->query("
            select left(m.escid,1) as facid,name from base_registration as m inner join base_faculty as f
            on left(m.escid,1)=f.facid  where perid='".$where['perid']."' and m.eid='".$where['eid']."' and m.oid='".$where['oid']."'
            group by left(m.escid,1),name order by left(m.escid,1);
           ");
           
           $row=$sql->fetchAll();
           return $row;  
        }
        catch (Exception $ex) 
        {
            print $ex->getMessage();
        }
    }
    
        public function _getSubsidiaryXPeriodsXMat($where=null)
    {
        try
        {
            $sql=$this->_db->query("
            select m.subid,name from base_registration as m inner join base_subsidiary as s on m.subid=s.subid
            where perid='".$where['perid']."' and m.eid='".$where['eid']."' and m.oid='".$where['oid']."'
            group by m.subid,name order by m.subid;
           ");
           $row=$sql->fetchAll();
           return $row;  
        }
        catch (Exception $ex) 
        {
            print $ex->getMessage();
        }
    }
  

      public function _getTotalMatXFacultadesXPerXEst($eid='',$oid='',$state='',$perid='',$facid='')
    {
        try
        {
             $select = $this->_db->select()
            ->from(array('m' => 'base_registration'),array('COUNT(*) as totmat'))
                ->where('perid = ?', $perid)->where('state = ?', $state)->where('left(escid,1) = ?',$facid)
                ->where('oid = ?', $oid)->where('eid = ?', $eid);
            $results = $select->query();            
            $rows = $results->fetchAll();
            if($rows) return $rows;
            return false;   
        }
        catch (Exception $ex) 
        {
            print $ex->getMessage();
        }
    }


        public function _getTotalMatXEscuelasXPerXEst($eid='',$oid='',$state='',$perid='',$escid='')
    {
        try
        {
            $select = $this->_db->select()
            ->from(array('m' => 'base_registration'),array('COUNT(*) as totmat'))
                ->where('perid = ?', $perid)->where('state = ?', $state)->where('escid = ?',$escid)
                ->where('oid = ?', $oid)->where('eid = ?', $eid);
            $results = $select->query();            
            $rows = $results->fetchAll();
            if($rows) return $rows;
            return false;    
        }
        catch (Exception $ex) 
        {
            print $ex->getMessage();
        }
    }


        public function _getTotalMatXSedesXPerXEst($eid='',$oid='',$state='',$perid='',$subid='')
    {
        try
        {
             $select = $this->_db->select()
            ->from(array('m' => 'base_registration'),array('COUNT(*) as totmat'))
                ->where('perid = ?', $perid)->where('state = ?', $state)->where('subid = ?',$subid)
                ->where('oid = ?', $oid)->where('eid = ?', $eid);
            $results = $select->query();            
            $rows = $results->fetchAll();
            if($rows) return $rows;
            return false;   
        }
        catch (Exception $ex) 
        {
            print $ex->getMessage();
        }
    }




       



}
