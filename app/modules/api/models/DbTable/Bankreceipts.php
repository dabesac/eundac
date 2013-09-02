<?php

class Api_Model_DbTable_Bankreceipts extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_bankreceipts';
	protected $_primary = array("operation","code_student");
	
	 public function _getBankreceiptsBetween2Dates($fecha_ini='',$fecha_fin='')
    {
        try
        {
            if ($fecha_ini=='' || $fecha_fin=='') return false;
            $sql=$this->_db->query(" 
                select * from base_Bankreceipts 
                where payment_date  between '$fecha_ini' and '$fecha_fin'");
            return $sql->fetchAll(); 
        }  
        catch (Exception $ex)
        {
            print "Error: Lecturando el registro de Recibobanco".$ex->getMessage();
        }   
    }

    public function _getBankreceiptsBetween2DatesXuid($fecha_ini='',$fecha_fin='',$uid='')
    {
        try 
        {
            if($fecha_ini=='' || $fecha_fin=='' || $uid=='') return false;    
            $sql=$this->_db->query("select * from base_Bankreceipts 
                where code_student='$uid' and payment_date  between '$fecha_ini' and '$fecha_fin'");
            return $sql->fetchAll();
        } catch (Exception $ex) {
            print "Error: Lecturando el registro de Recibos".$ex->getMessage();
        }
    }

     public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			$select = $this->_db->select();
				if ($attrib=='') $select->from("base_bankreceipts");
				else $select->from("base_bankreceipts",$attrib);
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
			print "Error: Read Filter bankreceipts ".$e->getMessage();
		}
	}

	public function _update($data,$pk){
		try{
			if ($pk['operation']=='' || $pk['code_student']=='') return false;
			$where = "operation = '".$pk['operation']."'and code_student = '".$pk['code_student']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Organization ".$e->getMessage();
		}
	}

    public function _update_x_periods_x_receipt($data,$pk)
    {
        try {

            if ($pk['operation']=='' || $pk['code_student']=='') return false;
            $where = "operation = '".$pk['operation']."'and code_student = '".$pk['code_student']."' 
                        and  perid='".$pk['perid']."' and concept='".$pk['concept']."'";

            return $this->update($data, $where);
            return false;
            
        } catch (Exception $e) {
            print "Error: Update receipt por periods ".$e->getMessage();
            
        }
    }
	   public function _getbankreceiptsXAnio($anio='')
    {
        try 
        {
            if($anio=='') return false;    
            $sql=$this->_db->query("
                select distinct payment_date from base_bankreceipts where extract(year from payment_date )='$anio' order by payment_date
                ");
            return $sql->fetchAll();
        } catch (Exception $ex) {
            print "Error: Lecturando el registro de Recibos".$ex->getMessage();
        }
    }

}