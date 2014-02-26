<?php

class Distribution_Form_Distribution extends Zend_Form{    
    public function init(){
    	
        $distid= new Zend_Form_Element_Hidden("distid");
        $distid->setAttrib("class","form-control");
        $distid->setAttrib('readonly',true);
        $distid->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $perid = new Zend_Form_Element_Select("perid");
        $perid->setRequired(true);
        $perid->removeDecorator('Label');
        $perid->removeDecorator('HtmlTag');
        $perid->setAttrib("class","form-control");
        $perid->addMultiOption("","Selecione Periodo");
        //$perid->addMultiOption("12B","12B Periodo");

        
        $number= new Zend_Form_Element_Text("number");
        $number->setAttrib("class","form-control");
        $number->setRequired(true);
        $number->setAttrib("required","");
        $number->setAttrib('readonly',true);
        $number->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $datepress= new Zend_Form_Element_Text("datepress");
        $datepress->setAttrib("class","form-control");
        $datepress->setAttrib("required","");
        $datepress->setRequired(true);
        $datepress->removeDecorator("HtmlTag")->removeDecorator("Label");
        

        $dateaccept= new Zend_Form_Element_Text("dateaccept");
        $dateaccept->setAttrib("class","form-control");
        $dateaccept->setAttrib("required","");
        $dateaccept->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $state = new Zend_Form_Element_Select("state");
        $state->setRequired(true);
        $state->removeDecorator('Label');
        $state->removeDecorator('HtmlTag');
        $state->setAttrib("class","form-control");
        $state->addMultiOption("B","Borrador");
        $state->addMultiOption("A","Activo");
        $state->addMultiOption("C","Cerrado");
        
        $this-> addElements(array($perid, $number, $datepress, $dateaccept, $state));
    }
}