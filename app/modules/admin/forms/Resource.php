<?php
class Admin_Form_Resource extends Zend_Form
{
	public function init()
	{

	$sesion  = Zend_Auth::getInstance();
	if(!$sesion->hasIdentity() ){
		$this->_helper->redirector('index',"index",'default');
	}
	$login = $sesion->getStorage()->read();
		
        $mid = new Zend_Form_Element_Hidden('mid');
        $this->addElement($mid);
        
        $name= new Zend_Form_Element_Text('name');
        $name->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $name->setAttrib("maxlength", "50")->setAttrib("size", "10");
        $name->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $name->setAttrib("title","Nombre");
        $name->setAttrib("class","form-control");        
        
        $module= new Zend_Form_Element_Text('module');
        $module->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $module->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $module->setAttrib("title","Ingreso nombre controlador");
        $module->setAttrib("class","form-control");
        
        $controller= new Zend_Form_Element_Text('controller');
        $controller->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $controller->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $controller->setAttrib("title","Ingreso nombre controlador");
        $controller->setAttrib("class","form-control");

        $action= new Zend_Form_Element_Text('action');
        $action->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $action->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $action->setAttrib("title","Ingreso nombre controlador");
        $action->setAttrib("class","form-control");
        
        $icon = new Zend_Form_Element_Text("imgicon");
        $icon->removeDecorator('Label');
        $icon->removeDecorator('HtmlTag');
        $icon->setAttrib("class","form-control input-small");
         

        $is_menu = new Zend_Form_Element_Select("is_menu");
        $is_menu->setRequired(true);
        $is_menu->removeDecorator('Label');
        $is_menu->removeDecorator('HtmlTag');
        $is_menu->setAttrib("class","form-control ");
        $is_menu->addMultiOption("S","Si");
        $is_menu->addMultiOption("N","No");
        
        
        $submit = new Zend_Form_Element_Submit('save');
        $submit->setAttrib('class', 'btn btn-success');
        $submit->removeDecorator("DtDdWrapper");
        $submit->setLabel('GUARDAR');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $this->setDefaults(array(
                'is_menu' => 'N',
                ));

        $this->addElements(array($name,$mid,$controller,$module,$action,$icon,$is_menu,$submit));
	}
}
