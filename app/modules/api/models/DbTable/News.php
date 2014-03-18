<?php

class Api_Model_DbTable_News extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_system_news';
	protected $_primary = array("newid");
	protected $_sequence ="s_system_news";

	
	public function _getLastNews(){
		try{
            $sql=$this->_db->query("select * from base_system_news limit 20");
        	$r = $sql->fetchAll();
        	return $r;
		}catch (Exception $e){
			print "Error: Read Filter News ".$e->getMessage();
		}
	}
}