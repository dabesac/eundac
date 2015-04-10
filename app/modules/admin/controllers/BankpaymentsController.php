<?php

class Admin_BankpaymentsController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	
    	$this->sesion = $login;
    
    }
    
    public function indexAction()
    {
   	    
    }

    public function listAction()
    {
        $this->_helper->layout()->disableLayout();
        $oid=$this->sesion->oid;
        $eid=$this->sesion->eid;
        $this->view->eid=$eid;
        $this->view->oid=$oid;
        $fini=base64_decode($this->_getParam("fini"));
        $ffin=base64_decode($this->_getParam("ffin"));
        $uid=base64_decode($this->_getParam("usuario"));

        $this->view->fini=$fini;
        $this->view->ffin=$ffin;

        $f_ini= split("-",$fini);
        $f_fin= split("-",$ffin);
        $fini=$f_ini[2]."-".$f_ini[1]."-".$f_ini[0];
        $ffin=$f_fin[2]."-".$f_fin[1]."-".$f_fin[0];
        $dat=new Api_Model_DbTable_Bankreceipts();
        if ($uid=="") {
            $drec=$dat->_getBankreceiptsBetween2Dates($fini,$ffin);

        }else{
            $drec=$dat->_getBankreceiptsBetween2DatesXuid($fini,$ffin,$uid);
         }
        if ($drec) {
            $this->view->drecibo=$drec;
        }else{
            $this->view->nodata="1";
        }

    }
    public function listperiodAction(){
        try {
            $this->_helper->layout->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $anio=$this->_getParam('anio');
            $data = array('eid' => $eid, 'oid' => $oid, 'year' => $anio);
            $per = new Api_Model_DbTable_Periods();
            $data_per = $per->_getPeriodsxYears($data);
            $this->view->data_per=$data_per;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function assignAction(){
        $this->_helper->layout()->disableLayout();
        $data=new Api_Model_DbTable_Bankreceipts();
        $perso=new Api_Model_DbTable_Users();
        $dperi=new Api_Model_DbTable_Periods();
        
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $perid=base64_decode($this->_getParam('perid'));
        $uid=base64_decode($this->_getParam('uid'));
        $operation=base64_decode($this->_getParam('operacion'));
        $fini=$this->_getParam("fini");
        $ffin=$this->_getParam("ffin");

        $this->view->fini=$fini;
        $this->view->ffin=$ffin;

        $where = array('operation'=>$operation,'code_student'=>$uid,'perid'=>$perid);
        $datrec=$data->_getFilter($where);
        $this->view->datarec=$datrec;
        
        $dato = array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid);
        $dataper=$perso->_getUserXUid($dato);
        $this->view->nomalum=$dataper;

    }

    public function assignedAction(){

        $this->_helper->layout()->disableLayout();
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $new_uid=base64_decode($this->_getParam('uid'));
        $new_concept=base64_decode($this->_getParam('concept'));

        $old_operation=base64_decode($this->_getParam('operation'));
        $old_uid=base64_decode($this->_getParam('codestudent'));
        $old_perid=base64_decode($this->_getParam('perid'));
        
        $bankDB=new Api_Model_DbTable_Bankreceipts();

        $pk = array('operation'=>$old_operation,'code_student'=>$old_uid);
        $data = array('concept'=>$new_concept,'code_rect'=>$new_uid);

        if ($bankDB->_update($data,$pk)) {
            $json = array('sms'=>"Se actualizó correctamente",'status'=>true);
        }
        else{
            $json = array('sms'=>"Hubo un error al Reasignar",'status'=>false);
        }
        $this->_response->setHeader('Content-Type', 'application/json');
        $this->view->data = $json;

    }

    public function removeAction(){
        $this->_helper->layout()->disableLayout();
        $uid = $this->_getParam('uid');
        $operation = $this->_getParam('num_operacion');
        $pag = new Api_Model_DbTable_PaymentsDetail();
        $pk['uid']=$uid;
        $pk['operation']=$operation;
        if ($pag->_delete($pk)) {
            $recban= new Api_Model_DbTable_Bankreceipts();
            $str['operation']=$operation ;
            $str['code_student']=$uid;
            $datos['processed']="N";
            
            if ($recban->_update($datos,$str)) { 
                $this->_helper->redirector("index");
            }
        }
    }

    public function loadreceiptAction(){
        try {
            $uid = $this->sesion->uid;
            $form = new Admin_Form_Receipt();
            if ($this->getRequest()->isPost())
            {
                $formdata = $this->getRequest()->getPost();
                if ($form->isValid($formdata)){
                    unset($formdata['save']);
                    unset($formdata['anios']);
                    $formdata['processed'] = 'N';
                    $formdata['payment_date']= date('Y-m-d', strtotime($formdata['payment_date']));
                    $formdata['code_rect'] = $formdata['code_student']; 
                    $formdata['created_uid'] = $uid1;    
                    $bank = new Api_Model_DbTable_Bankreceipts();
                    if ($bank->_save($formdata)) {
                        $this->_helper->redirector('index');                        
                    }
                }
                else{
                    $form->populate($formdata);
                }
            }
            $this->view->form = $form;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

}