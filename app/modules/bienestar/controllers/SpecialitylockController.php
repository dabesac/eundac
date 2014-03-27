<?php
class Bienestar_SpecialitylockController extends Zend_Controller_Action {

	public function init(){
		$sesion = Zend_Auth::getInstance();
		if (!$sesion->hasIdentity()) {
			$this->_helper->redirector('index','index','default');
		}
		$login = $sesion->getStorage()->read();
		$this->sesion = $login;
	}

	public function indexAction(){
		try {
			$frm = new Bienestar_Form_Speciality();
			$this->view->frm=$frm;
			$fm= new Bienestar_Form_Lock();
			$this->view->fm=$fm;

		} catch (Exception $e) {
			print "Error: Lock Speciality".$e->getMessage();
		}
	}

	public function getspecialityAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$where['eid']=$this->sesion->eid;
			$where['oid']=$this->sesion->oid;
			$where['facid']=$this->_getParam('facid');
			$where['state']='A';
			$attrib = array('escid','subid','facid','name','state');
			$filter=new Api_Model_DbTable_Speciality();
			$data=$filter->_getFilter($where,$attrib);
			$this->view->data=$data;
		} catch (Exception $e) {
			print "Error: get Speciality".$e->getMessage();
		}
	}

	public function listlockAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$escid=$this->_getParam('escid');
			$subid=$this->_getParam('subid');
			$office=$this->_getParam('office');
			$reason=$this->_getParam('reason');
			$type_doc=$this->_getParam('type_doc');
			$date=$this->_getParam('date');
			$detail=$this->_getParam('detail');
			$formdata=array('escid'=>$escid,'subid'=>$subid,'office'=>$office,'reason'=>$reason,'type_doc'=>$type_doc,'date'=>$date,'detail'=>$detail);
			$this->view->formdata=$formdata;
			$state=$this->_getParam('state');			
			if ($state=='A') {
				$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'rid'=>'AL','state'=>$state);
				$dbuser= new Api_Model_DbTable_Users();
				$lockuser=$dbuser->_getUserXRidXEscidAll($where);	
			}
			$this->view->lockuser=$lockuser;
		} catch (Exception $e) {
			print "Error: List lock ".$e->getMessage();
				
		}	
	}

	public function listunlockAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$escid=$this->_getParam('escid');
			$subid=$this->_getParam('subid');
			$office=$this->_getParam('office');
			$reason=$this->_getParam('reason');
			$type_doc=$this->_getParam('type_doc');
			$date=$this->_getParam('date');
			$detail=$this->_getParam('detail');
			$formdata=array('escid'=>$escid,'subid'=>$subid,'office'=>$office,'reason'=>$reason,'type_doc'=>$type_doc,'date'=>$date,'detail'=>$detail);
			$this->view->formdata=$formdata;
			$state=$this->_getParam('state');			
			if($state=='B'){
				$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'state'=>$state);
				$attrib=array('dbuid','subid','pid','escid','uid','perid','state');
				$dbuser= new Api_Model_DbTable_Lockandunlock();
				$unlockuser=$dbuser->_getFilter($where,$attrib);	
			}
			$i=0;
			$attri= array('pid','last_name0','last_name1','first_name');
			$order= array('last_name0');
			foreach ($unlockuser as $data) {
				$pid=$data['pid'];
				$whe = array('eid'=>$eid,'pid'=>$pid);
				$dbuser = new Api_Model_DbTable_Person();
				$datauser[$i] = $dbuser->_getFilter($whe,$attri,$order);
				$i++;
			}
			print_r($unlockuser);
			$this->view->datauser=$datauser;
			$this->view->unlockuser=$unlockuser;
		} catch (Exception $e) {
			print "Error: List unlock ".$e->getMessage();
				
		}	
	}

	public function createdAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
	        $uid_lock=$this->sesion->uid;
			$perid=$this->sesion->period->perid;
			$subid=$this->_getParam('subid');
			$pid=$this->_getParam('pid');
			$escid=$this->_getParam('escid');
			$uid=$this->_getParam('uid');
			$office_lock=$this->_getParam('office');
			$type_doc_lock=$this->_getParam('type_doc');
			$date_lock=$this->_getParam('date');
			$reason_lock=$this->_getParam('reason');
			$detail_lock=$this->_getParam('detail');
			$date_reg=date('Y-m-d');
			$where=array('subid'=>$subid,'eid'=>$eid,'oid'=>$oid,'pid'=>$pid,'escid'=>$escid,'uid'=>$uid,'perid'=>$perid,
				         'office_lock'=>$office_lock,'type_doc_lock'=>$type_doc_lock,'date_lock'=>$date_lock,'reason_lock'=>$reason_lock,
				         'detail_lock'=>$detail_lock,'uid_lock'=>$uid_lock,'date_reg'=>$date_reg,'state'=>"B");
			$dblock = new Api_Model_DbTable_Lockandunlock();
			if ($dblock->_save($where)) {
	            $pk = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'pid'=>$pid,'uid'=>$uid);
	            $data = array('state'=>'B','comments'=>$reason_lock);
	            $user_= new Api_Model_DbTable_Users();
	            $user_->_update($data,$pk);
	        }
	        // print_r($datalock);
		} catch (Exception $e) {
			print "Error: Created user(s)".$e->getMessage();
		}
	}

	public function updatedAction(){
		try {
		    $this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
	        $uid_unlock=$this->sesion->uid;
			$dbuid=$this->_getParam('dbuid');
			$subid=$this->_getParam('subid');
			$pid=$this->_getParam('pid');
			$escid=$this->_getParam('escid');
			$uid=$this->_getParam('uid');
			$office_lock=$this->_getParam('office');
			$type_doc_lock=$this->_getParam('type_doc');
			$date_lock=$this->_getParam('date');
			$reason_lock=$this->_getParam('reason');
			$detail_lock=$this->_getParam('detail');
			$data=array('office_unlock'=>$office_lock,'type_doc_unlock'=>$type_doc_lock,'date_unlock'=>$date_lock,'reason_unlock'=>$reason_lock,
				         'detail_unlock'=>$detail_lock,'uid_unlock'=>$uid_unlock,'state'=>"A");
			$pk = array('dbuid'=>$dbuid,'subid'=>$subid,'eid'=>$eid,'oid'=>$oid,'pid'=>$pid,'escid'=>$escid,'uid'=>$uid);	
	        $reg_= new Api_Model_DbTable_Lockandunlock();
	        if ($reg_->_update($data,$pk)) {
	            $pk = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'pid'=>$pid,'uid'=>$uid);
	            $data = array('state'=>'A','comments'=>$data['reason_lock']);
	            $user_= new Api_Model_DbTable_Users();
	            $user_->_update($data,$pk);
	        }	
		} catch (Exception $e) {
			print "Error: Updated user(s)".$e->getMessage();
		}
	}

	public function deletedAction(){
		try {
			
		} catch (Exception $e) {
			print "Error: Deleted user(s)".$e->getMessage();
		}
	}
}
