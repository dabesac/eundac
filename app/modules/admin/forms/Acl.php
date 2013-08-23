<?php
class Admin_Form_Acl extends Zend_Form
{
	public function init()
	{
		//----------------
			$eid="20154605046";
			$oid="1";

		//-----------------

            
        $reid = new Zend_Form_Element_Select('reid');
        $reid->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $reid->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $reid->addMultiOption("","Resource");
        $reid->setAttrib("class","form-control");

		$dbrecxacl=new Api_Model_DbTable_Acl();
        $where=array("eid"=>$eid,"oid"=>$oid);
        $attrib=array("name","reid");
        $recxacl=$dbrecxacl->_getinfoResource($where,$attrib);
        $c=0;
        foreach ($recxacl as $recnm) {
            $reid->addMultiOption($recnm['reid'],$recnm['name']);
        	$c++;
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

        $this->addElements(array($reid, $state, $submit));
	}
}