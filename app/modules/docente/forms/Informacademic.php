<?php

class Docente_Form_Informacademic extends Zend_Form{    
    public function init(){
        $this->setName("frmInformacademic");
        
        $acad_log= new Zend_Form_Element_Textarea("acad_logros");
        $acad_log->setAttrib("class","form-control");
        $acad_log->setAttrib('rows','15');
        $acad_log->setRequired(true)->addErrorMessage('Rellene Logros');
        $acad_log->removeDecorator("HtmlTag")->removeDecorator("Label");

        $acad_medios= new Zend_Form_Element_Textarea("acad_medios");
        $acad_medios->setAttrib("class","form-control");
        $acad_medios->setAttrib('rows', '15');
        $acad_medios->setRequired(true)->addErrorMessage('Rellene Medios');
        $acad_medios->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $acad_dif= new Zend_Form_Element_Textarea("acad_dificultades");
        $acad_dif->setAttrib("class","form-control");
        $acad_dif->setAttrib("placeholder","Ingrese texto");
        $acad_dif->setAttrib('rows', '15');
        $acad_dif->setRequired(true)->addErrorMessage('Rellene Diferencias');
        $acad_dif->removeDecorator("HtmlTag")->removeDecorator("Label");

        $acad_sug= new Zend_Form_Element_Textarea("acad_sugerencias");
        $acad_sug->setAttrib("class","form-control");
        $acad_sug->setAttrib("placeholder","Ingrese texto");
        $acad_sug->setAttrib('rows', '15');
        $acad_sug->setRequired(true)->addErrorMessage('Rellene Sugerencias');
        $acad_sug->removeDecorator("HtmlTag")->removeDecorator("Label");

        $adm_log= new Zend_Form_Element_Textarea("adm_logros");
        $adm_log->setAttrib("class","form-control");
        $adm_log->setAttrib("placeholder","Ingrese texto");
        $adm_log->setAttrib('rows', '15');
        $adm_log->setRequired(true)->addErrorMessage('Rellene Logros');
        $adm_log->removeDecorator("HtmlTag")->removeDecorator("Label");

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
       
        $adm_dif= new Zend_Form_Element_Textarea("adm_dificultades");
        $adm_dif->setAttrib("class","form-control");
        $adm_dif->setAttrib('rows', '15'); 
        $adm_dif->setRequired(true)->addErrorMessage('Rellene Dificultades');
        $adm_dif->removeDecorator("HtmlTag")->removeDecorator("Label");

        $adm_sug= new Zend_Form_Element_Textarea("adm_sugerencias");
        $adm_sug->setAttrib("class","form-control");
        $adm_sug->setAttrib('rows', '15'); 
        $adm_sug->setRequired(true)->addErrorMessage('Rellene Sugerencias');
        $adm_sug->removeDecorator("HtmlTag")->removeDecorator("Label");

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
        $cerrar->setLabel('Cerrar');
        $cerrar->removeDecorator("HtmlTag")->removeDecorator("Label")->removeDecorator('DtDdWrapper');

        $this->addElements(array($acad_log,$acad_medios,$acad_dif,$acad_sug,$adm_log,$adm_labores,$adm_asesoria,$adm_dif,$adm_sug,$adm_acre,$enviar,$cerrar));
    }
}