<?php
class Rcentral_Form_Generatecode extends Zend_Form{

	public function init(){
	
		$this->setName("frmgeneratecode");

		$csv = new Zend_Form_Element_File('csv');
		$csv->setRequired(true);
		$csv->addValidator('Count', false, 1);
		$csv->addValidator('Extension', false, 'csv');
		$csv->addValidator('File_Upload',true);
		$csv->setValueDisabled(true);
		$csv->getValidator('Extension')->setMessage('El archivo no es un formato .csv');
		$csv->getValidator('File_Upload')->setMessage('Primero debe seleccionar algun archivo');
		$this->addElement($csv);


		$save= new Zend_Form_Element_Submit('save');
        $save->removeDecorator("HtmlTag")->removeDecorator("Label");
        $save->setAttrib("class","btn btn-success");
        $save->setLabel("Subir Archivo");
        $this->addElement($save);

	}



}