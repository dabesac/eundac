<?php
class Admin_Form_Rol extends Zend_Form{    
    public function init(){
        $this->setName("frmrol");

        $rid= new Zend_Form_Element_Text('rid');
        $rid->removeDecorator('Label')->removeDecorator("HtmlTag");
        $rid->setAttrib("maxlength","10")->removeDecorator('Label');
        $rid->setAttrib('class','form-control');
        $rid->setRequired(true)->addErrorMessage('Este campo es requerido.');
        $rid->setAttrib("title","Ingrese un Codigo");
        
        $name= new Zend_Form_Element_Text('name');
        $name->removeDecorator('Label')->removeDecorator("HtmlTag");
        $name->setAttrib("maxlength", "150");
        $name->setAttrib('class','form-control');
        $name->setRequired(true)->addErrorMessage('Este campo es requerido.');
        $name->setAttrib("title","Ingrese Nombre");

        $prefix= new Zend_Form_Element_Text('prefix');
        $prefix->removeDecorator('Label')->removeDecorator("HtmlTag");
        $prefix->setAttrib("maxlength", "5");
        $prefix->setAttrib('class','form-control');
        $prefix->setAttrib("title","Ingrese un Prefijo");

        $module= new Zend_Form_Element_Text('module');
        $module->removeDecorator('Label')->removeDecorator("HtmlTag");
        $module->setAttrib("maxlength", "150");
        $module->setAttrib('class','form-control');
        // $module->setRequired(true)->addErrorMessage('Este campo es requerido.');
        $module->setAttrib("title","Ingrese un Modulo");

        $state = new Zend_Form_Element_Select('state');
        $state->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $state->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $state->setAttrib('class','form-control');
        $state->addMultiOption("","- Seleccione Estado -");
        $state->addMultiOption("A","Activo");
        $state->addMultiOption("I","Inactivo");


        $submit = new Zend_Form_Element_Submit('send');
        $submit->setAttrib('class', 'btn btn-success');
        $submit->setLabel('Guardar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($rid,$name,$prefix,$state,$module,$submit));        
    }
}