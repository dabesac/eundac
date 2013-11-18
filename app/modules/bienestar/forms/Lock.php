<?php
class Bienestar_Form_Lock extends Zend_Form{
	public function init(){

	$this->setName('frmlockandunlock');

        $office_lock= new Zend_Form_Element_Text('office_lock');
        $office_lock->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $office_lock->setAttrib('class','form-control');
        $office_lock->setAttrib("placeholder","Ingrese texto");
        $office_lock->setRequired(true)->addErrorMessage('Este campo es Obligatorio');

        $reason_lock= new Zend_Form_Element_Text('reason_lock');
        $reason_lock->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $reason_lock->setAttrib('class','form-control');
        $reason_lock->setAttrib("placeholder","Ingrese texto");
        $reason_lock->setRequired(true)->addErrorMessage('Este campo es Obligatorio');

        $type_doc_lock= new Zend_Form_Element_Text('type_doc_lock');
        $type_doc_lock->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $type_doc_lock->setAttrib('class','form-control');
        $type_doc_lock->setAttrib("placeholder","Ingrese texto");
        $type_doc_lock->setRequired(true)->addErrorMessage('Este campo es Obligatorio');

        $date_lock= new Zend_Form_Element_Text('date_lock');
        $date_lock->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $date_lock->setAttrib('class','form-control');
        $date_lock->setAttrib("placeholder","aa-mm-dd");
        $date_lock->setRequired(true)->addErrorMessage('Este campo es Obligatorio');

        $detail_lock= new Zend_Form_Element_Textarea("detail_lock");
        $detail_lock->setAttrib('rows', '4');
        $detail_lock->setAttrib("class","form-control");
        $detail_lock->setAttrib("placeholder","Ingrese texto");
        $detail_lock->removeDecorator("HtmlTag")->removeDecorator("Label");

        $send= new Zend_Form_Element_Submit('Guardar');
        $send->removeDecorator('Label')->removeDecorator('DtDdWrapper');
        $send->removeDecorator('Label')->removeDecorator("HtmlTag");
        $send->setAttrib('class','btn btn-success');

        $this->addElements(array($office_lock,$reason_lock,$type_doc_lock,$date_lock,$detail_lock,$send));
	}
}