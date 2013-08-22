<?php

class Syllabus_Form_Syllabus extends Zend_Form{    
    public function init(){
        $this->setName("frmSyllabus");

        $number= new Zend_Form_Element_Text("number");
        $number->setAttrib("class","form-control");
        $number->setAttrib('readonly',true);
        $number->removeDecorator("HtmlTag")->removeDecorator("Label");

        $sumilla= new Zend_Form_Element_Textarea("sumilla");
        $sumilla->setAttrib('rows','12');
        $sumilla->setRequired(true)->addErrorMessage('Rellene Sumilla');
        $sumilla->setAttrib("class","form-control");
        $sumilla->setAttrib("placeholder","Ingrese texto");
        $sumilla->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $competency= new Zend_Form_Element_Textarea("competency");
        $competency->setAttrib('rows','10');
        $competency->setRequired(true)->addErrorMessage('Rellene Competencia');
        $competency->setAttrib("class","form-control");
        $competency->setAttrib("placeholder","Ingrese texto");
        $competency->removeDecorator("HtmlTag")->removeDecorator("Label");

        $capacity= new Zend_Form_Element_Textarea("capacity");
        $capacity->setAttrib('rows','10');
        $capacity->setRequired(true)->addErrorMessage('Rellene Capacidades');
        $capacity->setAttrib("class","form-control");
        $capacity->setAttrib("placeholder","Ingrese texto");
        $capacity->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $units= new Zend_Form_Element_Text("units");
        $units->setRequired(true)->addErrorMessage('Rellene unidad');
        $units->setAttrib("class","form-control");
        $units->setAttrib("maxlength","2");
        $units->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $methodology= new Zend_Form_Element_Textarea("methodology");
        $methodology->setAttrib('rows', '14');
        $methodology->setAttrib("class","form-control");
        $methodology->setAttrib("placeholder","Ingrese texto");
        $methodology->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $media= new Zend_Form_Element_Textarea("media");
        $media->setAttrib('rows', '14');
        $media->setRequired(true)->addErrorMessage('Rellene medios');
        $media->setAttrib("class","form-control");
        $media->setAttrib("placeholder","Ingrese texto");
        $media->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $evaluation= new Zend_Form_Element_Textarea("evaluation");
        $evaluation->setAttrib('rows', '14');
        $evaluation->setRequired(true)->addErrorMessage('Rellene evaluación');
        $evaluation->setAttrib("class","form-control");
        $evaluation->setAttrib("placeholder","Ingrese texto");
        $evaluation->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $sources= new Zend_Form_Element_Textarea("sources");
        $sources->setAttrib('rows', '14');
        $sources->setRequired(true)->addErrorMessage('Rellene Fuentes');
        $sources->setAttrib("class","form-control");
        $sources->setAttrib("placeholder","Ingrese texto");
        $sources->removeDecorator("HtmlTag")->removeDecorator("Label");

        $save= new Zend_Form_Element_Submit('save');
        $save->removeDecorator("HtmlTag")->removeDecorator("Label");
        $save->setAttrib("class","btn btn-success");
        $save->setLabel("Guardar Avance");

        $close= new Zend_Form_Element_Submit('close');
        $close->removeDecorator("HtmlTag")->removeDecorator("Label");
        $close->setAttrib("class","btn btn-danger");
        $close->setLabel("Cerrar Enviar");

        $this-> addElements(array($number,$sumilla,$competency,$capacity,$units,
                            $methodology,$media,$evaluation,$sources,$save,$close));
    }
}