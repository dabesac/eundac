<?php
class Admin_AclController extends Zend_Controller_Action{

	public function init(){
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
        try 
        {
            $eid=$this->eid;
            $oid=$this->oid;

            $dbrol=new Api_Model_DbTable_Rol();
            $where=array("eid"=>$eid,"oid"=>$oid);
            $order=array("name");
            $rols=$dbrol->_getAll($where,$order);
            //print_r($rols);
            $this->view->rols=$rols;

            //New Acl
            //print_r($rid);
            $form=new Admin_Form_Acl();
            $form->save->setLabel("Guardar");
            $this->view->form=$form;
            if ($this->getRequest()->isPost()){
                $formdata=$this->getRequest()->getPost();
                //print_r($formdata);
                if($form->isValid($formdata)){
                    unset($formdata['save']);
                    $formdata['eid']=$eid;
                    $formdata['oid']=$oid;
                    trim($formdata['reid']);
                    trim($formdata['state']);
                    //print_r($formdata);
                    $dbacl=new Api_Model_DbTable_Acl();
                    $saveacl=$dbacl->_save($formdata);
                    //$this->view->formdata=$formdata;
                }
            }

        } 
        catch (Exception $ex) 
        {
            print "Error listando al Crear Roles: ".$ex->getMessage();
        }

    }


    public function listaclAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $eid=$this->eid;
            $oid=$this->oid;
            $rid=$this->_getParam("rid");
            $dbacl=new Api_Model_DbTable_Acl();
            $where=array("eid"=>$eid,"oid"=>$oid,"rid"=>$rid);
            $acl=$dbacl->_getFilter($where);
            //print_r($acl);
            $c=0;
            $attrib=array("name","reid");
            foreach ($acl as $a) {
                $where=array("eid"=>$eid,"oid"=>$oid,"reid"=>$a['reid']);
                $recxacl[$c]=$dbacl->_getinfoResource($where,$attrib);
                $c++;
            }
            //print_r($recxacl);
            $this->view->rid=$rid;
            $this->view->aclname=$recxacl;

        } 
        catch (Exception $ex) 
        {
            print "Error listando al Crear Roles: ".$ex->getMessage();
        }
    }


     public function deleteAction()
    {
        try{
            $rid=$this->_getParam("rid");
            $eid=$this->eid;
            $oid=$this->oid;
            $reid=$this->_getParam("reid");
            $where=array("eid"=>$eid, "oid"=>$oid, "reid"=>$reid, "rid"=>$rid);
            print_r($where);
            $dbdelacl=new Api_Model_DbTable_Acl();
            if($dbdelacl->_delete($where)){
                $this->_helper->_redirector("index");
            }else{
                print_r("Error al Eliminar ACL!!");
            }

        } 
        catch (Exception $ex) 
        {
            print "Error al guardar: ".$ex->getMessage();
        }
    }

	
}