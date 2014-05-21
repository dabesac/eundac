<?php
class Acreditacion_Form_Search extends Zend_Form{
    public function init(){
    
        $this->setName('frmsearch');

        $uid= new Zend_Form_Element_Text('uid');
        $uid->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $uid->setAttrib('onkeypress','return validNumber(event)');
        $uid->setAttrib('maxlength','10')->setAttrib('size','10');
        $uid->setAttrib('class','form-control');

        $this->addElements(array($uid));
    }
}