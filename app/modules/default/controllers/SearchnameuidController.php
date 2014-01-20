<?php
class SearchnameuidController extends Zend_Controller_Action{

	public function init(){
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	$this->sesion = $login;
    }

    public function indexAction(){
    	$form=new Default_Form_Search();
    	$this->view->form=$form;
    }

    public function searchAction(){
        try {
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $val = $this->_getParam('searching');
            $dbuid= new Api_Model_DbTable_Users();

            if (is_numeric($val)) {
                $where=array('eid'=>$eid,'oid'=>$oid,'uid'=>$val);
                $datauid=$dbuid->_getUserXUid($where);
                // print_r($datauid);

                $pid=substr($val,0,8);
                if (is_numeric($pid)) {
                    $where=array('eid'=>$eid,'oid'=>$oid,'pid'=>$pid);
                    // print_r($where);
                    $datapid=$dbuid->_getUserXPid($where);
                    // print_r($datapid);
                }
            }
            else {
                $pid=substr($val,0,8);
                $rid=substr($val,8,2);
                if (is_numeric($pid) && $rid) {
                    $where=array('eid'=>$eid,'oid'=>$oid,'pid'=>$pid,'rid'=>$rid);
                    $datapid=$dbuid->_getUserXRidXPid($where);
                    // print_r($datapid);
                }
                else{
                    $where['nom'] = trim(strtoupper($val));
                    $where['nom'] = mb_strtoupper($where['nom'],'UTF-8');
                    $where['eid'] = $eid;
                    $where['oid'] = $oid;
                    $where['rid'] = 'AL';
                    // print_r($where);
                    $dataname=$dbuid->_getUsuarioXNombre($where);
                    // print_r($dataname);                    
                }
            }    
            $this->view->datapid=$datapid;
            $this->view->datauid=$datauid;
            $this->view->dataname=$dataname;
            
        } catch (Exception $e) {
            
        }
    }

}











