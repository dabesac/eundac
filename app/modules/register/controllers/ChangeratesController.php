<?php
class Register_ChangeratesController extends Zend_Controller_Action{

	public function init(){
		$sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		if (!$login->rol['module']=="bienestar"){
 			$this->_helper->redirector('index','index','default');
 		}
		$this->sesion = $login;

	}

	public function indexAction(){
		try {
 			$this->sesion->eid;
 			$this->sesion->oid;
 			$this->sesion->rid;
 			$perid=$this->sesion->period->perid;
 			$this->view->perid=$perid;
 			$fm=new Register_Form_Buscar();
			$this->view->fm=$fm;
 			
 		} catch (Exception $e) {
 			print ('Error: Get data'. $e->getMessage());
 			
 		}

	}

	public function getuserAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$perid=$this->sesion->period->perid;
 			$this->view->perid=$perid;
          	$uid= $this->_getParam('uid');
          	$where['uid'] = $uid;
        	$where['eid'] = $this->sesion->eid;
        	$where['oid'] = $this->sesion->oid;
        	$bdu = new Api_Model_DbTable_Users();
            $data = $bdu->_getUserXUid($where);
            $this->view->data=$data;

            if ($data) {
                    $where['eid']=$this->sesion->eid;
                    $where['oid']=$this->sesion->oid;
                    $where['perid']=$perid;
                    $where['uid']=$data[0]['uid'];
                    $list= new Api_Model_DbTable_Payments();
                    $dlist=$list->_getFilter($where);
                	$this->view->dlist=$dlist;

                    $whered['eid']=$this->sesion->eid;
                    $whered['oid']=$this->sesion->oid;
                    $whered['perid']=$perid;
                    $whered['ratid']=$dlist[0]['ratid'];
                    $rates= new Api_Model_DbTable_Rates();
                    $dblist=$rates->_getOne($whered);            
                	$this->view->dblist=$dblist;                
            }
            $info['eid'] = $data[0]['eid'];
            $info['oid'] = $data[0]['oid'];
            $info['escid'] = $data[0]['escid'];
            $info['subid'] = $data[0]['subid'];
            $facaulm = new Api_Model_DbTable_Speciality();
            $rfacaulm = $facaulm->_getOne($info);
            $this->view->rfacaulm=$rfacaulm;

            $wheres['eid']=$this->sesion->eid;
            $wheres['oid']=$this->sesion->oid;
            $wheres['facid'] = $rfacaulm['facid'];
            $lfacu = new Api_Model_DbTable_Faculty();
            $dfacu = $lfacu->_getOne($wheres);
            $this->view->dfacu=$dfacu;

			
		} catch (Exception $e) {
			print ('Error: get data user'.$e->getMessage());
		}

	}

	public function changerateAction(){
        try {
            
		$this->_helper->layout()->disableLayout();
        $fm=new Register_Form_Changerate();
		$this->view->fm=$fm;
        $eid= base64_decode($this->_getParam('eid'));
        $oid= base64_decode($this->_getParam('oid'));
        $uid= base64_decode($this->_getParam('uid'));
        $pid= base64_decode($this->_getParam('pid'));
        $escid= base64_decode($this->_getParam('escid'));
        $subid= base64_decode($this->_getParam('subid'));
        $perid= base64_decode($this->_getParam('perid'));
        $this->view->eid=$eid;
        $this->view->oid=$oid;
        $this->view->uid=$uid;
        $this->view->pid=$pid;
        $this->view->escid=$escid;
        $this->view->subid=$subid;
        $this->view->perid=$perid;
        $date['ratid'] = base64_decode($this->_getParam('ratid'));
        $date['date_payment'] = base64_decode($this->_getParam('date_payment'));
        $date['document_auth'] = base64_decode($this->_getParam('document_auth'));
        $fm->populate($date);
        $this->view->fm=$fm;
        if ($this->getRequest()->isPost()) {
                $frmdata=$this->getRequest()->getPost();
                    // print_r($frmdata);
                if ($fm->isValid($frmdata)) {
                    unset($frmdata['send']);
                    trim($frmdata['ratid']);
                    trim($frmdata['document_auth']);
                    $pks['eid']=$frmdata['eid'];
                    $pks['oid']=$frmdata['oid'];
                    $pks['uid']=$frmdata['uid'];
                    $pks['pid']=$frmdata['pid'];
                    $pks['escid']=$frmdata['escid'];
                    $pks['subid']=$frmdata['subid'];
                    $pks['perid']=$frmdata['perid'];
                    // print_r($pks);
                    unset($frmdata['eid']);
                    unset($frmdata['oid']);
                    unset($frmdata['uid']);
                    unset($frmdata['pid']);
                    unset($frmdata['escid']);
                    unset($frmdata['subid']);
                    unset($frmdata['perid']);
                    unset($frmdata['guardar']);
                    $frmdata['modified']=$this->sesion->uid;
                    $frmdata['updated']=date('Y-m-d h:m:s');
                    $reg_= new Api_Model_DbTable_Payments();
                    // print_r($frmdata);
                    // print_r($pks); exit();
                    $reg_->_update($frmdata,$pks);
                    $this->_redirect("/register/changerates/");
                }
                else
                {
                    echo "Ingrese nuevamente por favor";
                }
            }
        } catch (Exception $e) {
            
        }
	}

    public function asignationrateAction(){
        try {
                $this->_helper->layout()->disableLayout();
                $fm=new Register_Form_Changerate();
                $this->view->fm=$fm;
                $eid= base64_decode($this->_getParam('eid'));
                $oid= base64_decode($this->_getParam('oid'));
                $uid= base64_decode($this->_getParam('uid'));
                $pid= base64_decode($this->_getParam('pid'));
                $escid= base64_decode($this->_getParam('escid'));
                $subid= base64_decode($this->_getParam('subid'));
                $perid= base64_decode($this->_getParam('perid'));
                $this->view->eid=$eid;
                $this->view->oid=$oid;
                $this->view->uid=$uid;
                $this->view->pid=$pid;
                $this->view->escid=$escid;
                $this->view->subid=$subid;
                $this->view->perid=$perid;
                              
                if ($this->getRequest()->isPost()) {
                    $frmdata=$this->getRequest()->getPost();
                    // print_r($frmdata);
                        if ($fm->isValid($frmdata)) {
                            unset($frmdata['guardar']);
                            trim($frmdata['ratid']);
                            trim($frmdata['document_auth']);
                            $frmdata['date_payment']=date('Y-m-d h:m:s');
                            $frmdata['amount']='0';
                            $frmdata['register']=$this->sesion->uid;
                            $frmdata['created']=date('Y-m-d h:m:s');
                            $reg_= new Api_Model_DbTable_Payments();
                            $reg_->_save($frmdata);
                            $this->_redirect("/register/changerates/");
                        }
                        else
                        {
                            echo "Ingrese nuevamente por favor";
                        }
                }
        } catch (Exception $e) {
            print ('Error: asignation rate'.$e->getMessage());
        }
    }

}