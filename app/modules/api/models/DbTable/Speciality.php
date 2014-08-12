<?php

class Api_Model_DbTable_Speciality extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_speciality';
	protected $_primary = array("eid","oid","escid","subid");

	
	public function _getOne($where=array()){
		try{
			if ($where['eid']=="" || $where['oid']=="" || $where['escid']=="" || $where['subid']=="") return false;
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and escid = '".$where['escid']."' and subid='".$where['subid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Speciality ".$e->getMessage();
		}
	}

	public function _getspeciality($where=array()){
		try{
			if ($where['eid']=="" || $where['oid']=="" || $where['facid']=="" || $where['subid']=="") return false;
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and facid = '".$where['facid']."' and state = 'A' and subid='".$where['subid']."'";
			$row = $this->fetchAll($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Speciality ".$e->getMessage();
		}
	}


	public function _getAll($where=null,$order='',$start=0,$limit=0)
	{
		try{
			if ($where['eid']=='' ||  $where['oid']=='') return false;
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."'";
			if ($limit==0) $limit=null;
			if ($start==0) $start=null;			
			$rows=$this->fetchAll($wherestr,$order,$start,$limit);
			if($rows) return $rows->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read All Speciality ".$e->getMessage();
		}
	}

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_speciality");
				else $select->from("base_speciality",$attrib);
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

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' ) return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save Speciality ".$e->getMessage();
		}
	}

	public function _update($data,$pk)
	{
		try{
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']=='' ) return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Speciality".$e->getMessage();
		}
	}

	public function _delete($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' ) return false;
			$where = "eid = '".$data['eid']."' and oid='".$data['oid']."' and escid='".$data['escid']."' and subid='".$data['subid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Rol".$e->getMessage();
		}
	}


	public function _getFacspeciality($where=null){
        try{
            if ($where['oid']=="" || $where['eid']=="" || $where['escid']=="" ) return false;
			
			$select = $this->_db->select()
			->from(array('e' => 'base_speciality'),array('e.facid','e.subid','e.escid','e.abbreviation','e.parent'))
				->join(array('f' => 'base_faculty'),'e.eid= f.eid and e.oid=f.oid and e.facid=f.facid', array('f.name as nomfac'))
				->join(array('s' => 'base_subsidiary'),'e.subid=s.subid ', array('s.name as nomsed','e.name as nomesc'))
				->where('e.escid = ?', $where['escid']);
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;         
        }  catch (Exception $ex){
            print "Error: Obteniendo datos de un usuario deacuerdo a su codigo y a su rol".$ex->getMessage();
        }
    }

    //muestra escuela x facultad x sede de la facultad de educacion
    public  function _getSchoolXSecundaria($data=null){
        try{
            if ($data['eid']=='' ||  $data['oid']=='' || $data['facid']=='' || $data['subid']=='' ) return false;
            $where = "eid = '".$data['eid']."' and oid='".$data['oid']."' and facid='".$data['facid']."' and subid='".$data['subid']."' and (parent='2ES' or parent='')";
   			$row = $this->fetchAll($where);
			if($row) return $row->toArray();
			return false;
        }catch (Exception $ex){
            print "Error: Leer todas las escuelas por facultad ".$ex->getMessage();
        }   
    }

	/*Retorna la lista escuelas segun la facultad sin considerar los parent*/
    public function _getSchoolXFacultyNOTParent($where=array()){
        try{
         	if ($where['eid']=='' || $where['oid']=='' || $where['facid']=="") return false;
         	$eid = $where['eid'];
         	$oid = $where['oid'];
         	$facid = $where['facid'];
         	$sql="facid='$facid' AND eid='$eid' AND oid='$oid' AND state='A' AND (parent is null OR parent = '' OR escid = parent)";
         	$r=$this->fetchAll($sql);
         	return ($r)?$r->toArray():false;
        } catch (Exception $ex){
         	print "Error: Read ".$ex->getMessage();
        } 
    }
    //retorna la escuela de una nueva facultad
    public function _getSchoolnewFaculty($where=array()){
    	try {
    		if ($where['escid']=='') return false;

    		if ($where['escid']=='9CE') {
    			$cad="AD";
    		}
    		else{
    			$cad=substr($where['escid'],-2);
    		}
    		
    		$select = $this->_db->select()
    							->from(array('s'=>'base_speciality'))
    							->where('right(s.escid,2) = ?', $cad)
    							->where('s.escid != ?',$where['escid']);
    		$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;
    	} catch (Exception $e) {
    		print("Error: Read Get School New Faculty").$e->getMessage();
    	}
    }
}
