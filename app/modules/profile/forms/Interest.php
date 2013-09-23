<?php

class Profile_Form_Interest extends Zend_Form{

	public function init(){

		$discipline= new Zend_Form_Element_Select("discipline");
		$discipline->removeDecorator("HtmlTag")->removeDecorator("Label");
		$discipline->setRequired(true)->addErrorMessage("Este Campo es Obligatorio");
		$discipline->setAttrib("class","form-control");
		$discipline->addMultiOption("H","Hobby");
		$discipline->addMultiOption("D","Deporte");
		$discipline->addMultiOption("R","Religion");
		$discipline->addMultiOption("O","Otros");

		$title=new Zend_Form_Element_Text("title");
		$title->removeDecorator("HtmlTag")->removeDecorator("Label");
		$title->setAttrib("maxlength","30")->setAttrib("size","30");
		$title->setRequired(true)->addErrorMessage("Campo Obligatorio");
		$title->setAttrib("title","Nombre");
		$title->setAttrib("class","form-control");

		$club= new Zend_Form_Element_Select("club");
		$club->removeDecorator("HtmlTag")->removeDecorator("Label");
		$club->setRequired(true)->addErrorMessage("Este Campo es Obligatorio");
		$club->setAttrib("class","form-control");
		$club->addMultiOption("S","Si");
		$club->addMultiOption("N","No");

		$submit=new Zend_Form_Element_Submit("save");
		$submit->setAttrib("class","btn btn-info pull-right");
		$submit->setLabel("Guardar");
		$submit->removeDecorator("HtmlTag")->removeDecorator("Label");


		$this->addElements(array($discipline, $title, $club, $submit));


	}
}