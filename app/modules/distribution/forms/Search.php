<?php
class Distribution_Form_Search extends Zend_Form{    
    public function init(){
        $this->setName("frmSearch");

        $name= new Zend_Form_Element_Text('name');
        $name->setAttrib('class','form-control');
        $name->removeDecorator('Label')->removeDecorator('HtmlTag');
        $name->setAttrib("placeholder","Ingrese Apellidos");
        $name->setAttrib("onkeypress","return soloLetras(event)");
        $name->setAttrib("maxlength", "50");
        
        $pid= new Zend_Form_Element_Text('pid');
        $pid->setAttrib('class','form-control');
        $pid->removeDecorator('Label')->removeDecorator('HtmlTag'); 
        $pid->setAttrib("placeholder","Ingrese DNI");
        $pid->setAttrib("onkeypress","return soloNumero(event)");
        $pid->setAttrib("maxlength", "10");
       
        $submit = new Zend_Form_Element_Submit('enviar');
        $submit->removeDecorator("DtDdWrapper");
        $submit->setAttrib('class', 'btn btn-success');
        $submit->setLabel('Buscar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($name,$pid,$submit));        
    }
}