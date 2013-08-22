<?php
class Docente_Form_Syllabusunitcontent extends Zend_Form{    
    public function init(){
        $this->setName("frmSyllabusunitcontent");

        $week= new Zend_Form_Element_Select("week");
        $week->setAttrib("class","form-control");
        $week->setAttrib("maxlength", "2");
        $week->setRequired(true)->addErrorMessage('Seleccione número Semana.');
        $week->removeDecorator("HtmlTag")->removeDecorator("Label");
        for ($i=1; $i < 18; $i++) $week->addMultiOption($i,$i);

        $session= new Zend_Form_Element_Select("session");
        $session->setAttrib("class","form-control");
        $session->setAttrib("maxlength", "2");
        $session->removeDecorator("HtmlTag")->removeDecorator("Label");
        for ($i=1; $i < 52; $i++) $session->addMultiOption($i,$i);        
        
        $obj_content= new Zend_Form_Element_Text("obj_content");
        $obj_content->setAttrib("class","form-control");
        $obj_content->setAttrib("maxlength", "500");
        $obj_content->setAttrib("placeholder","Ingrese texto");
        $obj_content->removeDecorator("HtmlTag")->removeDecorator("Label");

        $obj_strategy= new Zend_Form_Element_Text("obj_strategy") ;
        $obj_strategy->setAttrib("class","form-control");
        $obj_strategy->setAttrib("maxlength", "500");
        $obj_strategy->setAttrib("placeholder","Ingrese texto");
        $obj_strategy->removeDecorator("HtmlTag")->removeDecorator("Label");

        $com_conceptual= new Zend_Form_Element_Text("com_conceptual") ;
        $com_conceptual->setAttrib("class","form-control");
        $com_conceptual->setAttrib("maxlength", "500");
        $com_conceptual->setAttrib("placeholder","Ingrese texto");
        $com_conceptual->removeDecorator("HtmlTag")->removeDecorator("Label");

        $com_procedimental= new Zend_Form_Element_Text("com_procedimental") ;
        $com_procedimental->setAttrib("class","form-control");
        $com_procedimental->setAttrib("maxlength", "500");
        $com_procedimental->setAttrib("placeholder","Ingrese texto");
        $com_procedimental->removeDecorator("HtmlTag")->removeDecorator("Label");

        $com_actitudinal= new Zend_Form_Element_Text("com_actitudinal") ;
        $com_actitudinal->setAttrib("class","form-control");
        $com_actitudinal->setAttrib("maxlength", "500");
        $com_actitudinal->setAttrib("placeholder","Ingrese texto");
        $com_actitudinal->removeDecorator("HtmlTag")->removeDecorator("Label");

        $com_indicators= new Zend_Form_Element_Text("com_indicators") ;
        $com_indicators->setAttrib("class","form-control");
        $com_indicators->setAttrib("maxlength", "500");
        $com_indicators->setAttrib("placeholder","Ingrese texto");
        $com_indicators->removeDecorator("HtmlTag")->removeDecorator("Label");

        $com_instruments= new Zend_Form_Element_Text("com_instruments") ;
        $com_instruments->setAttrib("class","form-control");
        $com_instruments->setAttrib("maxlength", "500");
        $com_instruments->setAttrib("placeholder","Ingrese texto");
        $com_instruments->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $submit = new Zend_Form_Element_Submit('guardar');
        $submit->setAttrib('class','btn btn-info');
        $submit->setLabel('Agregar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label")->removeDecorator('DtDdWrapper');

        $this-> addElements(array($week,$session,$obj_content,$obj_strategy,$com_conceptual,$com_procedimental,$com_actitudinal,$com_indicators,$com_instruments,$submit));
    }
}