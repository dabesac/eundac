<?php
Class Admin_Form_Accessapi extends Zend_Form
{
	public function init()
	{
		$this->setName('frmaccessapi');


		$ip=new Zend_Form_Element_Text('ip');
		$ip->removeDecorator('Label')->removeDecorator('HtmlTag');
		$ip->setAttrib('maxlength','20')->setAttrib('size','20');
		$ip->addValidator('Ip',true)->addErrorMessage('Debes Ingresar Una Ip Valida ');
		// $ip->setRequired(true)->addErrorMessage('Este campo es obligatorio');

		$key=new Zend_Form_Element_Text('key');
		$key->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
		// $key->setRequired(true)->addErrorMessage('Este campo es obligatorio');


		$secret=new Zend_Form_Element_Text('secret');
		$secret->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
		
		$emp=new Zend_Form_Element_Text('emp');
		$emp->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');

		$resp=new Zend_Form_Element_Text('resp');
		$resp->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');

		$mail=new Zend_Form_Element_Text('mail');
		$mail->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
		$mail->addValidator("EmailAddress",true)->addErrorMessage("Direccion Electronica Incorrecta");
		$submit= new Zend_Form_Element_Submit('guardar');
		$submit->removeDecorator('HtmlTag')->removeDecorator('Label');
		$submit->setAttrib('class', 'btn btn-info');
		$submit->setLabel('Guardar');


		
		$this->addElements(array($ip,$key,$secret,$emp,$resp,$mail,$register,$submit));

	}


}

?>