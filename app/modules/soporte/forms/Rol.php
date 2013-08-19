<?php
class Soporte_Form_Rol extends Zend_Form{    
    public function init(){

        $rid= new Zend_Form_Element_Text('rid');
        $rid->removeDecorator('Label')->removeDecorator("HtmlTag");
        $rid->setAttrib("maxlength","10")->removeDecorator('Label');
        $rid->setRequired(true)->addErrorMessage('Este campo es requerido.');
        $rid->setAttrib("title","Ingrese un Codigo");
        
        $name= new Zend_Form_Element_Text('name');
        $name->removeDecorator('Label')->removeDecorator("HtmlTag");
        $name->setAttrib("maxlength", "150")->setAttrib("class","input-medium");
        $name->setRequired(true)->addErrorMessage('Este campo es requerido.');
        $name->setAttrib("title","Ingrese Nombre");

        $prefix= new Zend_Form_Element_Text('prefix');
        $prefix->removeDecorator('Label')->removeDecorator("HtmlTag");
        $prefix->setAttrib("maxlength", "5")->setAttrib("class","input-medium");
        // $prefix->setRequired(true)->addErrorMessage('Este campo es requerido.');
        $prefix->setAttrib("title","Ingrese un Prefijo");

        $module= new Zend_Form_Element_Text('module');
        $module->removeDecorator('Label')->removeDecorator("HtmlTag");
        $module->setAttrib("maxlength", "150")->setAttrib("class","input-medium");
        // $module->setRequired(true)->addErrorMessage('Este campo es requerido.');
        $module->setAttrib("title","Ingrese un Modulo");

        $state = new Zend_Form_Element_Select('state');
        $state->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $state->setRequired(true)->addErrorMessage('Es necesario que selecciones el state.');
        $state->addMultiOption("","- Seleccione Estado -");
        $state->addMultiOption("A","Activo");
        $state->addMultiOption("I","Inactivo");


        $submit = new Zend_Form_Element_Submit('send');
        $submit->setAttrib('class', 'btn btn-primary');
        $submit->setLabel('Guardar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($rid,$name,$prefix,$state,$module,$submit));        
    }
}