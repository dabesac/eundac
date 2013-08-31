<?php
class Distribution_Form_DistributionAdmin extends Zend_Form{    
    public function init(){
        $this->setName("frmdistributionadmin");

        $work = new Zend_Form_Element_Text("work");
        $work->setAttrib('class','form-control');
        $work->setAttrib("maxlength", "250");
        $work->setRequired(true)->addErrorMessage('Este campo es requerido');
        $work->removeDecorator('Label')->removeDecorator("HtmlTag");
        
        $hours = new Zend_Form_Element_Select("hours");
        $hours->setAttrib('class','form-control');
        $hours->setRequired(true)->addErrorMessage('Este campo es requerido');
        $hours->removeDecorator('Label')->removeDecorator("HtmlTag");
        $hours->addMultiOption("","Seleccione");
        for ($i=1; $i <= 20; $i++) { 
            $hours->addMultiOption($i,$i);
        }

        $submit = new Zend_Form_Element_Submit('enviar');
        $submit->removeDecorator("DtDdWrapper");
        $submit->setAttrib("class","btn btn-success");  
        $submit->setAttrib("data-loading-text","Guardando...");
        $submit->setLabel("Guardar");

        $this->addElements(array($work,$hours,$submit));
    }
}