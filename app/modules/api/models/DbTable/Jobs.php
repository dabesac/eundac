<?php

class Api_Model_DbTable_Jobs extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_jobs';
	protected $_primary = array("lid", "eid","pid");
	protected $_sequence ="s_jobs";

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['pid']=='' ) return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_jobs");
				else $select->from("base_jobs",$attrib);
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
			print "Error: Read Filter Familiars ".$e->getMessage();
		}
	}

	public function _save($data){
		try{
			if ($data['eid']=='' || $data['pid']=='') return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
			print "Error al Guardar Jobs ".$e->getMessage();
		}
	}

	public function _delete($pk){
		try{
			if ($pk['eid']=='' || $pk['pid']==''|| $pk['lid']=='') return false;
			$where = "eid = '".$pk['eid']."'and pid = '".$pk['pid']."'and lid = '".$pk["lid"]."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Interes ".$e->getMessage();
		}
	}

	public function _getCompanyXDistinct($eid="",$oid=""){
        
           try{
            if ($eid=="" || $oid=="" ) return false;
            $sql = $this->_db->query("select distinct company from base_jobs");
            if ($sql) return $sql->fetchAll();
            return false;           
        }catch (Exception $ex){
            print "Error: Leer información de docentes segun dni activos".$ex->getMessage();
        }                              
    }


    public function _getBuscarxSeleccion($nom,$f1,$f2)
	    {
	    try
	        {
	        if ($f1=="" and $f2=="") 
		        {	
			        $sql=$this->_db->query
			        (
			        "
			        select * from base_jobs
			        where  company like '%$nom%' order by company
			        ");
		    	}
		    else 		    	
		    	{	
			    	
		    		if($f1<>"") //and $f2<>"") 
		    		{

		    			if($f1==1000)
		    			{
			    			$sql=$this->_db->query
					    	(
					        "select * from base_jobs
					         where  company like '%$nom%' and (CAST(salary as decimal) <=$f1)  order by company
					        ");
				    	}

				    	if($f1==1001)
		    			{
			    			$sql=$this->_db->query
					    	(
					        "select * from base_jobs
					         where  company like '%$nom%' and (CAST(salary as decimal) >=$f1 and CAST(salary as decimal) <=$f2) order by company
					        ");
				    	}

				    	if($f1==3000)
		    			{
			    			$sql=$this->_db->query
					    	(
					        "select * from base_jobs
					         where  company like '%$nom%' and (CAST(salary as decimal) >=$f1) order by company
					        ");
				    	}

		    		}

		    		// if($salary=="" and $f1<>"" and $f2<>"" and $salary3=="") 
		    		// {
		    		// 	$sql=$this->_db->query
				    // 	(
				    //     "select * from base_jobs
				    //      where  company like '%$nom%' and (CAST(salary as decimal)>$f1 )  order by company
				    //     "
				    //     );				       

		    		// }

		    		// if($salary=="" and $salary2=="" and $salary3<>"") 
		    		// {
		    		// 	$sql=$this->_db->query
				    // 	(
				    //     "select * from base_jobs
				    //      where  company like '%$nom%' and (CAST(salary as decimal) >$salary3)  order by company
				    //     ");

		    		// }
   				    	

		    	}


	  

	        return $sql->fetchAll();            
	        }
	    catch (Exception $ex){
	            print "Error: Guardar entidad".$ex->getMessage();
	        }
    }
}