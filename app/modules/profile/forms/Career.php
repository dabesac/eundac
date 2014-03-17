<?php
class Profile_Form_Career extends Zend_Form{    

    public function init(){

    	$xq_undac = new Zend_Form_Element_Text('xq_undac');
    	$xq_undac->setAttrib('class', 'form-control')->setAttrib('title', 'Por que lo elegiste?');
    	$xq_undac->setAttrib('placeholder', 'Por que?');
    	$xq_undac->setRequired(true);

    	$eligio_carrera = new Zend_Form_Element_Select('eligio_carrera');
    	$eligio_carrera->setAttrib('class', 'form-control')->setAttrib('title', 'Le gusta?');
    	$eligio_carrera->addMultiOption("S","Si");
    	$eligio_carrera->addMultiOption("N","No");
    	
    	$carrera_preferencia = new Zend_Form_Element_Text('carrera_preferencia');
    	$carrera_preferencia->setAttrib('class', 'form-control')->setAttrib('title', 'Cual te gusta :)?');
    	$carrera_preferencia->setAttrib('placeholder', 'Que carrera?');

    	$traslate = new Zend_Form_Element_Select('traslate');
    	$traslate->setAttrib('class', 'form-control')->setAttrib('title', 'Te trasladaras :)?');
    	$traslate->addMultiOption("N","No");
        $traslate->addMultiOption("S","Si");

    	$horas_permanece = new Zend_Form_Element_Select('horas_permanece');
    	$horas_permanece->setAttrib('class', 'form-control')->setAttrib('title', 'Cuanto tiempo?');
    	for ($i=2; $i <= 20 ; $i++) { 
    		$horas_permanece->addMultiOption($i,$i.' Horas');
    	}

    	$debido_a = new Zend_Form_Element_Select('debido_a');
    	$debido_a->setAttrib('class', 'form-control')->setAttrib('title', 'Por que te quedas?');
        $debido_a->addMultiOption("HC","Horario de Clases");
        $debido_a->addMultiOption("EG","Estudios en Grupo");
        $debido_a->addMultiOption("CB","Consulta de Libros en la Biblioteca");
        $debido_a->addMultiOption("IU","Internet en la Universidad");
        $debido_a->addMultiOption("O","Otros");

        $this->addElements(array($xq_undac, $eligio_carrera, $carrera_preferencia, $horas_permanece, $debido_a, $traslate));
    }
}