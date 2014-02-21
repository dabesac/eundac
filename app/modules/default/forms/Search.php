<?php
class Default_Form_Search extends Zend_Form{    
    public function init(){
        $this->setName("frmsearch");
           
        $searching = new Zend_Form_Element_Text('searching');
        $searching->setRequired(true)->addErrorMessage('Este campo es requerido');
        $searching->removeDecorator('Label')->removeDecorator("HtmlTag");
        $searching->setAttrib("class","form-control");


        $submit = new Zend_Form_Element_Submit('Buscar');
        $submit->removeDecorator('HtmlTag'); 
        $submit->removeDecorator('Label')->removeDecorator('DtDdWrapper');
        $submit->setAttrib('class', 'btn btn-success');

    
        $this->addElements(array($searching,$submit)); 

    }
}
