<?php
Class Acreditacion_Form_Project extends Zend_Form
{
	public $elemnetDecorator =array(
        array('HTMLTag', array('tag' => 'fieldset'))
    );
    public $elemnetDecorator1 =array(
            array('HTMLTag', array('tag' => 'div', 'class' =>
                'form-group'))
    );
	public function init()
	{
	    $name = new Zend_Form_Element_Text('name');
    	$name  ->setRequired(true)
	           ->setLabel('Nombre')
        		->setRequired(true)
        		->addErrorMessage('Este campo es requerido')
               ->addDecorators($this->elemnetDecorator)
               // ->setAttrib('maxlength',8)
               ->setAttrib('class','form-control')
	           // ->setAttrib('onkeypress','return soloNumero(event)')
	           ->addValidator('NotEmpty',true,array('messages' => '*'));
	    $this->addElement($name);

	    $place = new Zend_Form_Element_Text('place');
    	$place  ->setRequired(true)
	           ->setLabel('Ejecución')
        		->setRequired(true)
        		->addErrorMessage('Este campo es requerido')
               ->addDecorators($this->elemnetDecorator)
               // ->setAttrib('maxlength',8)
               ->setAttrib('class','form-control')
	           // ->setAttrib('onkeypress','return soloNumero(event)')
	           ->addValidator('NotEmpty',true,array('messages' => '*'));
	    $this->addElement($place);

	    $f_fin = new Zend_Form_Element_Text('f_fin');
    	$f_fin  ->setRequired(true)
	           ->setLabel('Fecha Culminación')
        		->setRequired(true)
        		->addErrorMessage('Este campo es requerido')
               ->addDecorators($this->elemnetDecorator)
               // ->setAttrib('maxlength',8)
               ->setAttrib('class','form-control')
	           // ->setAttrib('onkeypress','return soloNumero(event)')
	           ->addValidator('NotEmpty',true,array('messages' => '*'));
	    $this->addElement($f_fin);

	    $f_presentation = new Zend_Form_Element_Text('f_presentation');
    	$f_presentation  ->setRequired(true)
	           ->setLabel('Fecha Presentación')
        		->setRequired(true)
        		->addErrorMessage('Este campo es requerido')
               ->addDecorators($this->elemnetDecorator)
               // ->setAttrib('maxlength',8)
               ->setAttrib('class','form-control')
	           // ->setAttrib('onkeypress','return soloNumero(event)')
	           ->addValidator('NotEmpty',true,array('messages' => '*'));
	    $this->addElement($f_presentation);


	    $f_ini = new Zend_Form_Element_Text('f_ini');
    	$f_ini  ->setRequired(true)
	           ->setLabel('Fecha Inicio')
        		->setRequired(true)
        		->addErrorMessage('Este campo es requerido')
               ->addDecorators($this->elemnetDecorator)
               // ->setAttrib('maxlength',8)
               ->setAttrib('class','form-control')
	           // ->setAttrib('onkeypress','return soloNumero(event)')
	           ->addValidator('NotEmpty',true,array('messages' => '*'));
	    $this->addElement($f_ini);



	    $modality = new Zend_Form_Element_Select('modality');
		$modality ->removeDecorator('DtDdWrapper')
					->setAttrib('class','form-control')
					->setRequired(true)
        		->addErrorMessage('Este campo es requerido')
		           ->removeDecorator('Label')
		           ->removeDecorator('HtmlTag')
		           ->addMultiOption('','Seleccione')
		           ->addMultiOption('M','Monovalente')
		           ->addMultiOption('P','Polivalente');
		$this->addElement($modality);

		$type = new Zend_Form_Element_Select('type');
		$type ->removeDecorator('DtDdWrapper')
					->setAttrib('class','form-control')
					->setRequired(true)
        		->addErrorMessage('Este campo es requerido')
		           ->removeDecorator('Label')
		           ->removeDecorator('HtmlTag')
		           ->addMultiOption('','Seleccione')
		           ->addMultiOption('P','Proyección Social')
		           ->addMultiOption('I','Investigación');
		$this->addElement($type);
		// $this->setDefaults(array('type'=>'P'));

		$state = new Zend_Form_Element_Select('state');
		$state ->removeDecorator('DtDdWrapper')
					->setAttrib('class','form-control')
		           ->removeDecorator('Label')
		           ->setRequired(true)
        		->addErrorMessage('Este campo es requerido')
		           ->removeDecorator('HtmlTag')
		           ->addMultiOption('','Seleccione')
		           ->addMultiOption('B','Borrador')
		           ->addMultiOption('E','Enviado');
		           // ->addMultiOption('A','Aprobado')
		           // ->addMultiOption('R','Rechazado')
		           // ->addMultiOption('C','Cerrado');
		$this->addElement($state);
		$this->setDefaults(array('state'=>'B'));


	    $min_student = new Zend_Form_Element_Text('min_student');
    	$min_student  ->setRequired(true)
	           ->setLabel('minino Alumnos')
               ->addDecorators($this->elemnetDecorator)
               ->setRequired(true)
        		->addErrorMessage('Este campo es requerido')
				->setAttrib('class','form-control')
               ->setAttrib('maxlength',8)
	           // ->setAttrib('onkeypress','return soloNumero(event)')
	           ->addValidator('NotEmpty',true,array('messages' => '*'));
	    $this->addElement($min_student);

	    $max_student = new Zend_Form_Element_Text('max_student');
    	$max_student  ->setRequired(true)
	           ->setLabel('maximo Alumnos')
	           ->setRequired(true)
        		->addErrorMessage('Este campo es requerido')
			   ->setAttrib('class','form-control')
               ->addDecorators($this->elemnetDecorator)
               ->setAttrib('maxlength',8)
	           // ->setAttrib('onkeypress','return soloNumero(event)')
	           ->addValidator('NotEmpty',true,array('messages' => '*'));
	    $this->addElement($max_student);
	    $this->setDefaults(array('max_student'=>'10','min_student'=>'0'));

	    $num_horas = new Zend_Form_Element_Text('num_horas');
    	$num_horas  ->setRequired(true)
	           ->setLabel('Numero de Horas')
	           ->setRequired(true)
        		->addErrorMessage('Este campo es requerido')
				->setAttrib('class','form-control')
               ->addDecorators($this->elemnetDecorator)
               ->setAttrib('maxlength',8)
	           // ->setAttrib('onkeypress','return soloNumero(event)')
	           ->addValidator('NotEmpty',true,array('messages' => '*'));
	    $this->addElement($num_horas);
	    $this->setDefaults(array('num_horas'=>'8'));

	    $comment = new Zend_Form_Element_Textarea('comment');
    	$comment  ->setRequired(true)
	           ->setLabel('Comentario')
	           ->setRequired(true)
        		->addErrorMessage('Este campo es requerido')
				->setAttrib('class','form-control')
				->setAttrib('rows','3')
               // ->addDecorators($this->elemnetDecorator)
               // ->setAttrib('maxlength',8)
	           // ->setAttrib('onkeypress','return soloNumero(event)')
	           ->addValidator('NotEmpty',true,array('messages' => '*'));
	    $this->addElement($comment);

	    $description = new Zend_Form_Element_Textarea('description');
    	$description  ->setRequired(true)
	           ->setLabel('Description')
	           ->setRequired(true)
        		->addErrorMessage('Este campo es requerido')
				->setAttrib('class','form-control')
				->setAttrib('rows','3')
               // ->addDecorators($this->elemnetDecorator)
               // ->setAttrib('maxlength',8)
	           // ->setAttrib('onkeypress','return soloNumero(event)')
	           ->addValidator('NotEmpty',true,array('messages' => '*'));
	    $this->addElement($description);

	    $project = new Zend_Form_Element_File('project');
        	$project 	
       			->removeDecorator('HtmlTag')
       			// ->setRequired(true)
         		->addErrorMessage('Adjuntar Proyecto')
        			///->setDestination(APPLICATION_PATH.'/upload')
        		->addValidator('Count', false, 1)
       			// ->addValidator('Size', false, 902400)
        			// ->addValidator('Extension', false, 'jpg,png,gif')
   		 		->removeDecorator('Label')
   				->removeDecorator('DtDdWrapper');
   		 $this->addElement($project,'project');

	}


}

?>