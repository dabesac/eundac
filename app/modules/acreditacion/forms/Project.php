<?php
Class Acreditacion_Form_Project extends Zend_Form
{
	public function init()
	{
		public $elemnetDecorator =array(
            array('HTMLTag', array('tag' => 'fieldset'))
	    );
	    public $elemnetDecorator1 =array(
	            array('HTMLTag', array('tag' => 'div', 'class' =>
	                'column_4'))
	    );
	    $name = new Zend_Form_Element_Text('name');
    	$name  ->setRequired(true)
	           ->setLabel('Nombre')
               ->addDecorators($this->elemnetDecorator)
               ->setAttrib('maxlength',8)
	           ->setAttrib('onkeypress','return soloNumero(event)')
	           ->addValidator('NotEmpty',true,array('messages' => '*'));
	    $this->addElement($name);

	    $type = new Zend_Form_Element_Select('type');
		$type ->removeDecorator('DtDdWrapper')
		           ->removeDecorator('Label')
		           ->removeDecorator('HtmlTag')
		           ->addMultiOption('P','Proyección Social')
		           ->addMultiOption('I','Investigación');
		$this->addElement($type);
		$this->setDefaults(array('type'=>'I'));

	    $name = new Zend_Form_Element_Text('name');
    	$name  ->setRequired(true)
	           ->setLabel('Nombre')
               ->addDecorators($this->elemnetDecorator)
               ->setAttrib('maxlength',8)
	           ->setAttrib('onkeypress','return soloNumero(event)')
	           ->addValidator('NotEmpty',true,array('messages' => '*'));
	    $this->addElement($name);

	    $min_student = new Zend_Form_Element_Text('min_student');
    	$min_student  ->setRequired(true)
	           ->setLabel('minino Alumnos')
               ->addDecorators($this->elemnetDecorator)
               ->setAttrib('maxlength',8)
	           ->setAttrib('onkeypress','return soloNumero(event)')
	           ->addValidator('NotEmpty',true,array('messages' => '*'));
	    $this->addElement($min_student);

	    $max_student = new Zend_Form_Element_Text('max_student');
    	$max_student  ->setRequired(true)
	           ->setLabel('minino Alumnos')
               ->addDecorators($this->elemnetDecorator)
               ->setAttrib('maxlength',8)
	           ->setAttrib('onkeypress','return soloNumero(event)')
	           ->addValidator('NotEmpty',true,array('messages' => '*'));
	    $this->addElement($max_student);

	    $num_horas = new Zend_Form_Element_Text('num_horas');
    	$num_horas  ->setRequired(true)
	           ->setLabel('minino Alumnos')
               ->addDecorators($this->elemnetDecorator)
               ->setAttrib('maxlength',8)
	           ->setAttrib('onkeypress','return soloNumero(event)')
	           ->addValidator('NotEmpty',true,array('messages' => '*'));
	    $this->addElement($num_horas);

	}


}

?>