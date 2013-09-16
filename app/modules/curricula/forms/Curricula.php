<?php

class Rcentral_Form_Curricula extends Zend_Form{    
    public function init(){

        $this->setName("frmcurricula");

        $curid= new Zend_Form_Element_Text("curid");
        $curid->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $curid->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $curid->setAttrib("maxlength", "10");
        $curid->setAttrib("class","form-control");
        $curid->setAttrib('readonly',true);

        $subid= new Zend_Form_Element_Hidden('subid');
        $subid->removeDecorator('Label')
                ->removeDecorator('HtmlTag')
                ->setRequired(true)
                ->setAttrib('class','form-control');
        $this->addElement($subid);

        $escid= new Zend_Form_Element_Hidden('escid_cur');
        $escid->removeDecorator('Label')
                ->removeDecorator('HtmlTag')
                ->setRequired(true)
                ->setAttrib('class','form-control');
        $this->addElement($escid);

        $type_periods = new Zend_Form_Element_Select('type_periods');
        $type_periods->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $type_periods->addMultiOption("","Periodo");
        $type_periods->setAttrib("class","form-control");
        $type_periods->addMultiOption("A","A");
        $type_periods->addMultiOption("B","B");

        $year = new Zend_Form_Element_Select('year');
        $year->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $year->setAttrib("class","form-control");
        $anioactual = (int)Date("Y");
        $year->addMultiOption("","Año");
        for ($i=$anioactual;$i>=1990;$i--){
                $year->addMultiOption((mb_substr((string)$i,2,2)),$i);
        }

        $type = new Zend_Form_Element_Select('type');
        $type->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $type->setRequired(true)->addErrorMessage('Este campo es requerido');
        $type->addMultiOption("","Tipo Curricula");
        $type->setAttrib("class","span3");
        $type->setAttrib("class","form-control");
        $type->addMultiOption("S","Semestral");
        $type->addMultiOption("A","Anual");

        $state = new Zend_Form_Element_Select('state');
        $state->removeDecorator('Label')->removeDecorator("HtmlTag");
        $state->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $state->setAttrib("class","span3"); 
        $state->setAttrib("class","form-control");
        $state->addMultiOption("","Estado Curricula");
        $state->addMultiOption("A","Activa");
        $state->addMultiOption("T","Temporal");
        $state->addMultiOption("C","Cerrada");
        $state->addMultiOption("B","Borrador");

        $cur_per_ant= new Zend_Form_Element_Text("cur_per_ant");
        $cur_per_ant->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $cur_per_ant->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $cur_per_ant->setAttrib("maxlength", "3");
        $cur_per_ant->setAttrib("class","form-control");
        $cur_per_ant->setAttrib('readonly',true);        

        $name= new Zend_Form_Element_Text("name");
        $name->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $name->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $name->setAttrib("maxlength", "100");
        $name->setAttrib("class","form-control");   
        $name->setAttrib("title","Nombre del Plan Curricular");

        $alias = new Zend_Form_Element_Text("alias");
        $alias->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $alias->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $alias->setAttrib("maxlength", "100");
        $alias->setAttrib("title","Alias del Plan Curricular");   
        $alias->setAttrib("class","form-control");     

        $number_periods = new Zend_Form_Element_Text("number_periods");
        $number_periods->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $number_periods->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $number_periods->setAttrib("maxlength", "2");
        $number_periods->setAttrib("class","form-control");
        $number_periods->setAttrib("title","Total Periodos");        
                
        $mandatory_credits = new Zend_Form_Element_Text("mandatory_credits");
        $mandatory_credits->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $mandatory_credits->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $mandatory_credits->setAttrib("maxlength", "3");
        $mandatory_credits->setAttrib("class","form-control");
        // $mandatory_credits->setAttrib("style","text-align:center");
        $mandatory_credits->setAttrib("title","Total Creditos O");        

        $elective_credits = new Zend_Form_Element_Text("elective_credits");
        $elective_credits->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $elective_credits->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $elective_credits->setAttrib("maxlength", "3");
        $elective_credits->setAttrib("class","form-control");
        // $elective_credits->setAttrib("style","text-align:center");
        $elective_credits->setAttrib("title","Total Creditos Electivos");        

        $mandatory_course = new Zend_Form_Element_Text("mandatory_course");
        $mandatory_course->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $mandatory_course->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $mandatory_course->setAttrib("maxlength", "3");
        $mandatory_course->setAttrib("class","form-control");
        // $mandatory_course->setAttrib("style","text-align:center");
        $mandatory_course->setAttrib("title","Nro Cursos O");        

        $elective_course = new Zend_Form_Element_Text("elective_course");
        $elective_course->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $elective_course->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $elective_course->setAttrib("maxlength", "3");
        $elective_course->setAttrib("class","form-control");
        // $elective_course->setAttrib("style","text-align:center");
        $elective_course->setAttrib("title","Nro Cursos E");     
      
        $this->addElements(array($curid,$year,$type,$state,$cur_per_ant,$name,$alias,$number_periods,$mandatory_credits,$elective_credits,$mandatory_course,$elective_course,$type_periods));  
    }
}