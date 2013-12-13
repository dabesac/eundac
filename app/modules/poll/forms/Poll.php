<?php

class Poll_Form_Poll extends Zend_Form
{    
    public function init()
    {
        $this->setName("frmPoll");
        $sesion  = Zend_Auth::getInstance();
        $login = $sesion->getStorage()->read();


        $title= new Zend_Form_Element_Text("title");
        $title->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $title->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $title->setAttrib("maxlength", "250");
        $title->setAttrib("class", "form-control");
        $title->setAttrib("title", "Título de la Encuesta");
        $this->addElement($title);


        $objective = new Zend_Form_Element_Text('objective');
        $objective->removeDecorator('Label')->removeDecorator("HtmlTag");
        $objective->setAttrib("class","form-control"); 
        $objective->setAttrib("title", "Objetivos");
        $this->addElement($objective);


        $comments= new Zend_Form_Element_Textarea("comments");
        $comments->removeDecorator('Label')->removeDecorator("HtmlTag");
        $comments->setAttrib('rows', '5');
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
        $state->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $state->setAttrib("class","form-control");
        $state->addMultiOption("A","Activo");
        $state->addMultiOption("C","Cerrado");
        $this->addElement($state);


        $perid = new Zend_Form_Element_Select('perid');
        $perid->removeDecorator('Label')->removeDecorator("HtmlTag");
        $perid->setRequired(true)->addErrorMessage('Este campo es requerido');
        $perid->setAttrib("class","form-control");
        $perid->addMultiOption("","- Seleccione Periodo -");
        $where = array(
            'eid' => $login->eid, 'oid' => $login->oid, 
            'p1' => substr(date('Y'), 2, 3).'A', 'p2' => substr(date('Y'), 2, 3).'B');
        $per = new Api_Model_DbTable_Periods();
        $data_per = $per->_getPeriodsXAyB($where);
        foreach ($data_per as $data_per) {
            $perid->addMultiOption($data_per['perid'], $data_per['perid'].' - '.$data_per['name']);
        }
        $this->addElement($perid);

        
        $is_all= new Zend_Form_Element_Checkbox('is_all');
        $is_all->removeDecorator('Label')->removeDecorator("HtmlTag");
        $is_all->setRequired(true)->addErrorMessage('Este campo es requerido');
        $is_all->setAttrib("class","form-control");
        $this->addElement($is_all);
    }
}