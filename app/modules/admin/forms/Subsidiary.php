<?php
class Admin_Form_Subsidiary extends Zend_Form{    
    public function init(){
        
        $code= new Zend_Form_Element_Text('subid');
        $code->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $code->setAttrib("maxlength", "4")->setAttrib("size", "10");
        $code->setRequired(true)->addErrorMessage('Este campo es requerido');
        $code->setAttrib("title","Codigo");
        $code->setAttrib("class","input-sm");
        $code->setAttrib("onkeypress","return soloNumero(event)");

        $name= new Zend_Form_Element_Text("name");
        $name->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $name->setAttrib("maxlength", "50")->setAttrib("size", "40");
        $name->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $name->setAttrib("title","Nombre");
        $name->setAttrib("class","input-sm");
        
        $state = new Zend_Form_Element_Select('state');
        $state->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $state->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $state->addMultiOption("A","Activo");
        $state->addMultiOption("I","Inactivo");
        $state->setAttrib("class","input-sm");

        $submit = new Zend_Form_Element_Submit('save');
        $submit->setAttrib('class', 'btn btn-info');
        $submit->setLabel('Guardar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $submitup = new Zend_Form_Element_Submit('update');
        $submitup->setAttrib('class', 'btn btn-info');
        $submitup->setLabel('Actualizar');
        $submitup->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($code,$name,$state,$submit,$submitup));        
    }
}
