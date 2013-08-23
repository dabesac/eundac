<?php

class Api_Model_DbTable_Rates extends Zend_Db_Table_Abstract{
	protected $_name = 'base_rates';
	protected $_primary = array("eid","oid","ratid","perid");


                public function _getOne($data=null){
            try{               
                if ($data['eid']==''|| $data['oid']==''||$data['ratid']==''|| $data['perid']=='') return false;
              	$where ="eid='".$data['eid']."' and oid='".$data['oid']."' and ratid='".$data['ratid']."'";
                $row = $this->fetchRow($where);
            if($row) return $row->toArray();
            }  catch (Exception $ex){
                print "Error Generar el registro de su pago".$ex->getMessage();
            }
        }
        
}
