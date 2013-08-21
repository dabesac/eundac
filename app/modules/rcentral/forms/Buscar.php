<?php

class Rcentral_Form_Buscar extends Zend_Form{
    public function init(){
        $sesion  = Zend_Auth::getInstance();
        $login = $sesion->getStorage()->read();
        // print_r($login);

        $this->setName('frmbuscar');

        $uid= new Zend_Form_Element_Text('uid');
        $uid->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $uid->setAttrib('maxlength','10')->setAttrib('size','10');
        $uid->setAttrib('class','form-control');
        $uid->setAttrib('onkeypress','return validNumber(event)');
        $uid->setRequired(true)->addErrorMessage('Este campo es Obligatorio');

        // $pid= new Zend_Form_element_Text('pid');
        // $pid->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        // $pid->setAttrib('maxlength','10')->setAttrib('size','10');
        // $pid->setAttrib('class','form-control');
        // $pid->setRequired(true)->addErrorMessage('Este campo es requerido');

        $last_name0= new Zend_Form_Element_Text('last_name0');
        $last_name0->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $last_name0->setAttrib('maxlength'.'40')->setAttrib('size','40');
        $last_name0->setAttrib('class','form-control');
        $last_name0->setRequired(true)->addErrorMessage('Este campo es Obligatorio');

        // $last_name1= new Zend_Form_element_Text('am');
        // $last_name1->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        // $last_name1->setAttrib('maxlength','40')->setAttrib('size','40');
        // $last_name1->setAttrib('class'.'form-control');
        // $last_name1->setRequired(true)->addErrorMessage('Este campo es requerido');

        // $first_name= new Zend_Form_element_Text('name');
        // $first_name->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        // $first_name->setAttrib('maxlength','40')->setAttrib('size','40');
        // $first_name->setAttrib('class','form-control');
        // $first_name->setRequired(true)->addErrorMessage('Este campo es requerido');

        // $submit = new Zend_Form_Element_Submit('enviar');
        // $submit->setAttrib('class', 'btn btn-primary');
        // $submit->setLabel('Guardar');
        // $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($uid,$last_name0));
    }
}