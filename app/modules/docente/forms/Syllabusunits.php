<?php
class Docente_Form_Syllabusunits extends Zend_Form{    
    public function init(){
        $this->setName("frmSyllabusunits");

        $name= new Zend_Form_Element_Text("name");
        $name->setAttrib("maxlength","150");
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
        $submit->setAttrib('class','btn btn-info');
        $submit->setLabel('Guardar y Siguiente');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label")->removeDecorator('DtDdWrapper');

        $this-> addElements(array($name,$objetive,$reading,$activity,$submit));
    }
}