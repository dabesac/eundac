<?php

class Admin_Form_Password extends Zend_Form{    
    public function init(){
        

        $this->setName("frmclave");
        
        $uid= new Zend_Form_Element_Text("uid");
        $uid->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $uid->setAttrib("maxlength", "10")->setAttrib("size", "10");
        $uid->setAttrib("class","input-medium");
        $uid->setAttrib("placeholder","Ingrese un código");
        $uid->setAttrib('class', 'form-control');
        $uid->setRequired(true)->addErrorMessage('Este campo es requerido');
        

        $nom= new Zend_Form_Element_Text("nom");
        $nom->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $nom->setAttrib("maxlength", "40")->setAttrib("size", "40");
        $nom->setAttrib("class","input-large");
        $nom->setAttrib('class', 'form-control');
        $nom->setAttrib("placeholder","Ingrese Apellido(s) y/o Nombre(s)");
        $nom->setRequired(true)->addErrorMessage('Este campo es requerido');

        $chrol=new Zend_Form_Element_Radio("chrol");
        $chrol->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $chrol->addMultiOptions(array(
            'docente' => 'Docente',
            'alumno' => 'Alumno',
            'otros' => 'Otros',
            ))
        ->setSeparator(' ');
        $chrol->setAttrib('class', 'form-control');

        $submit = new Zend_Form_Element_Submit('buscar');
        $submit->setAttrib('class', 'btn btn-primary');
        $submit->setLabel('Buscar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($uid,$nom,$chrol,$submit));        
    }
}
