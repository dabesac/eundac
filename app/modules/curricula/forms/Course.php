<?php

class Curricula_Form_Course extends Zend_Form
{    
    public function init()
    {
        $courseid = new Zend_Form_Element_Text("courseid");
        $courseid   ->removeDecorator('Label')->removeDecorator("HtmlTag")
                    ->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Debe ingresar el código'))
                    ->setAttribs(array( 
                                        'class'       => 'form-control',
                                        'title'       => 'Codigo de curso...',
                                        'placeholder' => 'Código',
                                        'maxlength'   => '10'));
        $this->addElement($courseid);

       
        $name = new Zend_Form_Element_Text("name");
        $name   ->removeDecorator('Label')->removeDecorator("HtmlTag")
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Debe ingresar el nombre'))
                ->setAttribs(array( 
                                    'class'       => 'form-control',
                                    'maxlength'   => '150',
                                    'title'       => 'Nombre del curso...',
                                    'placeholder' => 'Nombre'));
        $this->addElement($name);


        $abbreviation = new Zend_Form_Element_Text("abbreviation");
        $abbreviation ->removeDecorator('Label')->removeDecorator("HtmlTag")
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Debe ingresar el nombre abreviado'))
                ->setAttribs(array(  
                                    'class'       => 'form-control',
                                    'maxlength'   => '30',
                                    'title'       => 'Nombre abreviado del curso...',
                                    'placeholder' => 'Nombre abreviado' ));
        $this->addElement($abbreviation);


        $credits = new Zend_Form_Element_Text("credits");
        $credits->removeDecorator('Label')->removeDecorator("HtmlTag")
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Debe ingresar los creditos..'))
                ->addValidator('Digits', true, array('messages' => 'La cantidad de créditos debe ser solo números ¬¬'))
                ->setAttribs(array( 
                                    'class'       => 'form-control',
                                    'maxlength'   => '2',
                                    'title'       => 'Créditos del curso...',
                                    'placeholder' => 'Creditos' ));
        $this->addElement($credits);


        $type = new Zend_Form_Element_Select('type');
        $type   ->removeDecorator('Label')->removeDecorator("HtmlTag")
                ->setAttribs(array(
                                    'class' => 'form-control',
                                    'title' => 'Tipo de curso'))
                ->addMultiOptions(array(
                                        'O' => 'Obligatorio',
                                        'E' => 'Electivo' ));
        $this->addElement($type);


        $hours_theoretical = new Zend_Form_Element_Text("hours_theoretical");
        $hours_theoretical  ->removeDecorator('Label')->removeDecorator("HtmlTag")
                            ->setRequired(true)
                            ->addValidator('NotEmpty', true, array('messages' => 'Debe ingresar las horas teóricas..'))
                            ->addValidator('Digits', true, array('messages' => 'Las horas teóricas debe ser solo números ¬¬'))
                            ->setAttribs(array( 
                                                'class'       => 'form-control',
                                                'maxlength'   => '2',
                                                'title'       => 'Horas teóricas...',
                                                'placeholder' => 'Horas teóricas' ));
        $this->addElement($hours_theoretical);


        $hours_practical = new Zend_Form_Element_Text("hours_practical");
        $hours_practical  ->removeDecorator('Label')->removeDecorator("HtmlTag")
                            ->setRequired(true)
                            ->addValidator('NotEmpty', true, array('messages' => 'Debe ingresar las horas practicas..'))
                            ->addValidator('Digits', true, array('messages' => 'Las horas practicas debe ser solo números ¬¬'))
                            ->setAttribs(array( 
                                                'class'       => 'form-control',
                                                'maxlength'   => '2',
                                                'title'       => 'Horas practicas...',
                                                'placeholder' => 'Horas practicas' ));
        $this->addElement($hours_practical);


        $semid = new Zend_Form_Element_Select('semid');
        $semid  ->removeDecorator('Label')->removeDecorator("HtmlTag")
                ->setRegisterInArrayValidator(false)
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Debe ingresar un semestre...'))
                ->setAttribs(array(
                                    'class' => 'form-control',
                                    'title' => 'Semestre')); 
        $this->addElement($semid);


        $year_course = new Zend_Form_Element_Select('year_course');
        $year_course->removeDecorator('Label')->removeDecorator("HtmlTag")
                    ->setAttribs(array(
                                        'class' => 'form-control',
                                        'title' => 'Semestre'))
                    ->addMultiOptions(array(
                                            '1' => 'Primer Año',
                                            '2' => 'Segundo Año',
                                            '3' => 'Tercer Año',
                                            '4' => 'Cuarto Año',
                                            '5' => 'Quinto Año',
                                            '6' => 'Sexto Año' )); 
        $this->addElement($year_course);


        $state = new Zend_Form_Element_Select('state');
        $state  ->removeDecorator('Label')->removeDecorator("HtmlTag")
                ->setAttribs(array(
                                    'class' => 'form-control',
                                    'title' => 'Estado de curso'))
                ->addMultiOptions(array(
                                        'A' => 'Activo',
                                        'I' => 'Inactivo' ));
        $this->addElement($state);

        //Para los cursos de equivalencia me dare una fumada todavia :3
        /*$course_equivalence = new Zend_Form_Element_Select("course_equivalence");
        $course_equivalence->removeDecorator('Label')->removeDecorator("HtmlTag");  
        $course_equivalence->setAttrib("class","input-xlarge");   
        $course_equivalence->addMultiOption("","- Selecione Curso Equivalente -");
        $this->addElement($course_equivalence);

        $course_equivalence_2= new Zend_Form_Element_Select("course_equivalence_2");
        $course_equivalence_2->removeDecorator('Label')->removeDecorator("HtmlTag"); 
        $course_equivalence_2->setAttrib("class","input-xlarge");    
        $course_equivalence_2->addMultiOption("","- Selecione Curso Convalidacion -");
        $this->addElement($course_equivalence_2);*/

    }
}