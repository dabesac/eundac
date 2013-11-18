<?php
class Bienestar_Form_Search extends Zend_Form{
	public function init(){

	$this->setName('frmsearch');

        $uid= new Zend_Form_Element_Text('uid');
        $uid->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $uid->setAttrib('maxlength','15')->setAttrib('size','15');
        $uid->setAttrib('class','form-control');
        $uid->setAttrib('onkeypress','return validNumber(event)');

        $search= new Zend_Form_Element_Submit('Buscar');
        $search->removeDecorator('Label')->removeDecorator('DtDdWrapper');
        $search->removeDecorator('Label')->removeDecorator("HtmlTag");
        $search->setAttrib('class','btn btn-success');

        $this->addElements(array($uid,$search));
	}
}