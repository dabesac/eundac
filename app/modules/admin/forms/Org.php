<?php
class Admin_Form_Org extends Zend_Form
{
	public function init()
	{
        $oid= new Zend_Form_Element_Text('oid');
        $oid->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $oid->setAttrib("maxlength", "3")->setAttrib("size", "10");
        $oid->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $oid->setAttrib("title","Nombre");
        $oid->setAttrib("class","input-medium");


		
		$name= new Zend_Form_Element_Text('name');
        $name->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $name->setAttrib("maxlength", "50")->setAttrib("size", "10");
        $name->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $name->setAttrib("title","Nombre");
        $name->setAttrib("class","input-medium");



        $header= new Zend_Form_Element_Text('header_print');
        $header->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $header->setAttrib("maxlength", "50")->setAttrib("size", "10");
        $header->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $header->setAttrib("title","Header");
        $header->setAttrib("class","input-medium");



        $footer= new Zend_Form_Element_Text('footer_print');
        $footer->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $footer->setAttrib("maxlength", "50")->setAttrib("size", "10");
        $footer->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $footer->setAttrib("title","Footer");
        $footer->setAttrib("class","input-medium");

        

        $submit = new Zend_Form_Element_Submit('save');
        $submit->setAttrib('class', 'btn btn-info');
        $submit->setLabel('Guardar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");



        $submitup = new Zend_Form_Element_Submit('update');
        $submitup->setAttrib('class', 'btn btn-info');
        $submitup->setLabel('Actualizar');
        $submitup->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($oid, $name, $header, $footer, $submit, $submitup));
	}
}
