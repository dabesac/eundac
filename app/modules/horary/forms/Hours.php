<?php
class Horary_Form_Hours extends Zend_Form{
	public function init(){

		$this->setName("frmhours");

		$hour= new Zend_Form_Element_Select('hour');
        $hour->removeDecorator('Label');
        $hour->removeDecorator('HtmlTag');
        $hour->setRequired(true)->addErrorMessage('Este campo es requerido');
        $hour->setAttrib('class','form-control');
        $hour->addMultiOption("","- Selecione -");
        for ($i=6; $i <=13 ; $i++) {
            if ($i<=9) {
         	  $hour->addMultiOption($i,"0".$i);
            }
            else
            {
              $hour->addMultiOption($i,$i);  
            }
        }

        $minute= new Zend_Form_Element_Select('minute');
        $minute->removeDecorator('Label');
        $minute->removeDecorator('HtmlTag');
        $minute->setRequired(true)->addErrorMessage('Este campo es requerido');
        $minute->setAttrib('class','form-control');
        $minute->addMultiOption("","- Selecione -");
        for ($j=0; $j <= 50 ; $j=$j+10) { 
            if ($j==0) {
              $minute->addMultiOption($j,"0".$j);
            }
            else
            {
              $minute->addMultiOption($j,$j);  
            }
        }

        $hour_t= new Zend_Form_Element_Select('hour_t');
        $hour_t->removeDecorator('Label');
        $hour_t->removeDecorator('HtmlTag');
        $hour_t->setAttrib('class','form-control');
        $hour_t->addMultiOption("","- Selecione -");
        for ($i=14; $i <=22 ; $i++) {
            if ($i<=9) {
              $hour_t->addMultiOption($i,"0".$i);
            }
            else
            {
              $hour_t->addMultiOption($i,$i);  
            }
        }

        $minute_t= new Zend_Form_Element_Select('minute_t');
        $minute_t->removeDecorator('Label');
        $minute_t->removeDecorator('HtmlTag');
        $minute_t->setAttrib('class','form-control');
        $minute_t->addMultiOption("","- Selecione -");
        for ($j=0; $j <= 50 ; $j=$j+10) { 
            if ($j==0) {
              $minute_t->addMultiOption($j,"0".$j);
            }
            else
            {
              $minute_t->addMultiOption($j,$j);  
            }
        }

        $submit = new Zend_Form_Element_Submit('save');
        $submit->setAttrib('class','btn btn-success');
        $submit->setLabel('Guardar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");    

        $this->addElements(array($hour,$minute,$hour_t,$minute_t,$submit));
	}
}
