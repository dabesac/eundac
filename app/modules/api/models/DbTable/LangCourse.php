<?php
class Api_Model_DbTable_LangCourse extends Zend_Db_Table_Abstract{

	protected $_name    = 'lang_course';
	protected $_primary = array('cid', 'eid');
    
 //Retorna la lista de periodos
   	public function _getAllCourses($eid){
      	try{
          	$f=$this->fetchAll("eid='$eid'");
	          if($f) return $f->toArray(); 
	          return false;
      	}catch (Exception $e){
          	print "Error: al Mostrar  Cursos (Module)".$e->getMessage();
      	}
  	}

  	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
	        if($where['eid']=='') return false;
	          $select = $this->_db->select();
	          if ($attrib=='') $select->from("lang_course");
	          else $select->from("lang_course",$attrib);
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
	    	print "Error: Read Filter Courses ".$e->getMessage();
	    }
    }


    public function _getOne($where=array()){
		try{
			if ($where['eid']=="" || $where['cid']=='') return false;
			$wherestr="eid = '".$where['eid']."' and cid='".$where['cid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Course ".$e->getMessage();
		}
	}


    public function _save($data){
		try {	
				if ($data['eid']=='' || $data['cid'] == '' || $data['nid'] == '') return false;
				return $this->insert($data);
				return false;			
		} catch (Exception $e) {
			print "Error: Save Course".$e->getMessage();
		}
	}

	public function _update($data,$pk){
		try {
				if ($pk['eid']=='' || $pk['cid']=='') return false;
				$where = "eid = '".$pk['eid']."' and cid='".$pk['cid']."'";
				return $this->update($data, $where);
				return false;
		} catch (Exception $e) {
			print "Error: Update Course".$e->getMessage();
		}
	}


	public function _delete($pk){
		try{
			if ($pk['eid']=='' || $pk['cid']=='') return false;
			$where = "eid = '".$pk['eid']."'and cid = '".$pk['cid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Organization ".$e->getMessage();
		}
	}


  /*public function new_curso($data='')
    {
        return $this->insert($data);
    }   

  public function _getCursosx($cid)
     {
        try{
            
            $r = $this->fetchRow ("cid='$cid'");
            return $r->toArray();
            return false;            
            }  
            
           catch (Exception $ex){
            print $ex->getMessage();
        }
    }

  
    }*/

}