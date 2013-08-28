<?php

class Rcentral_Form_Course extends Zend_Form
{    
    public function init()
    {
        $this->setName("frmcourse");

        $courseid= new Zend_Form_Element_Text("courseid");
        $courseid->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $courseid->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $courseid->setAttrib("maxlength", "10")->setAttrib("style","text-align:center");;
        $courseid->setAttrib("class","input-mini");
        $courseid->setAttrib("title","Codigo Curso");
        $this->addElement($courseid);


        $semid = new Zend_Form_Element_Select('semid');
        $semid->removeDecorator('Label')->removeDecorator("HtmlTag");
        $semid->setAttrib("class","input-medium"); 
        $semid->addMultiOption("","- Semestre-");
        $bdsemestre = new Api_Model_DbTable_Semester();
            $where=array('eid'=>'20154605046','oid'=>'1');
            $order='semid asc';
        $semestres= $bdsemestre->_getAll($where,$order);
        foreach ($semestres as $semestre)
        {
            $semid->addMultiOption($semestre['semid'],$semestre['nombre_semestre']);
        }
        $this->addElement($semid);


        $name= new Zend_Form_Element_Text("name");
        $name->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $name->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $name->setAttrib("maxlength", "100");
        $name->setAttrib("class","input-xlarge");
        $name->setAttrib("title","Nombre del Curso");
        $this->addElement($name);


        $req_1 = new Zend_Form_Element_Select('req_1');
        $req_1->removeDecorator('Label')->removeDecorator("HtmlTag"); 
        $req_1->setAttrib("class","input-xlarge");   
        $req_1->addMultiOption("","- Seleccione 1er Prerequisito-");
        $this->addElement($req_1);


        $req_2= new Zend_Form_Element_Select("req_2");
        $req_2->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $req_2->setAttrib("class","input-xlarge");   
        $req_2->addMultiOption("","- Selecione 2do Prerequisito -");
        $this->addElement($req_2);

        $req_3= new Zend_Form_Element_Select("req_3");
        $req_3->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $req_3->setAttrib("class","input-xlarge");   
        $req_3->addMultiOption("","- Selecione 3er Prerequisito -");
        $this->addElement($req_3);

        $credits = new Zend_Form_Element_Text("credits");
        $credits->removeDecorator('Label')->removeDecorator("HtmlTag");
        $credits->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $credits->setAttrib("maxlength", "2")->setAttrib("style","text-align:center");;
        $credits->setAttrib("class","input-mini");
        $credits->setAttrib("title","Nro de Creditos");
        $this->addElement($credits);

        $abbreviation = new Zend_Form_Element_Text("abbreviation");
        $abbreviation->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $abbreviation->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $abbreviation->setAttrib("maxlength", "20");
		$abbreviation->setAttrib("class","input-medium");
        $abbreviation->setAttrib("title","Abreviatura del Curso");
        $this->addElement($abbreviation);
        

        $type = new Zend_Form_Element_Select('type');
        $type->removeDecorator('Label')->removeDecorator("HtmlTag");
        $type->setAttrib("class","input-medium");  
        $type->addMultiOption("","- Tipo de Curso -");
        $type->addMultiOption("O","Obligatorio");
        $type->addMultiOption("E","Electivo");
        $this->addElement($type);
        
        $state = new Zend_Form_Element_Select('state');
        $state->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $state->setAttrib("class","input-small");
        $state->addMultiOption("","- Estado -");
        $state->addMultiOption("A","Activo");
        $state->addMultiOption("I","Inactivo");
        $this->addElement($state);

        $hours_theoretical = new Zend_Form_Element_Text("hours_theoretical");
        $hours_theoretical->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $hours_theoretical->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $hours_theoretical->setAttrib("maxlength", "2")->setAttrib("style","text-align:center");;
        $hours_theoretical->setAttrib("class","input-mini");
        $hours_theoretical->setAttrib("title","Nro de Horas Teoricas");        
        $this->addElement($hours_theoretical);

        $hours_practical = new Zend_Form_Element_Text("hours_practical");
        $hours_practical->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $hours_practical->setRequired(true)->addErrorMessage('Este campo es requerido');      
        $hours_practical->setAttrib("maxlength", "2")->setAttrib("style","text-align:center");;
        $hours_practical->setAttrib("class","input-mini");
        $hours_practical->setAttrib("title","Nro de Horas Practicas");
        $this->addElement($hours_practical); 
        
        $year_course = new Zend_Form_Element_Select('year_course');
        $year_course->removeDecorator('Label')->removeDecorator("HtmlTag"); 
        $year_course->setAttrib("class","input-mini")->setAttrib("style","text-align:center");; 
        $year_course->addMultiOption("","- Año de Estudio-");
        for ($i=1;$i<=6;$i++)
        {
            $year_course->addMultiOption($i,$i);
        }
        $this->addElement($year_course);

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