<?php 
class Api_Model_DbTable_LangPeriod extends Zend_Db_Table_Abstract
{
    protected $_name = 'lang_period';
    protected $_primary = array("perid","eid");

    public function _getPeriodActive($eid='',$state='')
	  {
	    try {
	        if ($eid=='' || $state=='') 
	        {
	          return false;
	        }
	        else
	        {
	          $sql=$this->fetchRow("eid='$eid' and state='$state'");
	          if ($sql) return $sql->toArray();
	        }
	    } catch (Exception $e) {
	      print "error: periodo activo";
	    }
	  }


    public function _getAll($eid){
      	try{
          	$f=$this->fetchAll("eid='$eid'");
          	if($f) return $f->toArray(); 
          	return false;
      	}catch (Exception $e){
          	print "Error: al Mostrar  Periodos (Module)".$e->getMessage();
      	}
  	}

  	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
	        if($where['eid']=='') return false;
	          $select = $this->_db->select();
	          if ($attrib=='') $select->from("lang_period");
	          else $select->from("lang_period",$attrib);
	          foreach ($where as $atri=>$value){
	            $select->where("$atri = ?", $value);
	          }
	          if($orders){
	            foreach ($orders as $key => $order) {
	              $select->order($order);
	            } 
	          }
	          
	          $results = $select->query();
	          $rows = $results->fetchAll();
	          if ($rows) return $rows;
	          return false;
	    }catch (Exception $e){
	    	print "Error: Read Filter Persons ".$e->getMessage();
	    }
    }

  	public function _getOne($where=array()){
		try{
			if ($where['eid']=="" || $where['perid']=='') return false;
			$wherestr="eid = '".$where['eid']."' and perid='".$where['perid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Period ".$e->getMessage();
		}
	}

  	public function _save($data){
		try {	
				if ($data['eid']=='' || $data['perid']=='') return false;
				return $this->insert($data);
				return false;			
		} catch (Exception $e) {
			print "Error: Save Course".$e->getMessage();
		}
	}

	public function _update($data,$pk){
		try {
				if ($pk['eid']=='' || $pk['perid']=='') return false;
				$where = "eid = '".$pk['eid']."' and perid='".$pk['perid']."'";
				return $this->update($data, $where);
				return false;
		} catch (Exception $e) {
			print "Error: Update Course".$e->getMessage();
		}
	}

	public function _delete($pk){
		try{
			if ($pk['eid']=='' || $pk['perid']=='') return false;
			$where = "eid = '".$pk['eid']."'and perid = '".$pk['perid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Organization ".$e->getMessage();
		}
	}

}