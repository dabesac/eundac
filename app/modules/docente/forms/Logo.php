<?php

class Docente_Form_Logo extends Zend_Form{


	public function init()
	{
	
		$this->setName("frmlogo");

		$photo = new Zend_Form_Element_File('photo');
		$photo->setLabel('Logo Escuela');
		//       // ->setDestination(Zend_Registry::get('config')->paths->backend->images->profile);
		      // ->setDestination(APPLICATION_PATH.'/upload')
			  // ->setDestination(BASE_PATH . '/html/upload');
		$photo->addValidator('Count', false, 1);
		$photo->addValidator('Size', false, 2097152)
		      ->setMaxFileSize(2097152);
		$photo->addValidator('Extension', false, 'jpg,png,gif');
		$photo->setValueDisabled(true);
		$this->addElement($photo,'photo');


		$save= new Zend_Form_Element_Submit('save');
        $save->removeDecorator("HtmlTag")->removeDecorator("Label");
        $save->setAttrib("class","btn btn-success");
        $save->setLabel("Guardar Avance");
        $this->addElement($save);

	}



}