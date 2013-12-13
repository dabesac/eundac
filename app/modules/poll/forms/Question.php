<?php

class Poll_Form_Question extends Zend_Form
{    
    public function init()
    {
        $this->setName("frmQuestion");


        $question= new Zend_Form_Element_Textarea("question");
        $question->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $question->setRequired(true)->addErrorMessage('Este campo es requerido');
        $question->setAttrib("class", "form-control");
        $question->setAttrib('rows', '3');
        $question->setAttrib("title", "Pregunta");
        $this->addElement($question);

        
        $type = new Zend_Form_Element_Select('type');
        $type->removeDecorator('Label')->removeDecorator("HtmlTag");
        $type->setRequired(true)->addErrorMessage('Este campo es requerido');
        $type->setAttrib("class","form-control");
        $type->addMultiOption("","- Seleccione Tipo -");
        $type->addMultiOption('OPT', 'Opcion Simple');
        $type->addMultiOption('MOPT', 'Opcion Multiple');
        $this->addElement($type);


        $state = new Zend_Form_Element_Select('state');
        $state->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $state->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $state->setAttrib("class","form-control");
        $state->addMultiOption("A","Activo");
        $state->addMultiOption("I","Inactivo");
        $this->addElement($state);


        $alternative1= new Zend_Form_Element_Text("alternative1");
        $alternative1->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $alternative1->setAttrib("class","form-control");
        $alternative1->setAttrib("maxlength", "250");
        $this->addElement($alternative1);

        
        $alternative2= new Zend_Form_Element_Text("alternative2");
        $alternative2->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $alternative2->setAttrib("class","form-control");
        $alternative2->setAttrib("maxlength", "250");
        $this->addElement($alternative2);

        
        $alternative3= new Zend_Form_Element_Text("alternative3");
        $alternative3->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $alternative3->setAttrib("class","form-control");
        $alternative3->setAttrib("maxlength", "250");
        $this->addElement($alternative3);

        
        $alternative4= new Zend_Form_Element_Text("alternative4");
        $alternative4->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $alternative4->setAttrib("class","form-control");
        $alternative4->setAttrib("maxlength", "250");
        $this->addElement($alternative4);

        
        $alternative5= new Zend_Form_Element_Text("alternative5");
        $alternative5->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $alternative5->setAttrib("class","form-control");
        $alternative5->setAttrib("maxlength", "250");
        $this->addElement($alternative5);
    }
}