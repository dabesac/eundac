<?php

class Rfacultad_Form_Search extends Zend_Form{    
    public function init(){
        

        $this->setName("frmbuscar");
        
        $pid= new Zend_Form_Element_Text("pid");
        $pid->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $pid->setAttrib("maxlength", "10")->setAttrib("size", "10");
        $pid->setRequired(true)->addErrorMessage('Este campo es requerido');
        $pid->setAttrib("title","Codigo");
		$pid->setAttrib("class","span2");
        


        $uid= new Zend_Form_Element_Text("uid");
        $uid->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $uid->setAttrib("maxlength", "10")->setAttrib("size", "10");
        $uid->setAttrib("title","Codigo");
		$uid->setAttrib("class","input-small");
		$uid->setRequired(true)->addErrorMessage('Este campo es requerido');


        $uids= new Zend_Form_Element_Text("uids");
        $uids->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $uids->setAttrib("maxlength", "10")->setAttrib("size", "10");
        $uids->setRequired(true)->addErrorMessage('Este campo es requerido');
        $uids->setAttrib("title","Codigo");


        $nom= new Zend_Form_Element_Text("nom");
        $nom->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $nom->setAttrib("maxlength", "40")->setAttrib("size", "40");
        $nom->setRequired(true)->addErrorMessage('Este campo es requerido');
        $nom->setAttrib("title","Codigo");

     

        $submit = new Zend_Form_Element_Submit('buscar');
        $submit->setAttrib('class', 'btn btn-primary');
        $submit->setLabel('buscar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($pid,$uid,$uids,$nom,$submit));        
     }
}
