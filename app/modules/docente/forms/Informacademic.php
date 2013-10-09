<?php

class Docente_Form_Informacademic extends Zend_Form{    
    public function init(){
        $this->setName("frmInformacademic");
        
        $acad_tuto= new Zend_Form_Element_Textarea("acad_tutoria");
        $acad_tuto->setAttrib("class","form-control");
        $acad_tuto->setAttrib('rows','15');
        $acad_tuto->setRequired(true)->addErrorMessage('Rellene Logros');
        $acad_tuto->removeDecorator("HtmlTag")->removeDecorator("Label");

        $acad_medios= new Zend_Form_Element_Textarea("acad_medios");
        $acad_medios->setAttrib("class","form-control");
        $acad_medios->setAttrib('rows', '15');
        $acad_medios->setRequired(true)->addErrorMessage('Rellene Medios');
        $acad_medios->removeDecorator("HtmlTag")->removeDecorator("Label");

        $adm_labores= new Zend_Form_Element_Textarea("adm_labores") ;
        $adm_labores->setAttrib("class","form-control");
        $adm_labores->setAttrib("placeholder","Ingrese texto");
        $adm_labores->setAttrib('rows', '15');
        $adm_labores->setRequired(true)->addErrorMessage('Rellene Labores');
        $adm_labores->removeDecorator("HtmlTag")->removeDecorator("Label");

        $adm_asesoria= new Zend_Form_Element_Textarea("adm_asesoria");
        $adm_asesoria->setAttrib("class","form-control");
        $adm_asesoria->setAttrib("placeholder","Ingrese texto");
        $adm_asesoria->setAttrib('rows', '15');
        $adm_asesoria->setRequired(true)->addErrorMessage('Rellene Asesoria');
        $adm_asesoria->removeDecorator("HtmlTag")->removeDecorator("Label");
       
        $adm_inves= new Zend_Form_Element_Textarea("adm_investigacion");
        $adm_inves->setAttrib("class","form-control");
        $adm_inves->setAttrib('rows', '15'); 
        $adm_inves->setRequired(true)->addErrorMessage('Rellene Dificultades');
        $adm_inves->removeDecorator("HtmlTag")->removeDecorator("Label");

        $adm_acre= new Zend_Form_Element_Textarea("adm_acreditacion");
        $adm_acre->setAttrib("class","form-control");
        $adm_acre->setAttrib('rows', '15'); 
        $adm_acre->setRequired(true)->addErrorMessage('Rellene Acreditacion');
        $adm_acre->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $enviar = new Zend_Form_Element_Submit('enviar');
        $enviar->setAttrib('class', 'btn btn-primary');
        $enviar->setLabel('Guardar Avance');
        $enviar->removeDecorator("HtmlTag")->removeDecorator("Label")->removeDecorator('DtDdWrapper');

        $cerrar = new Zend_Form_Element_Submit('cerrar');
        $cerrar->setAttrib('class', 'btn btn-danger');
        $cerrar->setLabel('Cerrar Informe');
        $cerrar->removeDecorator("HtmlTag")->removeDecorator("Label")->removeDecorator('DtDdWrapper');

        $this->addElements(array($acad_tuto,$acad_medios,$adm_labores,$adm_asesoria,$adm_inves,$adm_acre,$enviar,$cerrar));
    }
}