<?php
class Admin_Form_Speciabili extends Zend_Form{
	public function init(){
		$this->setName("frmpercentaje");
		$sesion  = Zend_Auth::getInstance();
		$login = $sesion->getStorage()->read();

		$eid=$login->eid;
		$oid=$login->oid;

		$esc=new Zend_Form_Element_Select('esc');
        $esc->removeDecorator('Label');
        $esc->removeDecorator('HtmlTag');
        $esc->setAttrib('class','form-control');
		$esc->addMultiOption("","- Seleccione una Escuela -");
		$where=array('eid'=>$eid,'oid'=>$oid,'state'=>'A');
		$attrib=array('escid','subid','name');
		$db = new Api_Model_DbTable_Speciality();
		$data = $db->_getFilter($where,$attrib);
		foreach ($data as $data ) {
			$esc->addMultiOption($data['escid'].';--;'.$data['subid'],$data['name']);
		}
		$this->addElements(array($esc));
	}
}