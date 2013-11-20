<?php

class Poll_Form_Poll extends Zend_Form
{    
    public function init()
    {
        $this->setName("frmpoll");

        $title= new Zend_Form_Element_Text("title");
        $title->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $title->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $title->setAttrib("maxlength", "10");
        $title->setAttrib("class", "form-control");
        $title->setAttrib("title", "Título de la Encuesta");
        $this->addElement($title);

        $objective = new Zend_Form_Element_Text('objective');
        $objective->removeDecorator('Label')->removeDecorator("HtmlTag");
        $objective->setAttrib("class","form-control"); 
        $objective->setAttrib("title", "Objetivos");
        $this->addElement($objective);

        $comments= new Zend_Form_Element_Text("comments");
        $comments->removeDecorator('Label')->removeDecorator("HtmlTag");
        $comments->setAttrib("class","form-control");
        $comments->setAttrib("title","Comentarios");
        $this->addElement($comments);

        $published = new Zend_Form_Element_Text("published");
        $published->removeDecorator('Label')->removeDecorator("HtmlTag");
        $published->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $published->setAttrib("maxlength", "20");
        $published->setAttrib("class","form-control");
        $published->setAttrib("title","Publicación");
        $this->addElement($published);

        $closed = new Zend_Form_Element_Text("closed");
        $closed->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $closed->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $closed->setAttrib("maxlength", "20");
        $closed->setAttrib("class","form-control");
        $closed->setAttrib("title","Cierre");
        $this->addElement($closed);
        
        $state = new Zend_Form_Element_Select('state');
        $state->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $closed->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $state->setAttrib("class","form-control");
        $state->addMultiOption("A","Activo");
        $state->addMultiOption("C","Cerrado");
        $this->addElement($state);

        $perid = new Zend_Form_Element_Select('perid');
        $perid->removeDecorator('Label')->removeDecorator("HtmlTag");
        $perid->setAttrib("class","form-control");
        $perid->addMultiOption("","- Selecione Periodo -");
        for ($i=1;$i<=6;$i++)
        {
            $perid->addMultiOption($i,$i);
        }
        $this->addElement($perid);

        $course_equivalence= new Zend_Form_Element_Select("course_equivalence");
        $course_equivalence->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $course_equivalence->setAttrib("class","input-xlarge");   
        $course_equivalence->addMultiOption("","- Selecione Curso Equivalente -");
        $this->addElement($course_equivalence);

        $course_equivalence_2= new Zend_Form_Element_Select("course_equivalence_2");
        $course_equivalence_2->removeDecorator('Label')->removeDecorator("HtmlTag"); 
        $course_equivalence_2->setAttrib("class","input-xlarge");    
        $course_equivalence_2->addMultiOption("","- Selecione Curso Convalidacion -");
        $this->addElement($course_equivalence_2);

    }
}