<?php

class Default_Model_DbTable_Premission extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_acl';
	protected $_primary = array('eid', 'oid', 'reid', 'rid', 'mid');

	public function _getResources($where = array()){
	
		if($where['eid']=='' || $where['oid']=='' || $where['rid']=='' || $where['mid']=='') return false;
		//print_r($where);
		$select = $this->_db->select()
			->from(array('a' => 'base_acl'),array('*'))
				->join(array('r' => 'base_resource'),'a.eid=r.eid and a.oid=r.oid and 
						a.reid=r.reid and a.mid=r.mid')
				->where('a.eid = ?', $where['eid'])
				->where('a.oid = ?', $where['oid'])
				->where('r.is_menu = ?', 'S')
				->where('a.permission= ?','allow')
				->where('a.mid = ?', $where['mid'])
				->where('a.rid = ?', $where['rid']);
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
			return false;
	}
	public function _getResources_parent($where = array()){
	
		if($where['eid']=='' || $where['oid']=='' || $where['rid']=='' || $where['mid']=='' || $where['parent'] =='') return false;
		//print_r($where);
			$sql = $this->_db->query("
				select *from 
				base_acl as a
				inner join base_resource r on
				a.eid = r.eid and a.oid=r.oid and
				a.mid = r.mid and a.reid = r.reid
				where
				a.eid = '".$where['eid']."' and
				a.oid = '".$where['oid']."' and
				a.mid = '".$where['mid']."' and
				a.rid in ('".$where['parent']."','".$where['rid']."') and
				a.permission = 'allow' and 
				r.is_menu = 'S'
               ");
            $rows=$sql->fetchAll();
			if($rows) return $rows;
			return false;
	}
	
	public function _getResource_Role($eid=null,$oid=nul,$rid=null)
	{
		try {
			
			if ($eid=='' || $oid==''|| $rid=='')return false;
			
			$select = $this->_db->select()
			->from(array('a' => 'base_acl'))
				->join(array('r' => 'base_resource'),'a.eid=r.eid and a.oid=r.oid and 
						a.reid=r.reid and a.mid=r.mid', array('r.*'))
				->where('a.eid = ?', $eid)
				->where('a.oid = ?', $oid)
				->where('a.rid = ?', $rid);
			$results = $select->query();			
			$rows = $results->fetchAll();
			if($rows) return $rows;
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
			$sql = $this->_db->query("
				select 
				m.mid, m.name
				from base_acl a 
				inner join base_resource r 
				on a.eid = r.eid and a.oid	= r.oid and 
				a.mid = r.mid and a.reid = r.reid 
				inner join base_module m
				on r.eid = m.eid and r.oid	= m.oid and 
				r.mid = m.mid 
				where 
				a.eid = '".$where['eid']."' and
				a.oid = '".$where['oid']."' and
				a.rid='".$where['rid']."' and is_menu='S' and a.permission='allow'
				group by m.mid,m.name 
               ");
            $rows=$sql->fetchAll();
			if($rows) return $rows;
			return false;
			
		} catch (Exception $e) {
			print "Error: Read Filter Premission's ".$e->getMessage();
			
		}
	}
	public function _getAllMenu_parent($where=array()){
		try {

			if ($where['eid']=='' || $where['oid'] =='' || $where['rid'] == '' || $where['parent'] =='') return false;
			$sql = $this->_db->query("
				select 
				m.mid, m.name
				from base_acl a 
				inner join base_resource r 
				on a.eid = r.eid and a.oid	= r.oid and 
				a.mid = r.mid and a.reid = r.reid 
				inner join base_module m
				on r.eid = m.eid and r.oid	= m.oid and 
				r.mid = m.mid 
				where 
				a.eid = '".$where['eid']."' and
				a.oid = '".$where['oid']."' and
				a.rid in ('".$where['rid']."','".$where['parent']."') and 
				is_menu='S' and a.permission='allow'
				group by m.mid,m.name 
               ");
            $rows=$sql->fetchAll();
			if($rows) return $rows;
			return false;
			
		} catch (Exception $e) {
			print "Error: Read Filter Premission's ".$e->getMessage();
			
		}
	}
	public function _getResource_Role_Parent($eid='',$oid='',$rid='',$parent=''){
		try {

			if ($eid == ''||	$oid==''	|| $rid=='' || $parent == '') return false;
			$sql = $this->_db->query("
				select *from 
				base_acl as a
				inner join base_resource r on
				a.eid = r.eid and a.oid=r.oid and
				a.mid = r.mid and a.reid = r.reid
				where
				a.permission = 'allow' and
				a.eid = '".$eid."' and
				a.oid = '".$oid."' and 
				a.rid in ('".$rid."','".$parent."') 
               ");
            $rows=$sql->fetchAll();
			if($rows) return $rows;
			return false;
			
		} catch (Exception $e) {
			print "Error: Read Filter Premission's ".$e->getMessage();
			
		}
	}

}