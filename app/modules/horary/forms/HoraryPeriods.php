<?php
class Horary_Form_HoraryPeriods extends Zend_Form{    
    public function init(){

        $this->setName("frmhoraryperiods");

        $anio = new Zend_Form_Element_Select("anio");
        $anio->removeDecorator('Label');
        $anio->removeDecorator('HtmlTag');
        $anio->setAttrib("class","form-control");
        $anio->addMultiOption("","Selecione Año");
        $year=date('Y');
        for ($i=$year; $i > 2000 ; $i--) { 
            $anio->addMultiOption($i,$i);
        }

        $perids = new Zend_Form_Element_Select("perids");
        $perids->removeDecorator('Label');
        $perids->removeDecorator('HtmlTag');
        $perids->setAttrib("class","form-control");
        $perids->addMultiOption("","Primero seleccione Año");
        
        $this-> addElements(array($perids, $anio));
    }
}