<?php

class Period_Form_Period extends Zend_Form{    
    public function init(){
    	$code = new Zend_Form_Element_Text('code');
    	$code 	->removeDecorator('Label')->removeDecorator('HtmlTag')
    			->setRequired(true)
    			->addValidator('NotEmpty', true, array('messages' => 'Falta el código...'))
    			->setAttribs(array(
    							'required' => true));
    	$this->addElement($code);


    	$name = new Zend_Form_Element_Text('name');
    	$name 	->removeDecorator('Label')->removeDecorator('HtmlTag')
    			->setRequired(true)
    			->addValidator('NotEmpty', true, array('messages' => 'Falta el nombre...'))
    			->setAttribs(array(
    							'required' => true));
    	$this->addElement($name);


    	$resolution = new Zend_Form_Element_Text('resolution');
    	$resolution	->removeDecorator('Label')->removeDecorator('HtmlTag')
	    			->setRequired(true)
	    			->addValidator('NotEmpty', true, array('messages' => 'Indique el numero de resolución...'))
	    			->setAttribs(array(
    							'required' => true));
    	$this->addElement($resolution);


    	$class_start = new Zend_Form_Element_Text('class_start');
    	$class_start->removeDecorator('Label')->removeDecorator('HtmlTag')
	    			->setRequired(true)
	    			->addValidator('NotEmpty', true, array('messages' => 'Indique una fecha para el inicio de clases...'))
	    			->setAttribs(array(
    							'required' => true,
    							'js-type'  => 'date'));
    	$this->addElement($class_start);


    	$class_end = new Zend_Form_Element_Text('class_end');
    	$class_end 	->removeDecorator('Label')->removeDecorator('HtmlTag')
	    			->setRequired(true)
	    			->addValidator('NotEmpty', true, array('messages' => 'Indique una fecha para el fin de clases...'))
	    			->setAttribs(array(
    							'required' => true,
    							'js-type'  => 'date'));
    	$this->addElement($class_end);


    	$register_start = new Zend_Form_Element_Text('register_start');
    	$register_start	->removeDecorator('Label')->removeDecorator('HtmlTag')
		    			->setRequired(true)
		    			->addValidator('NotEmpty', true, array('messages' => 'Indique una fecha para el inicio de matricula...'))
		    			->setAttribs(array(
	    							'required' => true,
    							'js-type'  => 'date'));
    	$this->addElement($register_start);


    	$register_end = new Zend_Form_Element_Text('register_end');
    	$register_end 	->removeDecorator('Label')->removeDecorator('HtmlTag')
		    			->setRequired(true)
		    			->addValidator('NotEmpty', true, array('messages' => 'Indique una fecha para el fin de matricula...'))
		    			->setAttribs(array(
    							'required' => true,
    							'js-type'  => 'date'));
    	$this->addElement($register_end);


    	$first_start = new Zend_Form_Element_Text('first_start');
    	$first_start 	->removeDecorator('Label')->removeDecorator('HtmlTag')
		    			->setRequired(true)
		    			->addValidator('NotEmpty', true, array('messages' => 'Falta fecha inicial para el ingreso de notas del primer parcial...'))
		    			->setAttribs(array(
    							'required' => true,
    							'js-type'  => 'date'));
    	$this->addElement($first_start);


		$first_end = new Zend_Form_Element_Text('first_end');
    	$first_end 	->removeDecorator('Label')->removeDecorator('HtmlTag')
	    			->setRequired(true)
	    			->addValidator('NotEmpty', true, array('messages' => 'Falta fecha final para el ingreso de notas del primer parcial...'))
	    			->setAttribs(array(
    							'required' => true,
    							'js-type'  => 'date'));
    	$this->addElement($first_end);


	    $second_start = new Zend_Form_Element_Text('second_start');
    	$second_start 	->removeDecorator('Label')->removeDecorator('HtmlTag')
		    			->setRequired(true)
		    			->addValidator('NotEmpty', true, array('messages' => 'Falta fecha inicial para el ingreso de notas del segundo parcial...'))
		    			->setAttribs(array(
    							'required' => true,
    							'js-type'  => 'date'));
    	$this->addElement($second_start);


    	$second_end = new Zend_Form_Element_Text('second_end');
		$second_end ->removeDecorator('Label')->removeDecorator('HtmlTag')
	    			->setRequired(true)
		    		->addValidator('NotEmpty', true, array('messages' => 'Falta fecha final para el ingreso de notas del segundo parcial...'))
	    			->setAttribs(array(
    							'required' => true,
    							'js-type'  => 'date'));
    	$this->addElement($second_end);
    }
}