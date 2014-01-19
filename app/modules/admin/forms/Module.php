<?php
class Admin_Form_Module extends Zend_Form
{
	public function init()
        {
                $sesion  = Zend_Auth::getInstance();
                $this->setName("frmAcl");
                $this->setAction("/admin/acl/ne");

        if(!$sesion->hasIdentity() ){
                $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $eid=$login->eid;
        $oid=$login->oid;

        
        $name = new Zend_Form_Element_Text('name');
        $name->setAttrib("class","form-control");
        $name->setAttrib("required","true");
        $name->removeDecorator("HtmlTag")->removeDecorator("Label");
        $name->setRequired(true)->addErrorMessage('Campo Obligatorio');
        $this->addElement($name);
        

        $imgicon = new Zend_Form_Element_Text('imgicon');
        $imgicon->setAttrib("class","form-control");
        $imgicon->setAttrib("required","true");
        $imgicon->setRequired(true)->addErrorMessage('Campo Obligatorio');
        $imgicon->removeDecorator("HtmlTag")->removeDecorator("Label");
        $this->addElement($imgicon);



        $module = new Zend_Form_Element_Text('module');
        $module->setAttrib("class","form-control");
        $module->setAttrib("required","true");
        $module->setRequired(true)->addErrorMessage('Campo Obligatorio');
        $module->removeDecorator("HtmlTag")->removeDecorator("Label");
        $this->addElement($module);
                

        $submit = new Zend_Form_Element_Submit('save');
        $submit->setAttrib('class', 'btn btn-success pull-right');
        $submit->setLabel('Guardar');
        $submit->removeDecorator("DtDdWrapper");
        $this->addElement($submit);
        }
}
