<?php
class Profile_Form_Family extends Zend_Form{    

    public function init(){
    	
    	$type = new Zend_Form_Element_Select('type');
        $type   ->removeDecorator('Label')
                ->addMultiOption("","Elija Parentesco")
                ->setAttrib("class","form-control")
                ->setRequired(true)
                ->addMultiOptions(array('PA' => 'Padre',
                                        'MA' => 'Madre',
                                        'HE' => 'Hermana/o',
                                        'HI' => 'Hija/o'));

        $lastname= new Zend_Form_Element_Text('lastname');
        $lastname   ->removeDecorator('Label')
                    ->setRequired(true)
                    ->setAttrib("maxlength", "100")
                    ->setAttrib("title","Apellidos")
                    ->setAttrib("class","form-control");

        $firtsname= new Zend_Form_Element_Text('firtsname');
        $firtsname  ->removeDecorator('Label')
                    ->setRequired(true)
                    ->setAttrib("maxlength", "100")
                    ->setAttrib("title","Nombres")
                    ->setAttrib("class","form-control");

        $sex = new Zend_Form_Element_Select('sex');
        $sex    ->removeDecorator('Label')     
                ->addMultiOption("M","Masculino")
                ->addMultiOption("F","Femenino")
                ->setAttrib("class","form-control");

        $live = new Zend_Form_Element_Select('live');
        $live   ->removeDecorator('Label')     
                ->addMultiOption("", "Vive?")
                ->addMultiOption("S","Si")
                ->addMultiOption("N","No")
                ->setAttrib("class","form-control");

        $typedoc = new Zend_Form_Element_Select('typedoc');
        $typedoc->removeDecorator('Label')
                ->setRequired(true)     
                ->addMultiOption("D","DNI")
                ->addMultiOption("P","Pasaporte")
                ->setAttrib("class","form-control");

        $numdoc = new Zend_Form_Element_Text('numdoc');
        $numdoc ->removeDecorator("Label")
                ->addValidator('digits')
                ->addvalidator('stringLength', false, array(8))
                ->setRequired(true)
                ->setAttrib("maxlength", "8")
                ->setAttrib("title","Numero de Documento")
                ->setAttrib("class","form-control");

        $year = new Zend_Form_Element_Select("year");
        $year   ->removeDecorator('Label')
                ->setRequired(true)
                ->setAttrib("class","form-control")
                ->setAttrib("title","Año")
                ->addMultiOption("","Año");
        $anio = date('Y');
        for ($i = $anio; $i >= 1940 ; $i--) { 
            $year->addMultiOption($i, $i);
        }
        

        $month= new Zend_Form_Element_Select("month");
        $month  ->removeDecorator('Label')
                ->setRequired(true)
                ->setAttrib("class","form-control")
                ->setAttrib("title","Mes")
                ->addMultiOptions(array(''   => 'Mes',
                                        '01' => 'Enero',
                                        '02' => 'Febrero',
                                        '03' => 'Marzo',
                                        '04' => 'Abril',
                                        '05' => 'Mayo',
                                        '06' => 'Junio',
                                        '07' => 'Julio',
                                        '08' => 'Agosto',
                                        '09' => 'Septiembre',
                                        '10' => 'Octubre',
                                        '11' => 'Noviembre',
                                        '12' => 'Diciembre' ));
                

        $day= new Zend_Form_Element_Select("day");
        $day->removeDecorator('Label')
            ->setRequired(true)
            ->setAttrib("title","Dia")
            ->setAttrib("class", "form-control")
            ->addMultiOption("", "Día");
        for ($i=1; $i <= 31 ; $i++) { 
            $day->addMultiOption($i, $i);
        }
            /*->addMultiOptions(array(''   => 'Mes',
                                    '01' => 'Enero' ));*/

        $ocupacy= new Zend_Form_Element_Text('ocupacy');
        $ocupacy->removeDecorator('Label')
                ->setRequired(true)
                ->setAttrib("maxlength", "30")
                ->setAttrib("title","Ocupacion")
                ->setAttrib("class","form-control liveOptions");

        $health = new Zend_Form_Element_Select('health');
        $health ->removeDecorator('Label')     
                ->setAttrib("class","form-control liveOptions")
                ->addMultiOptions(array('N' => 'No',
                                        'S' => 'Si') );

        $phone= new Zend_Form_Element_Text('phone');
        $phone  ->removeDecorator("Label")
                ->addValidator('digits')
                ->setRequired(true)
                ->setAttrib("maxlength", "9")
                ->setAttrib("title","phone")
                ->setAttrib("class","form-control liveOptions");

        $address= new Zend_Form_Element_Text('address');
        $address->removeDecorator("Label")
                ->setRequired(true)
                ->setAttrib("maxlength", "250")
                ->setAttrib("title","Direccion")
                ->setAttrib("class","form-control liveOptions");

        $assignee = new Zend_Form_Element_Select('assignee');
        $assignee   ->removeDecorator('Label')
                    ->setRequired(true)
                    ->setAttrib("class","form-control liveOptions")
                    ->addMultiOptions(array('N' => 'No',
                                            'S' => 'Si') );


        $this->addElements(array( $type,  $lastname,  $firtsname, $sex, 
                                    $live,  $typedoc,   $numdoc,    $year, 
                                    $month, $day,       $ocupacy,   $health, 
                                    $phone, $address,   $assignee ));
    }
}