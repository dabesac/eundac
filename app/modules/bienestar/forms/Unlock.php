<?php
class Bienestar_Form_Unlock extends Zend_Form{
	public function init(){

	$this->setName('frmlockandunlock');

        $office_unlock= new Zend_Form_Element_Text('office_unlock');
        $office_unlock->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $office_unlock->setAttrib('class','form-control');
        $office_unlock->setAttrib("placeholder","Ingrese texto");
        $office_unlock->setRequired(true)->addErrorMessage('Este campo es Obligatorio');

        $reason_unlock= new Zend_Form_Element_Text('reason_unlock');
        $reason_unlock->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $reason_unlock->setAttrib('class','form-control');
        $reason_unlock->setAttrib("placeholder","Ingrese texto");
        $reason_unlock->setRequired(true)->addErrorMessage('Este campo es Obligatorio');

        $type_doc_unlock= new Zend_Form_Element_Text('type_doc_unlock');
        $type_doc_unlock->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $type_doc_unlock->setAttrib('class','form-control');
        $type_doc_unlock->setAttrib("placeholder","Ingrese texto");
        $type_doc_unlock->setRequired(true)->addErrorMessage('Este campo es Obligatorio');

        $date_unlock= new Zend_Form_Element_Text('date_unlock');
        $date_unlock->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $date_unlock->setAttrib('class','form-control');
        $date_unlock->setAttrib("placeholder","aa-mm-dd");
        $date_unlock->setRequired(true)->addErrorMessage('Este campo es Obligatorio');        

        $detail_unlock= new Zend_Form_Element_Textarea("detail_unlock");
        $detail_unlock->setAttrib('rows', '4');
        $detail_unlock->setAttrib("class","form-control");
        $detail_unlock->setAttrib("placeholder","Ingrese texto");
        $detail_unlock->removeDecorator("HtmlTag")->removeDecorator("Label");

        $send= new Zend_Form_Element_Submit('Guardar');
        $send->removeDecorator('Label')->removeDecorator('DtDdWrapper');
        $send->removeDecorator('Label')->removeDecorator("HtmlTag");
        $send->setAttrib('class','btn btn-success');

        $this->addElements(array($office_lock,$reason_lock,$type_doc_lock,$date_lock,$detail_lock,$office_unlock,$reason_unlock,$type_doc_unlock,$date_unlock,$detail_unlock,$send,$update));
	}
}