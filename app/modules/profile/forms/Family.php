<?php
class Profile_Form_Family extends Zend_Form{    

    public function init(){
    	
    	$type = new Zend_Form_Element_Select('type');
        $type->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $type->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $type->addMultiOption("F","Padre");
        $type->addMultiOption("M","Madre");
        $type->addMultiOption("S","Hijo/a");
        $type->addMultiOption("B","Hermano/a");
        $type->setAttrib("class","form-control");
        
        $last_name= new Zend_Form_Element_Text('last_name');
        $last_name->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $last_name->setAttrib("maxlength", "30")->setAttrib("size", "50");
        $last_name->setRequired(true)->addErrorMessage('Este campo es requerido');
        $last_name->setAttrib("title","Apellidos");
        $last_name->setAttrib("class","input-sm");

        $first_name= new Zend_Form_Element_Text('first_name');
        $first_name->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $first_name->setAttrib("maxlength", "30")->setAttrib("size", "50");
        $first_name->setRequired(true)->addErrorMessage('Este campo es requerido');
        $first_name->setAttrib("title","Nombres");
        $first_name->setAttrib("class","input-sm");

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
        $numdoc->setAttrib("class","input-sm");

        $birthday= new Zend_Form_Element_Text("birthday");
        $birthday->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $birthday->setAttrib("maxlength", "10")->setAttrib("size", "50");
        $birthday->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $birthday->setAttrib("title","Birthday");
        $birthday->setAttrib("class","input-sm");

        $ocupacy= new Zend_Form_Element_Text('ocupacy');
        $ocupacy->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $ocupacy->setAttrib("maxlength", "30")->setAttrib("size", "50");
        $ocupacy->setRequired(true)->addErrorMessage('Este campo es requerido');
        $ocupacy->setAttrib("title","Ocupacion");
        $ocupacy->setAttrib("class","input-sm");

        $health = new Zend_Form_Element_Select('health');
        $health->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $health->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $health->addMultiOption("N","No");
        $health->addMultiOption("S","Si");
        $health->setAttrib("class","form-control");

        $phone= new Zend_Form_Element_Text('phone');
        $phone->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $phone->setAttrib("maxlength", "9")->setAttrib("size", "50");
        $phone->setRequired(true)->addErrorMessage('Este campo es requerido');
        $phone->setAttrib("title","phone");
        $phone->setAttrib("class","input-sm");

        $address= new Zend_Form_Element_Text('address');
        $address->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $address->setAttrib("maxlength", "30")->setAttrib("size", "50");
        $address->setRequired(true)->addErrorMessage('Este campo es requerido');
        $address->setAttrib("title","Direccion");
        $address->setAttrib("class","input-sm");

        $assignee = new Zend_Form_Element_Select('assignee');
        $assignee->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $assignee->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $assignee->addMultiOption("S","Si");
        $assignee->addMultiOption("N","No");
        $assignee->setAttrib("class","form-control");

        $submit = new Zend_Form_Element_Submit('save');
        $submit->setAttrib('class','btn btn-info pull-right');
        $submit->setLabel('Guardar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($type, $last_name, $first_name, $sex, $live, $typedoc, $numdoc, $birthday, $ocupacy,$health, $phone, $address, $assignee, $submit));
    }
}