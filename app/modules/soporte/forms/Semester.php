<?php
class Soporte_Form_Semester extends Zend_Form{    
    public function init(){

        $semid= new Zend_Form_Element_Text('semid');
        $semid->removeDecorator('Label')->removeDecorator("HtmlTag");
        $semid->setAttrib("maxlength","10")->removeDecorator('Label');
        $semid->setRequired(true)->addErrorMessage('Este campo es requerido.');
        $semid->setAttrib("title","Ingrese un Codigo");
        
        $name= new Zend_Form_Element_Text('name');
        $name->removeDecorator('Label')->removeDecorator("HtmlTag");
        $name->setAttrib("maxlength", "150")->setAttrib("class","input-medium");
        $name->setRequired(true)->addErrorMessage('Este campo es requerido.');
        $name->setAttrib("title","Ingrese Nombre");

        $submit = new Zend_Form_Element_Submit('send');
        $submit->setAttrib('class', 'btn btn-primary');
        $submit->setLabel('Guardar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($semid,$name,$submit));        
    }
}