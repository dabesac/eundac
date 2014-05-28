<?php
class Api_Model_DbTable_LangTasa extends Zend_Db_Table_Abstract{

  protected $_name = 'lang_tasa';
  protected $_primary = array('tasaid', 'eid');
  protected $_sequence = 's_lang_tasa';
    
 //Retorna la lista de periodos
   	public function _getAll($eid){
      	try{
  			$f=$this->fetchAll("eid='$eid'");
          	if($f) return $f->toArray(); 
          	return false;
      	}catch (Exception $e){
          	print "Error: al Mostrar Tasas (Module)".$e->getMessage();
      	}
  	}

  	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
	        if($where['eid']=='') return false;
	          $select = $this->_db->select();
	          if ($attrib=='') $select->from("lang_tasa");
	          else $select->from("lang_tasa",$attrib);
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
	    	print "Error: Read Filter Tasas ".$e->getMessage();
	    }
    }


    public function _getOne($where=array()){
		try{
			if ($where['eid']=="" || $where['tasaid']=='') return false;
			$wherestr="eid = '".$where['eid']."' and tasaid ='".$where['tasaid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Course ".$e->getMessage();
		}
	}


    public function _save($data){
		try {	
				if ($data['eid']=='') return false;
				return $this->insert($data);
				return false;			
		} catch (Exception $e) {
			print "Error: Save Course".$e->getMessage();
		}
	}

	public function _update($data,$pk){
		try {
				if ($pk['eid']=='' || $pk['tasaid']=='') return false;
				$where = "eid = '".$pk['eid']."' and tasaid='".$pk['tasaid']."'";
				return $this->update($data, $where);
				return false;
		} catch (Exception $e) {
			print "Error: Update Course".$e->getMessage();
		}
	}


	public function _delete($pk){
		try{
			if ($pk['eid']=='' || $pk['tasaid']=='') return false;
			$where = "eid = '".$pk['eid']."'and tasaid = '".$pk['tasaid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Tasa ".$e->getMessage();
		}
	}

}