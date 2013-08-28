<?php
class Admin_Form_Module extends Zend_Form
{
	public function init()
	{
		$this->setName("frmDistribution");
		$this->setAction("/admin/module/new");
					
        $mid= new Zend_Form_Element_Hidden('mid');
        $mid->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $mid->addErrorMessage('Este campo es Obligatorio');
        $mid->setAttrib("class","form-control input-sm");
        $mid->setAttrib("readonly","true");
		
		$name= new Zend_Form_Element_Text('name');
        $name->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $name->setAttrib("maxlength", "50")->setAttrib("size", "10");
        $name->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $name->setAttrib("title","Nombre");
        $name->setAttrib("class","form-control");      

        $module= new Zend_Form_Element_Text('module');
        $module->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $module->setAttrib("maxlength", "50");
        $module->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $module->setAttrib("title","Nombre Modulo");
        $module->setAttrib("class","form-control input-sm");
        
        $state = new Zend_Form_Element_Select("state");
        $state->setRequired(true);
        $state->removeDecorator('Label');
        $state->removeDecorator('HtmlTag');
        $state->setAttrib("class","form-control ");
        $state->addMultiOption("A","Activo");
        $state->addMultiOption("I","Inactivo");
        
        
        $icon = new Zend_Form_Element_Text("imgicon");
        $icon->removeDecorator('Label');
        $icon->removeDecorator('HtmlTag');
        $icon->setAttrib("class","form-control input-small");
       
        

        $submit = new Zend_Form_Element_Submit('save');
        $submit->removeDecorator("DtDdWrapper");
        $submit->setAttrib('class', 'btn btn-success pull-right');
        $submit->setLabel('GUARDAR');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($mid, $name,$module,$icon,$state, $submit));
	}
}
