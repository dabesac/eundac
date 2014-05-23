<?php
	class Api_Model_DbTable_Accessapi extends Zend_Db_Table_Abstract
	{
		protected $_name='base_key_secret_api';
		protected $_primary=array('ip');

		public function _getAll()
		{
			$row=$this->fetchAll();
			return $row->toArray();
		}

		public function _getOne($pk=array())
		{
			try{
				if ($pk['ip']=="") return false;
				$pkstr="ip = '".$pk['ip']."'";
				$row = $this->fetchRow($pkstr);
				if($row) return $row->toArray();
				return false;
			}catch (Exception $e){
				print "Error: Read One Ip ".$e->getMessage();
			}
		}
		public function addNew($data)
		{
			try{

				return $this->insert($data);

			}catch(Exeption $e){
				echo "Falta Error".$e->getMessage();
			}

		}

		public function addChange($formdata,$str)
		{
			try{
				
				return $this->update($formdata,$str);

			}catch(Exeption $e){
				echo "Falta Error".$e->getMessage();
			}
		}

		public function deleteRow($data){
			try{
				return $this->delete($data);
			}catch(Exeption $e){
				print "Errro: Delete".$e->getMessage();
			}
		}
	}
?>