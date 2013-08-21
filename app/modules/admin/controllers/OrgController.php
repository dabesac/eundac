<?php

class Admin_OrgController extends Zend_Controller_Action 
{
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
       
    }
    
    public function indexAction() 
    {
        try 
        {
            $where['eid']="20154605046";
            // $oid=$this->sesion->oid;
            $org= new Api_Model_DbTable_Org();
            $dataorg=$org->_getAll($where);
            // print_r($datorg);
            $this->view->dataorg=$dataorg;
            
        } 
        catch (Exception $ex) 
        {
          print "Error aL listar las Organizaciones: ".$ex->getMessage();
        }
    }

     public function newAction()
    {
        try 
        {
            $eid="20154605046";
            $form= new Admin_Form_Org();
            $form->save->setLabel("Guardar");
            $this->view->form=$form;
            if ($this->getRequest()->isPost())
            {
                $formdata = $this->getRequest()->getPost();
                if ($form->isValid($formdata))
                { 
                    unset($formdata['save']);
                    $formdata['eid']=$eid;
                    trim($formdata['oid']);
                    trim($formdata['name']);
                    trim($formdata['header_print']);
                    trim($formdata['fooote_print']);
                    $newrec=new Api_Model_DbTable_Org();
                    $newrec->_save($formdata);
                    //print_r($formdata);
                    $this->_redirect("/admin/org");
                }
                else
                {
                    echo "Ingrese Nuevamente";
                }
            }
            
            
        } 
        catch (Exception $ex) 
        {
          print "Error ".$ex->getMessage();
        }
    }

    public function updateAction()
    {
        $eid="20154605046";
        $oid=$this->_getParam('oid');
        $pk['oid']=$oid;
        $pk['eid']=$eid;
        $dborg=new Api_Model_DbTable_Org();
        $org=$dborg->_getOne($pk);
        //print_r($org);
        $form= new Admin_Form_Org();
        $form-> populate($org);
        $form->oid->setAttrib("readonly","true");
        $this->view->form=$form;
        if ($this->getRequest()->isPost())
        {
            $formdata = $this->getRequest()->getPost();
            if ($form->isValid($formdata))
            { 
                unset($formdata['update']);
                $formdata['eid']=$eid;
                trim($formdata['name']);
                trim($formdata['header_print']);
                trim($formdata['fooote_print']);
                $newrec=new Api_Model_DbTable_Org();
                $newrec->_update($formdata,$pk);
                //print_r($formdata);
                $this->_redirect("/admin/org");
            }
            else
            {
                echo "Ingrese Nuevamente";
            }
        }
    }


    public function deleteAction()
    {
        try 
        {            
            $eid="20154605046";
            $oid=$this->_getParam('oid');
            $pk['oid']=$oid;
            $pk['eid']=$eid;
            $del=new Api_Model_DbTable_Org();
            if ($del->_delete($pk))
            {
                $this->_helper->_redirector("index");
            }
            else
            {
                echo "error al eliminar";
            }
        }
        catch (Exception $ex)
        {
            print "Error al Eliminar la Organizacion ".$ex->getMessage();
        }
    }
}