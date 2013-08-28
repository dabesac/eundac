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
		
        $reid= new Zend_Form_Element_Hidden('reid');
        $reid->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $reid->setAttrib("maxlength", "3")->setAttrib("size", "10");
       
        $reid->setAttrib("readonlu","true");
		
		$name= new Zend_Form_Element_Text('name');
        $name->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $name->setAttrib("maxlength", "50")->setAttrib("size", "10");
        $name->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $name->setAttrib("title","Nombre");
        $name->setAttrib("class","form-control");        
        
        $controller= new Zend_Form_Element_Text('controller');
        $controller->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $controller->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $controller->setAttrib("title","Ingreso nombre controlador");
        $controller->setAttrib("class","form-control");
        
        $icon = new Zend_Form_Element_Text("imgicon");
        $icon->removeDecorator('Label');
        $icon->removeDecorator('HtmlTag');
        $icon->setAttrib("class","form-control input-small");
         
        $state = new Zend_Form_Element_Select("state");
        $state->setRequired(true);
        $state->removeDecorator('Label');
        $state->removeDecorator('HtmlTag');
        $state->setAttrib("class","form-control ");
        $state->addMultiOption("A","Activo");
        $state->addMultiOption("I","Inactivo");
        
        $mid = new Zend_Form_Element_Select("mid");
        $mid->setRequired(true);
        $mid->removeDecorator('Label');
        $mid->removeDecorator('HtmlTag');
        $mid->setAttrib("class","form-control ");
        $modules = new Api_Model_DbTable_Module();
        $data['eid']= $login->eid;
        $data['oid']= $login->oid;
        $rows = $modules->_getAll($data);
        if ($rows){
        	foreach ($rows as $row){
        		$mid->addMultiOption(base64_encode($row['mid']),$row['name']);
        	}
        }
        
        
        $submit = new Zend_Form_Element_Submit('save');
        $submit->setAttrib('class', 'btn btn-success');
        $submit->removeDecorator("DtDdWrapper");
        $submit->setLabel('GUARDAR');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");
        

        $this->addElements(array($reid, $name,$mid,$controller,$icon, $submit,$state));
	}
}
