<?php

class Rcentral_Form_Buscar extends Zend_Form{
    public function init(){
        $sesion  = Zend_Auth::getInstance();
        $login = $sesion->getStorage()->read();

        $this->setName('frmbuscar');

        $uid= new Zend_Form_element_Text('uid');
        $uid->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $uid->setAttrib('maxlength','10')->setAttrib('size','10');
        $uid->setAttrib('class','form-control');
        $uid->setRequiered(true)->addErrorMessage('Este campo es requerido');

        $pid= new Zend_Form_element_Text('pid');
        $pid->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $pid->setAttrib('maxlength','10')->setAttrib('size','10');
        $pid->setAttrib('class','form-control');
        $pid->setRequiered(true)->addErrorMessage('Este campo es requerido');

        $last_name0= new Zend_Form_element_Text('ap');
        $last_name0->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $last_name0->setAttrib('maxlength'.'40')->setAttrib('size','40');
        $last_name0->setAttrib('class','form-control');
        $last_name0->setRequiered(true)->addErrorMessage('Este campo es requerido');

        $last_name1= new Zend_Form_element_Text('am');
        $last_name1->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $last_name1->setAttrib('maxlength','40')->setAttrib('size','40');
        $last_name1->setAttrib('class'.'form-control');
        $last_name1->setRequiered(true)->addErrorMessage('Este campo es requerido');

        $first_name= new Zend_Form_element_Text('name');
        $first_name->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $first_name->setAttrib('maxlength','40')->setAttrib('size','40');
        $first_name->setAttrib('class','form-control');
        $first_name->setRequiered(true)->addErrorMessage('Este campo es requerido');

        $facid= new Zend_Form_element_Select('facid');
        $facid->removeDecorator('Label');
        $facid->removeDecorator('HtmlTag');
    }
}