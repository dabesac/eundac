<?php
class Syllabus_Form_Syllabusunits extends Zend_Form{    
    public function init(){
        $this->setName("frmSyllabusunits");

        $name= new Zend_Form_Element_Textarea("name");
        $name->setAttrib('rows','2');
        $name->setAttrib("maxlength","250");
        $name->setAttrib("class","form-control");
        $name->setAttrib("placeholder","Ingrese texto");
        $name->setRequired(true)->addErrorMessage('Ingrese Nombre');
        $name->removeDecorator("HtmlTag")->removeDecorator("Label");

        $objetive= new Zend_Form_Element_Text("objetive") ;
        $objetive->setAttrib("class","form-control");
        $objetive->setAttrib("placeholder","Ingrese texto");
        $objetive->setRequired(true)->addErrorMessage('Ingrese Objetivo.');
        $objetive->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $reading= new Zend_Form_Element_Text("reading") ;
        $reading->setAttrib("class","form-control");
        $reading->setAttrib("placeholder","Ingrese texto");
        $reading->removeDecorator("HtmlTag")->removeDecorator("Label");

        $activity= new Zend_Form_Element_Text("activity") ;
        $activity->setAttrib("class","form-control");
        $activity->setAttrib("placeholder","Ingrese texto");
        $activity->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $submit = new Zend_Form_Element_Submit('guardar');
        $submit->setAttrib('class','btn btn-success');
        $submit->setLabel('Guardar y Siguiente');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label")->removeDecorator('DtDdWrapper');

        $this-> addElements(array($name,$objetive,$reading,$activity,$submit));
    }
}