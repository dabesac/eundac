<?php
class Docente_LogoheaderController extends Zend_Controller_Action {

    public function init()
    {
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        if (!$login->rol['module']=="docente"){
            $this->_helper->redirector('index','index','default');
        }
        $this->sesion = $login; 
    }
    public function indexAction(){
        try {
        //print_r($this->sesion);
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $escid = $this->sesion->escid;
        $subid = $this->sesion->subid;
        $this->view->facultad = $this->sesion->faculty->name;
        $this->view->speciality = $this->sesion->speciality->name;
        $wherescid= array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
        
        $esc= new Api_Model_DbTable_Speciality();
        $datescid=$esc->_getOne($wherescid);      

        $date=$datescid['header'];
        $this->view->header=$date;
        
        $form= new Docente_Form_Logo();
        $wherescid= array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
        $esc= new Api_Model_DbTable_Speciality();
        $datescid=$esc->_getOne($wherescid);
        //print_r($datescid['header']);
        $form->populate($datescid);

        if ($this->getRequest()->isPost()) 
        {

            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                unset($formData['MAX_FILE_SIZE']);
                unset($formData['save']);
                $upload = new Zend_File_Transfer_Adapter_Http();
                $filterRename = new Zend_Filter_File_Rename(array('target' => '/srv/www/app-academico/eundac/html/header/' .$eid."_".$oid."_".$escid."_".$subid, 'overwrite' => false));
                $upload->addFilter($filterRename);
                if (!$upload->receive()) {
                    $this->view->message = 'Error receiving the file';
                   
                }
                $pk=array();
                $pk['eid']=$eid;
                $pk['oid']=$oid;                           
                $pk['escid']=$escid;                     
                $pk['subid']=$subid;
                $formData['header']=$eid."_".$oid."_".$escid."_".$subid;
                if($esc->_update($formData,$pk))
                {
                    $this->_redirect('/docente/logoheader/index');              
                  
                }
            }

         }
            $this->view->form=$form;
        } catch (Exception $e) {
             print "Error: ".$e->getMessage();
        }
    }
}