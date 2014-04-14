<?php
	class Api_Model_DbTable_Infoacademic extends Zend_Db_Table_Abstract
	{
		protected $_name='base_add_reportacad_adm';
		protected $_primary=array("eid","oid","escid","subid","perid","uid","pid");
		
		public function listteacher($escid,$perid)
		{
			$sql=$this->_db->query("SELECT last_name0,last_name1,first_name,a.pid,state FROM base_person a inner join base_add_reportacad_adm b on a.pid=b.pid WHERE escid='$escid' AND perid='$perid'");
			$row=$sql->fetchAll();
			return $row;
		}
		public function _update($escid,$perid,$state,$pid)
		{
			$sql=$this->_db->query("UPDATE base_add_reportacad_adm set state='$state' WHERE escid='$escid' AND perid='$perid' AND pid='$pid' ");
			
		}
	}