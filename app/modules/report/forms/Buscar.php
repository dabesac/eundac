<?php

class Report_Form_Buscar extends Zend_Form{
    public function init(){
        $sesion  = Zend_Auth::getInstance();
        $login = $sesion->getStorage()->read();
        
        $this->setName('frmbuscar');

        $uid= new Zend_Form_Element_Text('uid');
        $uid->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $uid->setAttrib('maxlength','10')->setAttrib('size','10');
        $uid->setAttrib('class','form-control');
        $uid->setAttrib('onkeypress','return validNumber(event)');
        $uid->setRequired(true)->addErrorMessage('Este campo es Obligatorio');

        $last_name0= new Zend_Form_Element_Text('last_name0');
        $last_name0->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $last_name0->setAttrib('onkeypress','return soloLetras(event)');
        $last_name0->setAttrib('class','form-control');
        $last_name0->setRequired(true)->addErrorMessage('Este campo es Obligatorio');

        $this->addElements(array($uid,$last_name0));
    }
}