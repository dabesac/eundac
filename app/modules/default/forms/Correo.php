<?php
class Default_Form_Correo  extends Zend_Form{    
    public function init(){
        $this->setName("frmcorreo");
           
        $correo_personal = new Zend_Form_Element_Text('correo_per');
        $correo_personal->setRequired(true)->addErrorMessage('Este campo es requerido');
        $correo_personal->removeDecorator('Label')->removeDecorator("HtmlTag");
        $correo_personal->setAttrib("title","tunombre@gmail.com/hotmail.com");
        $correo_personal->removeDecorator('Label')->removeDecorator("HtmlTag");

        $correo_emisor = new Zend_Form_Element_Text('correo_per');
        $correo_emisor->setRequired(true)->addErrorMessage('Este campo es requerido');
        $correo_emisor->removeDecorator('Label')->removeDecorator("HtmlTag");
        $correo_emisor->setAttrib("title","tunombre@gmail.com/hotmail.com");
        $correo_emisor->removeDecorator('Label')->removeDecorator("HtmlTag");

        $asunto = new Zend_Form_Element_Text('title');
        $asunto->setRequired(true)->addErrorMessage('Este campo es requerido');
        $asunto->removeDecorator('Label')->removeDecorator("HtmlTag");
        $asunto->setAttrib("class","form-control");


        $comunicado= new Zend_Form_Element_Textarea("Asunto") ;
        $comunicado->setAttrib('rows', '15');
        $comunicado->setAttrib("class","form-control");
        $comunicado->setAttrib("placeholder","Ingrese texto");
        $comunicado->removeDecorator("HtmlTag")->removeDecorator("Label"); 

        $submit = new Zend_Form_Element_Submit('guardar');
        $submit->removeDecorator('HtmlTag'); 
        $submit->removeDecorator('Label');
        $submit->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator('DtDdWrapper');
        $submit->setAttrib('class', 'btn btn-success');

    
        $this->addElements(array($correo_personal,$correo_emisor,$asunto,$comunicado,$submit)); 

    }
}
