<?php
class Profile_Form_Userinfo extends Zend_Form{    

    public function init(){
        
        $dni= new Zend_Form_Element_Text('numdoc');
        $dni->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $dni->setAttrib("maxlength", "8");
        $dni->setRequired(true)->addErrorMessage('Este campo es requerido');
        $dni->setAttrib("title","DNI");
        $dni->setAttrib("class","form-control");

        $year= new Zend_Form_Element_Text("year");
        $year->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $year->setAttrib("maxlength", "4")->setAttrib("size", "4");
        $year->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $year->setAttrib("title","Año");
        $year->setAttrib("class","form-control");

        $month= new Zend_Form_Element_Text("month");
        $month->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $month->setAttrib("maxlength", "2")->setAttrib("size", "2");
        $month->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $month->setAttrib("title","Mes");
        $month->setAttrib("class","form-control");

        $day= new Zend_Form_Element_Text("day");
        $day->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $day->setAttrib("maxlength", "2")->setAttrib("size", "2");
        $day->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $day->setAttrib("title","Dia");
        $day->setAttrib("class","form-control");

        $sex = new Zend_Form_Element_Select('sex');
        $sex->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $sex->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $sex->addMultiOption("F","Femenino");
        $sex->addMultiOption("M","Masculino");
        $sex->setAttrib("class","form-control");

        $civil = new Zend_Form_Element_Select('civil');
        $civil->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $civil->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $civil->addMultiOption("S","Soltero/a");
        $civil->addMultiOption("C","Casado/a");
        $civil->addMultiOption("D","Divorciado/a");
        $civil->addMultiOption("V","Viudo/a");
        $civil->setAttrib("class","form-control");

        $mail_person= new Zend_Form_Element_Text("mail_person");
        $mail_person->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $mail_person->setAttrib("maxlength", "50")->setAttrib("size", "30");
        $mail_person->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $mail_person->setAttrib("title","Email");
        $mail_person->setAttrib("class","form-control");
        $email_person->addValidator('EmailAddress',true)->addErroMessage('Direccion electronica no valida');

        $mail_work= new Zend_Form_Element_Text("mail_work");
        $mail_work->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $mail_work->setAttrib("maxlength", "50")->setAttrib("size", "30");
        $mail_work->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $mail_work->setAttrib("title","Email Work");
        $mail_work->setAttrib("class","form-control");
        $email_person->addValidator('EmailAddress',true)->addErrorMessage('Direccion electronica no valida');

        $phone= new Zend_Form_Element_Text("phone");
        $phone->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $phone->setAttrib("maxlength", "10")->setAttrib("size", "30");
        $phone->setAttrib("title","Phone");
        $phone->setAttrib("class","form-control");
        $phone->setAttrib("value","No Tiene");

        $cellular= new Zend_Form_Element_Text("cellular");
        $cellular->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $cellular->setAttrib("maxlength", "9")->setAttrib("size", "30");
        $cellular->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $cellular->setAttrib("title","Cellular");
        $cellular->setAttrib("class","form-control");

        
        $this->addElements(array($dni, $year, $month, $day, $sex, $civil, $mail_person, $mail_work, $phone, $cellular));
    }
}
