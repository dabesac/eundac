<?php
class Profile_Form_Academic extends Zend_Form{    

    public function init(){

    	$institution= new Zend_Form_Element_Text('institution');
        $institution->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $institution->setAttrib("maxlength", "30")->setAttrib("size", "30");
        $institution->setRequired(true)->addErrorMessage('Este campo es requerido');
        $institution->setAttrib("title","Institucion");
        $institution->setAttrib("class","form-control");

		$year_end= new Zend_Form_Element_Text('year_end');
        $year_end->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $year_end->setAttrib("maxlength", "4")->setAttrib("size", "30");
        $year_end->setRequired(true)->addErrorMessage('Este campo es requerido');
        $year_end->setAttrib("title","Institucion");
        $year_end->setAttrib("class","form-control");

        $type = new Zend_Form_Element_Select('type');
        $type->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $type->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $type->addMultiOption("SE","Secundaria");
        $type->addMultiOption("SU","Superior");
        $type->setAttrib("class","form-control"); 

        $tittle=new Zend_Form_Element_Text('tittle');
        $tittle->removeDecorator('HtmlTag')->removeDecorator('Label');
        $tittle->setAttrib("maxlength", "30")->setAttrib("size","30");
        $tittle->setRequired(true)->addErrorMessage('Este campo es necesario');
        $tittle->setAttrib("tittle","Titulo");
        $tittle->setAttrib("class","form-control");

        $submit=new Zend_Form_Element_Submit('save');
        $submit->setAttrib("class","btn btn-info pull-right");
        $submit->setLabel("Guardar");
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($institution, $year_end, $type, $tittle, $submit));

   	}

}