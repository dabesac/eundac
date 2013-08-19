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
	
	public function _delete($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['escid']=='' || $data['subid']=='' || $data['regid']==''|| $data['pid']==''|| $data['uid']=='' || $data['perid']=='') return false;
			$where = 	"eid = '".$data['eid']."' and oid='".$data['oid']."' and escid='".$data['escid']."' and subid='".$data['subid']."' and regid='".$data['regid']."' and pid='".$data['pid']."' and uid='".$data['uid']."' and perid='".$data['perid']."'";			
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

	   //CANTIDAD DE ALUMNOS DE LAS ESCUELAS DE UNA DETERMINADA FACULTAD 
           public function _getTodasEscuelasconunt($eid,$oid,$perid,$facid){  
        try{
            $sql=$this->_db->query(" select e.name,count(*) conteo from base_registration as m  inner join base_speciality as e 
               on  e.eid=m.eid and e.oid=m.oid and m.escid=e.escid and m.state='M'
               where perid='$perid' and facid='$facid' and m.eid='$eid' and m.oid='$oid' 
               group by (e.name)
               ");

            return $sql->fetchAll(); 
            }  catch (Exception $ex){
                print $ex->getMessage();
            }            
        }

        


}
