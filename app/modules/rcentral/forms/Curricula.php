<?php

class Rcentral_Form_Curricula extends Zend_Form{    
    public function init(){
        $type_periods = new Zend_Form_Element_Select('type_periods');
        $type_periods   ->removeDecorator('Label')->removeDecorator("HtmlTag")
                        ->setRequired(true)
                        ->addValidator('NotEmpty', true, array('messages' => 'Elija un periódo...'))
                        ->setAttribs(array('class' => 'form-control'))
                        ->addMultiOptions(array(''  => 'Periódo',
                                                'A' => 'A',
                                                'B' => 'B' ));


        $year = new Zend_Form_Element_Select('year');
        $year   ->removeDecorator('Label')->removeDecorator("HtmlTag")
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Elija un año...'))
                ->setAttrib("class","form-control");

        $anioactual = (int)Date("Y");
        $year->addMultiOption("","Año");
        for ($i=$anioactual;$i>=1990;$i--){
                $year->addMultiOption($i,$i);
        }


        $type = new Zend_Form_Element_Select('type');
        $type   ->removeDecorator('Label')->removeDecorator("HtmlTag")
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Elija un tipo de currícula...'))
                ->setAttribs(array(
                                    'class' => 'form-control' ))
                ->addMultiOptions(array(
                                        ''  => 'Tipo de Curricula',
                                        'S' => 'Semestral',
                                        'A' => 'Anual' ));


        $name = new Zend_Form_Element_Text("name");
        $name   ->removeDecorator('Label')->removeDecorator("HtmlTag")->addDecorators(array('Errors'))
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'El nombre esta vacío...'))
                ->setAttribs(array(
                                    'class'       => 'form-control',
                                    'maxlength'   => '100',
                                    'placeholder' => 'Nombre del plan curricular...',
                                    'title'       => 'Nombre del plan Curricular' ));
        // ->addValidator('StringLength', true, array(0, 20, 'messages' => 'Campo obligatorio'))
        //$name->getValidator('Digits')->setMessage('Debe ser solo numeros');

        $alias = new Zend_Form_Element_Text("alias");
        $alias  ->removeDecorator('Label')->removeDecorator("HtmlTag")
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'El alias esta vacío...'))
                ->setAttribs(array(
                                    'class'       => 'form-control',
                                    'maxlength'   => '100',
                                    'placeholder' => 'Aliás del plan curricular...',
                                    'title'       => 'Aliás del plan Curricular' ));


        $number_periods = new Zend_Form_Element_Text("number_periods");
        $number_periods ->removeDecorator('Label')->removeDecorator("HtmlTag")
                        ->setRequired(true)
                        ->addValidator('NotEmpty', true, array('messages' => 'La cantidad de periódos esta vacío...'))
                        ->addValidator('Digits', true, array('messages' => 'La cantidad de periodos debe ser solo números ¬¬'))
                        ->setAttribs(array(
                                    'class'       => 'form-control',
                                    'maxlength'   => '2',
                                    'placeholder' => 'Cantidad de periódos...',
                                    'title'       => 'Cantidad de periódos' ));       
                

        $mandatory_credits = new Zend_Form_Element_Text("mandatory_credits");
        $mandatory_credits  ->removeDecorator('Label')->removeDecorator("HtmlTag")
                            ->setRequired(true)
                            ->addValidator('NotEmpty', true, array('messages' => 'La cantidad de créditos obligatorios esta vacío...'))
                            ->addValidator('Digits', true, array('messages' => 'Los creditos obligatorios debe ser solo números ¬¬'))
                            ->setAttribs(array(
                                    'class'       => 'form-control',
                                    'maxlength'   => '3',
                                    'placeholder' => 'Créditos obligatorios...',
                                    'title'       => 'Créditos obligatorios' ));


        $elective_credits = new Zend_Form_Element_Text("elective_credits");
        $elective_credits   ->removeDecorator('Label')->removeDecorator("HtmlTag")
                            ->setRequired(true)
                            ->addValidator('NotEmpty', true, array('messages' => 'La cantidad de créditos electivos esta vacío...'))
                            ->addValidator('Digits', true, array('messages' => 'La creditos electivos debe ser solo números ¬¬'))
                            ->setAttribs(array(
                                    'class'       => 'form-control',
                                    'maxlength'   => '3',
                                    'placeholder' => 'Créditos electivos...',
                                    'title'       => 'Créditos electivos' ));


        $mandatory_course = new Zend_Form_Element_Text("mandatory_course");
        $mandatory_course   ->removeDecorator('Label')->removeDecorator("HtmlTag")
                            ->setRequired(true)
                            ->addValidator('NotEmpty', true, array('messages' => 'La cantidad de cursos obligatorios esta vacío...'))
                            ->addValidator('Digits', true, array('messages' => 'La cantidad de cursos obligatorios debe ser solo números ¬¬'))
                            ->setAttribs(array(
                                    'class'       => 'form-control',
                                    'maxlength'   => '3',
                                    'placeholder' => 'Cursos obligatorios...',
                                    'title'       => 'Cursos obligatorios' ));


        $elective_course = new Zend_Form_Element_Text("elective_course");
        $elective_course->removeDecorator('Label')->removeDecorator("HtmlTag")
                        ->setRequired(true)
                        ->addValidator('NotEmpty', true, array('messages' => 'La cantidad de cursos electivos esta vacío...'))
                        ->addValidator('Digits', true, array('messages' => 'La cantidad de cursos electivos debe ser solo números ¬¬'))
                        ->setAttribs(array(
                                    'class'       => 'form-control',
                                    'maxlength'   => '3',
                                    'placeholder' => 'Cursos electivos...',
                                    'title'       => 'Cursos electivos' ));

      
        $cur_per_ant = new Zend_Form_Element_Select("cur_per_ant");
        $cur_per_ant->removeDecorator('Label')->removeDecorator("HtmlTag")
                    ->setRegisterInArrayValidator(false)
                    ->setAttribs(array(
                                    'class'       => 'form-control',
                                    'title'       => 'Cursos electivos' ));

        $save = new Zend_Form_Element_Submit('save');
        $save   ->setAttribs(array(
                                    'class' => 'btn btn-success form-control'))
                ->setLabel('Guardar');


        $this->addElements(array(   $type_periods, $year, $type, 
                                    $name, $alias, $number_periods, 
                                    $mandatory_credits, $elective_credits, $mandatory_course, 
                                    $elective_course, $cur_per_ant, $save ));  
    }
}