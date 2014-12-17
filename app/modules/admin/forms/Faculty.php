<?php
class Admin_Form_Faculty extends Zend_Form{    
    public function init(){
        
        $code= new Zend_Form_Element_Text('facid');
        $code->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $code->setAttrib("maxlength", "2")->setAttrib("size", "10");
        $code->setRequired(true)->addErrorMessage('Este campo es requerido');
        $code->setAttrib("title","Codigo");
        $code->setAttrib("class","input-sm");
        $code->setAttrib("onkeypress","return soloNumero(event)");

        $name= new Zend_Form_Element_Text("name");
        $name->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $name->setAttrib("maxlength", "50")->setAttrib("size", "40");
        $name->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $name->setAttrib("title","Nombre");
        $name->setAttrib("class","input-sm");
        
        $abrev = new Zend_Form_Element_Text('abbreviation');
        $abrev->removeDecorator('Label')->removeDecorator('HtmlTag');     
        $abrev->setAttrib("maxlength", "15")->setAttrib("size", "40");
        $abrev->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $abrev->setAttrib("title","Abreviatura");
        $abrev->setAttrib("class","input-sm");

        $datcre = new Zend_Form_Element_Text('created');
        $datcre->setRequired(true)->addErrorMessage('Este campo es requerido');
        $datcre->setAttrib("class","input-sm");
        $datcre->removeDecorator('Label')->removeDecorator("HtmlTag");


        $state = new Zend_Form_Element_Select('state');
        $state->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $state->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $state->addMultiOption("A","Activo");
        $state->addMultiOption("I","Inactivo");
        $state->setAttrib("class","input-sm");

        $previous_name= new Zend_Form_Element_Text("previous_name");
        $previous_name->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $previous_name->setAttrib("maxlength", "250")->setAttrib("size", "40");
        $previous_name->setAttrib("title","Nombre De Escuela");
        $previous_name->setAttrib("class","input-sm");

        $previous_escid= new Zend_Form_Element_Text("previous_escid");
        $previous_escid->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $previous_escid->setAttrib("maxlength", "10")->setAttrib("size", "40");
        $previous_escid->setAttrib("title","Codigo De Escuela");
        $previous_escid->setAttrib("class","input-sm");

        $submit = new Zend_Form_Element_Submit('save');
        $submit->setAttrib('class','btn btn-info');
        $submit->setLabel('Guardar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $submitup = new Zend_Form_Element_Submit('update');
        $submitup->setAttrib('class', 'btn btn-info');
        $submitup->setLabel('Actualizar');
        $submitup->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($code,$name,$abrev,$datcre,$state,$previous_name,$previous_escid,$submit,$submitup));        
    }
}
