<?php
class Admin_Form_Buscar extends Zend_Form{
    public function init(){
        $sesion  = Zend_Auth::getInstance();
        $login = $sesion->getStorage()->read();
        
        $this->setName('frmbuscar');

        $pid= new Zend_Form_Element_Text('pid');
        $pid->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $pid->setAttrib('maxlength','8')->setAttrib('size','8');
        $pid->setAttrib('class','form-control');
        $pid->setAttrib('onkeypress','return validNumber(event)');
        $pid->setRequired(true)->addErrorMessage('Este campo es Obligatorio');

        $name= new Zend_Form_Element_Text('name');
        $name->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $name->setAttrib('maxlength'.'40')->setAttrib('size','40');
        $name->setAttrib('class','form-control');

        $uid= new Zend_Form_Element_Text('uid');
        $uid->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $uid->setAttrib('maxlength'.'40')->setAttrib('size','40');
        $uid->setAttrib('class','form-control');
        // $name->setRequired(true)->addErrorMessage('Este campo es Obligatorio');

        $this->addElements(array($pid,$name,$uid));
    }
}