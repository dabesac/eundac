<?php
class Profile_Form_Family extends Zend_Form{    

    public function init(){

    	$institution= new Zend_Form_Element_Text('institution');
        $institution->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $institution->setAttrib("maxlength", "30")->setAttrib("size", "50");
        $institution->setRequired(true)->addErrorMessage('Este campo es requerido');
        $institution->setAttrib("title","Institucion");
        $institution->setAttrib("class","input-sm");

		$year_end= new Zend_Form_Element_Text('year_end');
        $year_end->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $year_end->setAttrib("maxlength", "4")->setAttrib("size", "50");
        $year_end->setRequired(true)->addErrorMessage('Este campo es requerido');
        $year_end->setAttrib("title","Institucion");
        $year_end->setAttrib("class","input-sm");        
   	}

}