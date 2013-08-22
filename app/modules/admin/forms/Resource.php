<?php
class Admin_Form_Resource extends Zend_Form
{
	public function init()
	{
        $reid= new Zend_Form_Element_Text('reid');
        $reid->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $reid->setAttrib("maxlength", "3")->setAttrib("size", "10");
        $reid->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $reid->setAttrib("title","Nombre");
        $reid->setAttrib("class","input-sm");
		
		$name= new Zend_Form_Element_Text('name');
        $name->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $name->setAttrib("maxlength", "50")->setAttrib("size", "10");
        $name->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $name->setAttrib("title","Nombre");
        $name->setAttrib("class","input-sm");        

        $submit = new Zend_Form_Element_Submit('save');
        $submit->setAttrib('class', 'btn btn-success');
        $submit->setLabel('Guardar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($reid, $name, $submit, $submitup));
	}
}
