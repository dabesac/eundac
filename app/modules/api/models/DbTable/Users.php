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
			if ($pk['eid']=='' ||  $pk['oid']=='' || $pk['escid']=='' || $pk['subid']=='' || $pk['pid']=='' ) return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."' and pid='".$pk['pid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update User".$e->getMessage();
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
			->from(array('p' => 'base_person'),array('p.pid','p.typedoc','numdoc','p.last_name0','p.last_name1','p.first_name','p.birthday','p.photografy'))
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
						array('u.uid','u.uid','u.escid','u.eid','u.oid','u.subid'))
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
  /*FALTA */
    //  public function _getUsuarioXNombre($where=null){
    //     try{
    //     	if ($where['eid']=="" || $where['oid']=="" || $where['uid']=="" || $where['rid']="" || $where['nom'] ) return false;

    //         $select = $this->_db->select()
    //         ->from(array('u' => 'base_users'),array('u.uid','u.rid','u.subid','u.eid','u.oid','u.escid','u.state'))
    //         	->join(array('p' => 'base_person'),'u.pid=p.pid and u.eid=p.eid ',array('p.first_name','p.last_name0','p.last_name1'))

    //         $row=$sql->fetchAll();
    //        return $row;  
    //     }catch (Exception $ex) {
    //         print "Error: Retornando los datos del alumno deacuerdo a una palabra ingresada".$ex->getMessage();
    //     }
    // }
    /*FALTA */
    public function _getUsuarioXNombreXsinRol($nom='',$eid='',$oid=''){
        try{
            $sql=$this->_db->query("
               select last_name0 || ' ' || last_name1 || ', ' || first_name as nombrecompleto
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



    public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_users");
				else $select->from("base_users",$attrib);
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

}
