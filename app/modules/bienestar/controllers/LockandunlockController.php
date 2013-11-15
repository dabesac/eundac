<?php 
class Bienestar_LockandunlockController extends Zend_Controller_Action {

	public function init(){
		$sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		$this->sesion = $login;
	}
	public function indexAction(){
		try {
			$fm= new Bienestar_Form_Search();
			$this->view->fm=$fm;
		} catch (Exception $e) {
			print "Error: Lock and Unlock".$e->getMessage();
		}
	}

	public function getuserAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$uid= $this->_getParam('uid');
          	$eid=$this->sesion->eid;
        	$oid=$this->sesion->oid;
        	$where=array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid);
 			$bduser = new Api_Model_DbTable_Users();
            $data = $bduser->_getUserXUid($where);
            $i=0;
			foreach ($data as $user) {
				$subid=$user['subid'];
				$escid=$user['escid'];
				$where=array('eid'=>$eid,'oid'=>$oid,'subid'=>$subid);
				$bdsubid = new Api_Model_DbTable_Subsidiary();
				$datasubid[$i] = $bdsubid->_getOne($where);
				$where=array('eid'=>$eid,'oid'=>$oid,'subid'=>$subid,'escid'=>$escid);
				$bdescid= new Api_Model_DbTable_Speciality();
				$dataesc[$i] = $bdescid->_getOne($where);
				$i++;  	
			} 
			$band=0;
			$where=array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid);
			$attrib=array('state');
			$bdata= new Api_Model_DbTable_Lockandunlock();
			$datauser= $bdata->_getFilter($where,$attrib);
			$l=count($datauser);
			for ($i=0; $i <= $l ; $i++) { 
				$state=$datauser[$i]['state'];
				if ($state=='C') {
					$band=1;
					$i=$l+1;
				}				
			}
			// print_r($data);
			$this->view->band=$band;	
			$this->view->esc=$dataesc;
			$this->view->sub=$datasubid;  
            $this->view->data=$data;
		} catch (Exception $e) {
			print "Error: get User".$e->getMessage();
		}
	}

	public function historyuserAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$uid=$this->_getParam('uid');
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;	
			$where=array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid);
			$attrib=array('dbuid','type_doc_lock','date_lock','reason_lock','type_doc_unlock','date_unlock','reason_unlock','state');
			$order=array('state and desc');
			$bdata= new Api_Model_DbTable_Lockandunlock();
			$datauser= $bdata->_getFilter($where,$attrib);
			
			$bduser = new Api_Model_DbTable_Users();
            $data = $bduser->_getUserXUid($where);
            $i=0;
			foreach ($data as $user) {
				$subid=$user['subid'];
				$escid=$user['escid'];
				$where=array('eid'=>$eid,'oid'=>$oid,'subid'=>$subid);
				$bdsubid = new Api_Model_DbTable_Subsidiary();
				$datasubid[$i] = $bdsubid->_getOne($where);
				$where=array('eid'=>$eid,'oid'=>$oid,'subid'=>$subid,'escid'=>$escid);
				$bdescid= new Api_Model_DbTable_Speciality();
				$dataesc[$i] = $bdescid->_getOne($where);
				$i++;  	
			}
			$this->view->esc=$dataesc;
			$this->view->sub=$datasubid;  
            $this->view->data=$data;
			$this->view->datauser=$datauser;
		} catch (Exception $e) {
			print "Error: History User".$e->getMessage();
		}
	}
	public function createdAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$fm= new Bienestar_Form_Lock();
			$this->view->fm=$fm;
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$perid=$this->sesion->period->perid;
			$uid_reg=$this->sesion->uid;
			$pid=$this->_getParam('pid');
			$this->view->pid=$pid;
			$uid=$this->_getParam('uid');
			$this->view->uid=$uid;
			$escid=$this->_getParam('escid');
			$this->view->escid=$escid;
			$subid=$this->_getParam('subid');
			$this->view->subid=$subid;
			if ($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();

                if ($fm->isValid($frmdata)){

                	unset($frmdata['Guardar']);
                	trim($frmdata['office_lock']);
	                trim($frmdata['reason_lock']);
	                trim($frmdata['type_doc_lock']);
	                trim($frmdata['detail_lock']);
	                $frmdata['eid']=$eid;
	                $frmdata['oid']=$oid;
	                $frmdata['perid']=$perid;
	                $frmdata['date_reg']=date('Y-m-d');
	                $frmdata['uid_lock']=$uid_reg;
	                $reg_= new Api_Model_DbTable_Lockandunlock();
	                $reg_->_save($frmdata);
	                ?>
	                <script type="text/javascript">
	                	alert("El usuario ha sido bloqueado");
						 $("#myModal").modal('hide');   					
	                </script>
	                <?php

                }
                else{
	          		echo "Ingrese nuevamente por favor";
                }	
            }
		} catch (Exception $e) {
			print "Error: Create".$e->getMessage();
		}
	}

	public function updatedAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$fm= new Bienestar_Form_Unlock();
			$this->view->fm=$fm;
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$perid=$this->sesion->period->perid;
			$uid_reg=$this->sesion->uid;
			$dbuid=$this->_getParam('dbuid');
			$this->view->dbuid=$dbuid;
			$pid=$this->_getParam('pid');
			$this->view->pid=$pid;
			$uid=$this->_getParam('uid');
			$this->view->uid=$uid;
			$escid=$this->_getParam('escid');
			$this->view->escid=$escid;
			$subid=$this->_getParam('subid');
			$this->view->subid=$subid;
			if ($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();

                if ($fm->isValid($frmdata)){

                	unset($frmdata['Guardar']);
                	trim($frmdata['office_unlock']);
	                trim($frmdata['reason_unlock']);
	                trim($frmdata['type_doc_unlock']);
	                trim($frmdata['detail_unlock']);
	                $pk['eid']=$eid;
	                $pk['oid']=$oid;
	                $pk['dbuid']=$frmdata['dbuid'];
	                $pk['subid']=$frmdata['subid'];
	                $pk['pid']=$frmdata['pid'];
	                $pk['uid']=$frmdata['uid'];
	                $pk['escid']=$frmdata['escid'];
	                $frmdata['perid']=$perid;
	                $frmdata['date_reg']=date('Y-m-d');
	                $frmdata['uid_lock']=$uid_reg;
	                // print_r($frmdata);
	                // print_r($pk);
	                $reg_= new Api_Model_DbTable_Lockandunlock();
	                $reg_->_update($frmdata,$pk);
	                ?>
	                <script type="text/javascript">
	                	alert("El usuario ha sido Desbloqueado");
						$("#Modal").modal('hide'); 
	                </script>
	                <?php	                
                }
                else{
	          		echo "Ingrese nuevamente por favor";
                }	
            }

       	} catch (Exception $e) {
			print "Error: Create".$e->getMessage();
		}
	}
}