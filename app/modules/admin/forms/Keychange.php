<?php

class Admin_Form_Keychange extends Zend_Form{    
    public function init(){
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
        $eid=$login->eid;
        $oid=$login->oid;
        $this->setName("frmcambioclave");
        $uid= new Zend_Form_Element_Text('uid');
        $uid->removeDecorator('Label')->removeDecorator('HtmlTag'); 
        $uid->setAttrib("maxlength", "10");
        $uid->setAttrib('class', 'form-control');

        
        $acla= new Zend_Form_Element_Password("acla");
        $acla->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $acla->setAttrib("maxlength", "30")->setAttrib("size", "30");
        $acla->setAttrib('class', 'form-control');
        $acla->setRequired(true)->addErrorMessage('Este campo es requerido');

        $ncla= new Zend_Form_Element_Password("ncla");
        $ncla->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $ncla->setAttrib("maxlength", "30")->setAttrib("size", "30");
        $ncla->setAttrib('class', 'form-control');
        $ncla->setRequired(true)->addErrorMessage('Este campo es requerido');

        $rcla= new Zend_Form_Element_Password("rcla");
        $rcla->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $rcla->setAttrib("maxlength", "30")->setAttrib("size", "30");
        $rcla->setAttrib('class', 'form-control');
        $rcla->setRequired(true)->addErrorMessage('Este campo es requerido');

        // $rol= new Zend_Form_Element_Select('rid');
        // $rol->removeDecorator('Label');
        // $rol->removeDecorator('HtmlTag');        
        // $rol->addMultiOption("","Selecione el Rol");
        // $listar = new Admin_Model_DbTable_Rol();
        // $list= $listar->_getTodosRolesXOrganizacion($eid,$oid);
        // foreach ($list as $pr){
        //         $rol->addMultiOption($pr['rid'],$pr['nombre_rol']);
        // }

        // $org= new Zend_Form_Element_Select('oid');
        // $org->removeDecorator('Label');
        // $org->removeDecorator('HtmlTag');        
        // $org->addMultiOption("","Selecione la Organizacion");
        // $listar = new Admin_Model_DbTable_Organizacion();
        // $list= $listar->_getTodasOrganizaciones($eid);
        // foreach ($list as $pr){
        //         $org->addMultiOption($pr['oid'],$pr['nombre']);
        // }

        $submit1 = new Zend_Form_Element_Submit('guardar');
        $submit1->setAttrib('class', 'btn btn-success');
        $submit1->setLabel('Guardar');
        $submit1->removeDecorator("HtmlTag")->removeDecorator("Label");

        //$this->addElements(array($uid,$acla,$ncla,$rcla,$rol,$org,$submit1));  

        $this->addElements(array($uid,$acla,$ncla,$rcla,$submit1));         
    }
}

