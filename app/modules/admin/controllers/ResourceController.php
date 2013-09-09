<?php

class Admin_ResourceController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	$this->sesion = $login;
    	$this->eid=$login->eid;
		$this->oid=$login->oid;
       
    }
    public function indexAction()
    {
    	try{
            $eid=$this->eid;
        	$oid=$this->oid;
    		$dbresource=new Api_Model_DbTable_Resource();
        	$allresources=$dbresource->_getAll($where);
        	$this->view->allresources=$allresources;            
        }catch(Exception $ex){
            print("Error al listar Recursos");
        }
    	
    }

    public function newAction()
    {
        try{
            $eid=$this->eid;
            $oid=$this->oid;
            $form= new Admin_Form_Resource();
            $this->view->form=$form;
            
            if ($this->getRequest()->isPost())
            {
                $formdata = $this->getRequest()->getPost();
                if ($form->isValid($formdata))
                { 
                    unset($formdata['save']);
                    $formdata['eid']=$eid;
                    $formdata['oid']=$oid;
                    $formdata['reid']=time();
                    $formdata['mid']=base64_decode($formdata['mid']);
                    trim($formdata['name']);
                    $dbrec=new Api_Model_DbTable_Resource();
                    if ($rec=$dbrec->_save($formdata))
                    	$this->_redirect("/admin/resource");
                }
                else
                {
                	$form->populate($formdata);
                    echo "Ingrese Nuevamente";
                }
            }
        }catch(Exception $ex){
            print("Error al listar Recursos");
        }
    }


    public function updateAction()
    {
        try{
            
            $eid=$this->eid;
            $oid=$this->oid;
            $reid=base64_decode($this->_getParam('reid'));
            $form= new Admin_Form_Resource();
            if ($this->getRequest()->isPost())
            {
                $formdata = $this->getRequest()->getPost();
                if ($form->isValid($formdata))
                { 
                    unset($formdata['save']);
                    $formdata['eid']=$eid;
                    $formdata['oid']=$oid;
                    trim($formdata['reid']);
                    $formdata['mid']=base64_decode($formdata['mid']);
                    trim($formdata['name']);
                    print_r($formdata);
                    $dbuprec=new Api_Model_DbTable_Resource();
                    $pk=array("eid"=>$eid,"oid"=>$oid,"reid"=>$reid);
                    if ($rec=$dbuprec->_update($formdata,$pk))
                    	$this->_redirect("/admin/resource");
                }
                else
                {
                   $form->populate($formdata);
                }
            }else{
            	$where=array("eid"=>$eid,"oid"=>$oid,"reid"=>$reid);
            	$dbrec=new Api_Model_DbTable_Resource();
            	$rec=$dbrec->_getOne($where);
            	$rec['mid']=base64_encode($rec['mid']);
            	if ($rec) $form->populate($rec);
            }
            $this->view->form=$form;
            
            
        }catch(Exception $ex){
            print("Error al listar Recursos");
        }
    }

    public function deleteAction()
    {
        try{
            $eid=$this->eid;
            $oid=$this->oid;
            $reid=$this->_getParam('reid');
            $pk=array("eid"=>$eid,"oid"=>$oid,"reid"=>$reid);
            $del=new Api_Model_DbTable_Resource();
            if ($del->_delete($pk))
            {
                $this->_helper->_redirector("index");
            }
            else
            {
                echo "error al eliminar";
            }
        }catch(Exception $ex){
            print("Error al listar Recursos");
        }
    }
}