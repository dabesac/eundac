<?php

class Register_Form_Buscar extends Zend_Form{
    public function init(){
               
        $this->setName('frmbuscar');

        $uid= new Zend_Form_Element_Text('uid');
        $uid->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $uid->setAttrib('maxlength','10')->setAttrib('size','10');
        $uid->setAttrib('class','form-control');
        $uid->setAttrib('onkeypress','return validNumber(event)');
        $uid->setRequired(true)->addErrorMessage('Este campo es Obligatorio');

        $this->addElements(array($uid));
    }
}