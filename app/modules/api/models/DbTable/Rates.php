<?php


class Api_Model_DbTable_Rates extends Zend_Db_Table_Abstract{
	protected $_name = 'base_rates';
	protected $_primary = array("eid","oid","ratid","perid");


	
	public function _getFilter($where=null,$attrib=null,$orders=null){
			try{
				if($where['eid']=='' || $where['oid']=='') return false;
					$select = $this->_db->select();
					if ($attrib=='') $select->from("base_rates");
					else $select->from("base_rates",$attrib);
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
				print "Error: Read Filter Rates ".$e->getMessage();
			}
		}

	public function _save($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['ratid']=='' || $data['perid']=='' || $data['name']=='' || $data['t_normal']=='' || $data['f_ini_tn']=='' ) return false;
			return $this->insert($data);
			return false;
		}catch (Exception $e){
				print "Error: Save rates ".$e->getMessage();
		}
	}

	public function _getAll($where=null,$order='',$start=0,$limit=0){

		try {
			if($where['eid']=='' || $where['oid']=='')
				$wherestr= null;
			else
				$wherestr="eid='".$where['eid']."' and oid='".$where['oid']."'";
			if($limit==0) $limit=null;	
			if($start==0) $start=null;

			$rows=$this->fetchAll($wherestr,$order,$start,$limit);
			if($rows) return $rows->toArray();
				// print_r($rows->toArray);
			return false;

		} catch (Exception $e) {
			print "Error: Read All Rates".$e->getMessage();			
		}
	}

	public function _getOne($where=array())
	{
		try{
			
			if ($where['eid']=="" || $where['oid']=="" || $where['ratid']==""  || $where['perid']=="") return false;
			$wherestr="eid = '".$where['eid']."' and oid = '".$where['oid']."' and ratid = '".$where['ratid']."' and perid = '".$where['perid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One rates".$e->getMessage();
		}
	}
      

	public function _update($data,$str='')
    {
        try
        {
            if ($str=="") return false;
            return $this->update($data,$str);
        }
        catch (Exception $ex)
        {
            print "Error: Guardar rates".$ex->getMessage();
        }
    }

    public function _delete($data)
	{
		try{
			if ($data['eid']=='' ||  $data['oid']=='' || $data['ratid']=='' || $data['perid']=='') return false;
			$where = "eid = '".$data['eid']."' and oid='".$data['oid']."' and ratid='".$data['ratid']."' and perid='".$data['perid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete rates".$e->getMessage();
		}
	}



}
