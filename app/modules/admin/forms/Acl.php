<?php
class Admin_Form_Acl extends Zend_Form
{
	public function init()
	{
		$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
        $eid=$login->eid;
        $oid=$login->oid;
            
        $reid = new Zend_Form_Element_Select('reid');
        $reid->setAttrib("class","form-control");
        
        $mid = new Zend_Form_Element_Select('mid');
        $mid->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $mid->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $mid->setAttrib("class","form-control");
        
		$dbrecxacl=new Api_Model_DbTable_Module();
        $where=array("eid"=>$eid,"oid"=>$oid);
        $recxacl=$dbrecxacl->_getAll($where);
        $mid->addMultiOption("","Selecciona un modulo");
        foreach ($recxacl as $recnm) {
            $mid->addMultiOption(base64_encode($recnm['mid']),$recnm['name']);
        }


        $state = new Zend_Form_Element_Select('state');
        $state->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $state->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $state->addMultiOption("A","Activo");
        $state->addMultiOption("I","Inactivo");
        $state->setAttrib("class","form-control");

        $submit = new Zend_Form_Element_Submit('save');
        $submit->setAttrib('class', 'btn btn-success');
        $submit->setLabel('Guardar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($reid, $state, $mid,$submit));
	}
}