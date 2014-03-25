<?php

class Api_Model_DbTable_Syllabus extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_syllabus';
	protected $_primary = array("eid","oid","subid","perid","escid","curid","courseid","turno");

	public function _save($data){
		try{
			if ($data['eid']=='' || $data['oid']=='' || $data['subid']=='' || $data['perid']=='' || $data['escid']=='' || $data['curid']=='' || $data['courseid']=='' || $data['turno']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save Syllabus ".$e->getMessage();
		}
	}
	
	public function _update($data,$pk){
		try{
			if ($pk['eid']=='' || $pk['oid']=='' || $pk['subid']=='' || $pk['perid']=='' || $pk['escid']=='' || $pk['curid']=='' || $pk['courseid']=='' || $pk['turno']=='') return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and subid='".$pk['subid']."' and perid='".$pk['perid']."' and escid='".$pk['escid']."' and curid='".$pk['curid']."' and courseid='".$pk['courseid']."' and turno='".$pk['turno']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Syllabus ".$e->getMessage();
		}
	}
	
	public function _delete($data){
		try{
			if ($data['eid']=='' || $data['oid']=='' || $data['subid']=='' || $data['perid']=='' || $data['escid']=='' || $data['curid']=='' || $data['courseid']=='' || $data['turno']=='') return false;
			$where = "eid = '".$data['eid']."' and oid='".$data['oid']."' and subid='".$data['subid']."' and perid='".$data['perid']."' and escid='".$data['escid']."' and curid='".$data['curid']."' and courseid='".$data['courseid']."' and turno='".$data['turno']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Syllabus ".$e->getMessage();
		}
	}
	
	public function _getOne($where=array()){
		try{
			if ($where['eid']=='' || $where['oid']=='' || $where['subid']=='' || $where['perid']=='' || $where['escid']=='' || $where['curid']=='' || $where['courseid']=='' || $where['turno']=='') return false;
			$wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and subid='".$where['subid']."' and perid='".$where['perid']."' and escid='".$where['escid']."' and curid='".$where['curid']."' and courseid='".$where['courseid']."' and turno='".$where['turno']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Syllabus ".$e->getMessage();
		}
	}

	public function _getAll($where=null,$order='',$start=0,$limit=0){

        try {
            if($where['eid']=='' || $where['oid']=='' || $where['subid']=='' || $where['perid']=='' || $where['escid']=='' || $where['courseid']=='' || $where['turno']=='')
                $wherestr= null;
            else
                $wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and subid='".$where['subid']."' and perid='".$where['perid']."' and escid='".$where['escid']."' and courseid='".$where['courseid']."' and turno='".$where['turno']."'";
            if($limit==0) $limit=null;  
            if($start==0) $start=null;

            $rows=$this->fetchAll($wherestr,$order,$start,$limit);
            if($rows) return $rows->toArray();
            return false;

        } catch (Exception $e) {
            print "Error: Leer las facultades".$e->getMessage();           
        }
    }

    public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_syllabus");
				else $select->from("base_syllabus",$attrib);
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
			print "Error: Read Filter Base_syllabus".$e->getMessage();
		}
	}


		   /*Devuelve el r*/
  public function _getDuplicasilabo($perid,$escid,$courseid,$curid,$turno,$escidorigen,$turnoorigen){
         try{    

    		//if ($perid=='' || $escid=='' || $courseid==''|| $curid=='' || $turno=='' || $escidorigen=='' || $turnoorigen=='' ) return false;

            $sql = $this->_db->query("select * from duplicar_silabo5('$perid','$escid','$courseid','$curid','$turno','$escidorigen','$turnoorigen')");
			$row=$sql->fetchAll();
			if ($row)
			return $row;
	       	return false;   
                     
        }  catch (Exception $ex){
            print "Ehh".$ex->getMessage();
        }
    }

}
