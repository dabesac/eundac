<?php

class Api_Model_DbTable_Periods extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_periods';
	protected $_primary = array("eid","oid","perid");

	public function _getOne($where=array()){
		try{
			if ($where['eid']=="" || $where['oid']=="" || $where['perid']=="") return false;
			$wherestr="eid='".$where['eid']."' and oid='".$where['oid']."' and perid='".$where['perid']."' and (state='I' or state='A')";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Entity ".$e->getMessage();
		}
	}
  public function _update($data,$str=''){
      try{
          if ($str=="") return false;
        return $this->update($data,$str);
      return false;
        }catch (Exception $ex){
      print "Error: Guardar periodo".$ex->getMessage();
    }
  }

		public function _save($data)
	{
		try{
			// if ($data['eid']=='' ||  $data['oid']=='' || $data['perid']=='' || $data['name']=='' ) return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save Periodo ".$e->getMessage();
		}
	}


	public function _getOnePeriod($where=array()){
		try{
			if ($where['eid']=="" || $where['oid']=="" || $where['perid']=="") return false;
			$wherestr="eid='".$where['eid']."' and oid='".$where['oid']."' and perid='".$where['perid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Entity ".$e->getMessage();
		}
	}
	
	public function _getPeriodsCurrent($data=null)
	{
		try{
			if ($data['eid']=='' || $data['oid']=='' ) return false;
			$where = "eid='".$data['eid']."' and oid='".$data['oid']."' and state='A'";
			$row = $this->fetchRow($where);
			if ($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Entity ".$e->getMessage();
		}
	}
	
	public function _getPeriodsNext($data=null)
	{
		try{
			if ($data['eid']=='' || $data['oid']=='' ) return false;
			$where = "eid='".$data['eid']."' and oid='".$data['oid']."' and state='I'";
			$row = $this->fetchRow($where);
			if ($row) return $row->toArray();
			return $this->_getPeriodsCurrent($data);	
		}catch (Exception $e){
			print "Error: Read One Entity ".$e->getMessage();
		}
	}
	
	public function _getPeriodsTermporaly()
	{
		try{
			if ($data['eid']=='' || $data['oid']=='' ) return false;
			$where = "eid='$eid' and oid='$oid' and state='T'";
			$row = $this->fetchAll($where);
			if ($row) return $row->toArray();
				
		}catch (Exception $e){
			print "Error: Read One Entity ".$e->getMessage();
		}
	}

	public function _getPeriodsxYears($data=null){
		// $sql=$this->_db->query("select * from periodos where eid='$eid' and oid ='$oid' and left(perid,2)='$anio' order by perid");
		try {
			if ($data['eid']=='' || $data['oid']=='') return false;
			$wherestr="eid='".$data['eid']."' and oid='".$data['oid']."' and left(perid,2)='".$data['year']."'";
			$order="perid asc";
			print_r($wherestr);
			$rows= $this->fetchAll($wherestr,$order);
			if ($rows) return $rows->toArray();
			return false;
		} catch (Exception $e) {
			print "Error: Read All Periods x Years" .$e->getMessage();
		}
	}

	public function _getPeriodsxYears1($data=null){
		try {
			if ($data['eid']=='' || $data['oid']=='') return false;
			$wherestr="eid='".$data['eid']."' and oid='".$data['oid']."' and perid in('13N','14A')";
			$order="perid asc";
			$rows= $this->fetchAll($wherestr,$order);
			if ($rows) return $rows->toArray();
			return false;
		} catch (Exception $e) {
			print "Error: Read All Periods x Years" .$e->getMessage();
		}
	}
  //Retorna dos periodos respectivos
     public function _getPeriodsXAyB($where){
            try{
            if ($where['eid']=="" || $where['oid']=="" || $where['p1']=="" || $where['p2']=="") return false;
			$wherestr="eid='".$where['eid']."' and oid='".$where['oid']."' and (perid='".$where['p1']."' or perid='".$where['p2']."')";
             $r = $this->fetchAll($wherestr);
                if ($r) return $r->toArray ();
                return false;
        }  catch (Exception $ex){
            phpsage(); }
    }

    public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_periods");
				else $select->from("base_periods",$attrib);
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
			print "Error: Read Filter Curricula ".$e->getMessage();
		}
	}


	public function _getAniosPerids($where=null)
    {
       try
       {
            $sql=$this->_db->query("
                select distinct left(perid,2) as anioid,case when cast(left(perid,2)as integer)>40 then '19' || left(perid,2) else '20' || left(perid,2) end  as anio  from base_periods
                where eid='".$where['eid']."'
                order by anio desc
           ");  
           $row=$sql->fetchAll();
           return $row;  
        } 
        catch (Exception $ex)
        {
            print "Al momento de Filtrar todos los anios existentes de periodos ".$ex->getMessage();
        }
    }

    public function _get_periods_ini_temp_activo($where = array() )
    {
       try
       {
       		if ($where['eid'] == "" || $where['oid']=='') return false;

            $sql=$this->_db->query("
            	select *from 
            	base_periods where 
            	eid='".$where['eid']."' and
            	oid ='".$where['oid']."' and
            	state in('A','T','I') and  
            	right(perid,1) in ('A','B','J','N')
            ");  
           $row=$sql->fetchAll();
           return $row;  
        } 
        catch (Exception $ex)
        {
            print "read periods temporal activo and start".$ex->getMessage();
        }
    }

	public function _delete($data)
	{
		try{
			// if ($data['eid']=='' ||  $data['oid']=='' || $data['perid']=='' return false;
			$where = "eid = '".$data['eid']."' and oid='".$data['oid']."'  and perid='".$data['perid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete Add_reportacad_adm ".$e->getMessage();
		}
	}

}
