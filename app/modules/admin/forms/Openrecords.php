<?php
class Admin_Form_Openrecords extends Zend_Form{
	public function init(){

		$this->setName("frmopenrecords");
		$sesion  = Zend_Auth::getInstance();
		$login = $sesion->getStorage()->read();

		$eid=$login->eid;
		$oid=$login->oid;

		$anio= new Zend_Form_Element_Select('anio');
        $anio->removeDecorator('Label');
        $anio->removeDecorator('HtmlTag');
        $anio->setRequired(true)->addErrorMessage('Este campo es requerido');
        $anio->setAttrib('class','form-control');
        $anio->addMultiOption("","- Selecione -");
        $a=date('Y');
        for ($i=$a; $i >1989 ; $i--) {
         	$anio->addMultiOption($i,$i);
        }

        $period=new Zend_Form_Element_Select('period');
        $period->removeDecorator('Label');
        $period->removeDecorator('HtmlTag');
        $period->setAttrib('class','form-control');
        $period->addMultiOption("","- Selecione un Año -");

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

        $this->addElements(array($anio,$period,$esc));  
	}
}
