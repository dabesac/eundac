<?php
class Profile_Form_Academic extends Zend_Form{    

    public function init(){

    	$institution= new Zend_Form_Element_Text('institution');
        $institution->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $institution->setAttrib("maxlength", "50");
        $institution->setRequired(true)->addErrorMessage('Este campo es requerido');
        $institution->setAttrib("title","Institucion");
        $institution->setAttrib("class","form-control");

        $location= new Zend_Form_Element_Text('location');
        $location->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $location->setAttrib("maxlength", "100")->setAttrib("size", "30");
        $location->setRequired(true)->addErrorMessage('Este campo es requerido');
        $location->setAttrib("title","Institucion");
        $location->setAttrib("class","form-control");

		$year_end= new Zend_Form_Element_Text('year_end');
        $year_end->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $year_end->setAttrib("maxlength", "4")->setAttrib("size", "10");
        $year_end->setRequired(true)->addErrorMessage('Este campo es requerido');
        $year_end->setAttrib("title","Institucion");
        $year_end->setAttrib("class","form-control");

        $type = new Zend_Form_Element_Select('type');
        $type->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $type->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $type->addMultiOption("E","Estatal");
        $type->addMultiOption("P","Particular");
        $type->addMultiOption("PA","Parroquial");
        $type->setAttrib("class","form-control"); 

        $title = new Zend_Form_Element_Select('title');
        $title->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $title->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $title->addMultiOption("SE","Secundaria");
        $title->addMultiOption("SU","Superior");
        $title->addMultiOption("DI","Diplomado");
        $title->addMultiOption("PT","Post Grado");
        $title->addMultiOption("PH","PHD");
        $title->addMultiOption("O","Otros");
        $title->setAttrib("class","form-control"); 

        $this->addElements(array($institution, $location, $year_end, $type, $title));

   	}

}