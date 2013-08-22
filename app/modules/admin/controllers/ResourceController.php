<?php

class Admin_ResourceController extends Zend_Controller_Action {

    public function init()
    {
    	/*$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (!$login->modulo=="admin"){
    		$this->_helper->redirector('index','index','default');
    	}
    	$this->sesion = $login;*/
    	$this->eid='20154605046';
		$this->oid='1';
       
    }
    public function indexAction()
    {
    	try{
            $eid=$this->eid;
        	$oid=$this->oid;
    		$dbresource=new Api_Model_DbTable_Resource();
        	$allresources=$dbresource->_getAll($where);
        	print_r($resource);
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
            $form->save->setLabel("Guardar");
            $this->view->form=$form;
            
            if ($this->getRequest()->isPost())
            {
                $formdata = $this->getRequest()->getPost();
                if ($form->isValid($formdata))
                { 
                    unset($formdata['save']);
                    $formdata['eid']=$eid;
                    $formdata['oid']=$oid;
                    trim($formdata['reid']);
                    trim($formdata['name']);
                    //print_r($formdata);
                    $dbrec=new Api_Model_DbTable_Resource();
                    $rec=$dbrec->_save($formdata);
                    $this->_redirect("/admin/resource");
                }
                else
                {
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
            $reid=$this->_getParam('reid');
            $where=array("eid"=>$eid,"oid"=>$oid,"reid"=>$reid);
            $dbrec=new Api_Model_DbTable_Resource();
            $rec=$dbrec->_getOne($where);
            //print_r($rec);
            $form= new Admin_Form_Resource();
            $form-> populate($rec);
            $form->reid->setAttrib("readonly","true");
            $this->view->form=$form;
            if ($this->getRequest()->isPost())
            {
                $formdata = $this->getRequest()->getPost();
                if ($form->isValid($formdata))
                { 
                    unset($formdata['save']);
                    $formdata['eid']=$eid;
                    $formdata['oid']=$oid;
                    trim($formdata['reid']);
                    trim($formdata['name']);
                    print_r($formdata);
                    $dbuprec=new Api_Model_DbTable_Resource();
                    $pk=array("eid"=>$eid,"oid"=>$oid,"reid"=>$reid);
                    $rec=$dbuprec->_update($formdata,$pk);
                    $this->_redirect("/admin/resource");
                }
                else
                {
                    echo "Ingrese Nuevamente";
                }
            }
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