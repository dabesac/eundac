<?php

class Docente_Form_Speciality extends Zend_Form{    
    public function init(){
        $this->setName("frmSpeciality");

        // $number= new Zend_Form_Element_Text("number");
        // $number->setAttrib("class","form-control");
        // $number->setAttrib('readonly',true);
        // $number->removeDecorator("HtmlTag")->removeDecorator("Label");

        $header= new Zend_Form_Element_Textarea("header");
        $header->setAttrib('rows','12');
        $header->setRequired(true)->addErrorMessage('Rellene Sumilla');
        $header->setAttrib("class","form-control");
        $header->setAttrib("placeholder","Ingrese texto");
        $header->removeDecorator("HtmlTag")->removeDecorator("Label");       
      
        $save= new Zend_Form_Element_Submit('save');
        $save->removeDecorator("HtmlTag")->removeDecorator("Label");
        $save->setAttrib("class","btn btn-success");
        $save->setLabel("Guardar Avance");

        $close= new Zend_Form_Element_Submit('close');
        $close->removeDecorator("HtmlTag")->removeDecorator("Label");
        $close->setAttrib("class","btn btn-danger");
        $close->setLabel("Cerrar Enviar");

        $this-> addElements(array($header,$save,$close));
    }
}