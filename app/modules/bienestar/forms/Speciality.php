<?php
class Bienestar_Form_Speciality extends Zend_Form{
	public function init(){

        $this->setName('frmspeciality');

        $sesion = Zend_Auth::getInstance();
        $login = $sesion->getStorage()->read();

        $eid = $login->eid;
        $oid = $login->oid;
        $where = array('eid'=>$eid,'oid'=>$oid);
        $attrib = array('facid','name','state');
        $db = new Api_Model_DbTable_Faculty();
        $data = $db->_getFilter($where,$attrib);
        
        $facid= new Zend_Form_Element_Select('facid');
        $facid->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $facid->setAttrib('class','form-control');
        $facid->addMultiOption("","- Selecione la Facultad -");
        foreach ($data as $value) {
           if ($value['state']=='A') {
                $facid->addMultiOption($value['facid'],$value['name']);
           }
        }

        $escid= new Zend_Form_Element_Select('escid');
        $escid->removeDecorator('Label')->removeDecorator("HtmlTag");
        $escid->setAttrib('class','form-control');
        $escid->addMultiOption("","- Selecione una Facultad -");
        $escid->setRequired(true)->addErrorMessage('Este campo es requerido.');

        $this->addElements(array($facid,$escid));
	}
}