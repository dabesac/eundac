<?php

class Docente_EditheaderController extends Zend_Controller_Action {

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
            $this->_redirect('/docente/editheader/index2');              

            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $escid = $this->sesion->escid;
            $subid = $this->sesion->subid;

            $wherescid= array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);

            $form= new Docente_Form_Speciality();

            $esc= new Api_Model_DbTable_Speciality();
            $datescid=$esc->_getOne($wherescid);      
            //echo $date=$datescid['header'];

            $form->populate($datescid);
            $this->view->form=$form;

            if ($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();
                
                if ($form->isValid($frmdata))
                { 
                    
                    unset($frmdata['save']);
                    $pk=array();
                    $pk['eid']=$eid;
                    $pk['oid']=$oid;                           
                    $pk['escid']=$escid;                     
                    $pk['subid']=$subid;
                    
                    $esc->_update($frmdata,$pk);
                    //print_r($frmdata);

                }

            }

            } catch (Exception $e) {
            print "Error: ".$e->getMessage();
             }

    }


    public function index2Action()
    {
        try {
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $escid = $this->sesion->escid;
        $subid = $this->sesion->subid;

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
                $filename = $upload->getFilename();
                $filename = basename($filename);
                $uniqueToken = md5(uniqid(mt_rand(), true));
                $filterRename = new Zend_Filter_File_Rename(array('target' => '/srv/www/eundac/html/header/' . $uniqueToken.$filename, 'overwrite' => false));
                $upload->addFilter($filterRename);
              

                if (!$upload->receive()) {
                    $this->view->message = 'Error receiving the file';
                    return;
                }

                $pk=array();
                $pk['eid']=$eid;
                $pk['oid']=$oid;                           
                $pk['escid']=$escid;                     
                $pk['subid']=$subid;
                $formData['header']=$uniqueToken.$filename;
                //print_r($formData);
                if($esc->_update($formData,$pk))
                {
                    $this->_redirect('/docente/editheader/index2');              
                  
                }
            }

         }


        $this->view->form=$form;
        

        }
        catch (Exception $e)
        {
            print "Error: ".$e->getMessage();
        }

    }
}