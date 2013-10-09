<?php
class Profile_Form_Family extends Zend_Form{    

    public function init(){
    	
    	$type = new Zend_Form_Element_Select('type');
        $type->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $type->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $type->addMultiOption("","Elija Parentesco");
        $type->setAttrib("class","form-control");
        $type->setAttrib("id","type");
        
        $lastname= new Zend_Form_Element_Text('lastname');
        $lastname->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $lastname->setAttrib("maxlength", "30")->setAttrib("size", "30");
        $lastname->setRequired(true)->addErrorMessage('Este campo es requerido');
        $lastname->setAttrib("title","Apellidos");
        $lastname->setAttrib("class","form-control");

        $firtsname= new Zend_Form_Element_Text('firtsname');
        $firtsname->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $firtsname->setAttrib("maxlength", "30")->setAttrib("size", "30");
        $firtsname->setRequired(true)->addErrorMessage('Este campo es requerido');
        $firtsname->setAttrib("title","Nombres");
        $firtsname->setAttrib("class","form-control");

        $sex = new Zend_Form_Element_Select('sex');
        $sex->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $sex->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $sex->addMultiOption("M","Masculino");
        $sex->addMultiOption("F","Femenino");
        $sex->setAttrib("class","form-control");

        $live = new Zend_Form_Element_Select('live');
        $live->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $live->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $live->addMultiOption("","Vive?");
        $live->addMultiOption("S","Si");
        $live->addMultiOption("N","No");
        $live->setAttrib("class","form-control");
        $live->setAttrib("id","live");

        $typedoc = new Zend_Form_Element_Select('typedoc');
        $typedoc->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $typedoc->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $typedoc->addMultiOption("D","DNI");
        $typedoc->addMultiOption("P","Pasaporte");
        $typedoc->setAttrib("class","form-control");

        $numdoc= new Zend_Form_Element_Text('numdoc');
        $numdoc->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $numdoc->setAttrib("maxlength", "8")->setAttrib("size", "10");
        $numdoc->setRequired(true)->addErrorMessage('Este campo es requerido');
        $numdoc->setAttrib("title","numdoc");
        $numdoc->setAttrib("class","form-control");

        $year= new Zend_Form_Element_Text("year");
        $year->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $year->setAttrib("maxlength", "4")->setAttrib("size", "4");
        $year->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $year->setAttrib("title","Año");
        $year->setAttrib("placeholder","Año");
        $year->setAttrib("class","form-control");

        $month= new Zend_Form_Element_Text("month");
        $month->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $month->setAttrib("maxlength", "2")->setAttrib("size", "2");
        $month->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $month->setAttrib("title","Mes");
        $month->setAttrib("placeholder","Mes");
        $month->setAttrib("class","form-control");

        $day= new Zend_Form_Element_Text("day");
        $day->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $day->setAttrib("maxlength", "2")->setAttrib("size", "2");
        $day->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $day->setAttrib("title","Dia");
        $day->setAttrib("placeholder","Día");
        $day->setAttrib("class","form-control");

        $ocupacy= new Zend_Form_Element_Text('ocupacy');
        $ocupacy->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $ocupacy->setAttrib("maxlength", "30")->setAttrib("size", "30");
        $ocupacy->setAttrib("title","Ocupacion");
        $ocupacy->setAttrib("class","form-control");
        $ocupacy->setAttrib("id","ocupacy");

        $health = new Zend_Form_Element_Select('health');
        $health->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $health->addMultiOption("N","No");
        $health->addMultiOption("S","Si");
        $health->setAttrib("class","form-control");
        $health->setAttrib("id","health");

        $phone= new Zend_Form_Element_Text('phone');
        $phone->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $phone->setAttrib("maxlength", "9")->setAttrib("size", "30");
        $phone->setAttrib("title","phone");
        $phone->setAttrib("class","form-control");
        $phone->setAttrib("id","phone");

        $address= new Zend_Form_Element_Text('address');
        $address->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $address->setAttrib("maxlength", "30")->setAttrib("size", "30");
        $address->setAttrib("title","Direccion");
        $address->setAttrib("class","form-control");
        $address->setAttrib("id","address");

        $assignee = new Zend_Form_Element_Select('assignee');
        $assignee->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $assignee->addMultiOption("N","No");
        $assignee->addMultiOption("S","Si");
        $assignee->setAttrib("class","form-control");
        $assignee->setAttrib("id","assignee");


        $this->addElements(array($type, $lastname, $firtsname, $sex, $live, $typedoc, $numdoc, $year, $month, $day, $ocupacy,$health, $phone, $address, $assignee));
    }
}