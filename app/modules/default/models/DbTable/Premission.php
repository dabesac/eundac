<?php

class Default_Model_DbTable_Premission extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_acl';
	protected $_primary = array('eid', 'oid', 'reid', 'rid', 'mid');


	public function _getResource_Role($eid=null,$oid=nul,$rid=null)
	{
		try {
			
			if ($eid=='' || $oid==''|| $rid=='')return false;
			$where="eid = '".$eid."' and oid = '".$oid."' and rid = '".$rid."'";
			$rows=$this->fetchAll($where);
			if($rows) return $rows->toArray();
			return false;

		} catch (Exception $e) {
			print "Error: Read Filter Acl's ".$e->getMessage();
		}
	}

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_acl");
				else $select->from("base_acl",$attrib);
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
			print "Error: Read Filter Premission's ".$e->getMessage();
		}
	}

	/**
	* @param 
	*/
	public function _getAllMenu($where=array()){
		try {

			if ($where['eid']=='' || $where['oid'] =='' || $where['rid'] == '') return false;

			$select = $this->_db->select()
				->from(array('a'=>'base_acl'))
				->join(array('r'=>'base_resource'),
					'a.eid = r.eid and a.oid=r.oid and a.reid=r.reid and a.mid=r.mid',array('r.module','r.controller','r.action','name_r'=>'r.name','icon_r'=>'r.imgicon'))
				->join(array('m'=>'base_module'),'r.eid=m.eid and r.oid=m.oid and r.mid=m.mid',array('m.mid','name_m'=>'m.name',  'icon_m' =>'m.imgicon'))
				->where('a.rid = ?', $where['rid'])
				->where('r.is_menu = ?', 'S')
				->where('a.permission = ?', 'allow');

			$results = $select->query();			
			$rows = $results->fetchAll();
			
			if($rows) return $rows;
			return false;
			
		} catch (Exception $e) {
			print "Error: Read Filter Premission's ".$e->getMessage();
			
		}
	}

}