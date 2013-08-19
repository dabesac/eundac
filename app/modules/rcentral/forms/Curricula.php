<?php
class Rcentral_Form_Curricula extends Zend_Form{
    public function init(){
        $this->setName("frmCurricula");

        $curid= new Zend_Form_Element_Text('curid');
        $curid->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $curid->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $curid->setAttrib("title","Código");
        $curid->setAttrib("class","form-control");
        $curid->setAttrib("readonly",true);

        $year= new Zend_Form_Element_Select('year');
        $year->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $year->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $year->setAttrib("title","Año");
        $year->setAttrib("class","form-control");
        $anio=date('Y');
        $year->addMultiOption("","Seleccione");
        for ($i=$anio; $anio >= 1963; $anio--) { 
            $year->addMultiOption($anio,$anio);
        }

        $type= new Zend_Form_Element_Select('type');
        $type->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $type->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $type->setAttrib("title","Tipo de Currícula");
        $type->setAttrib("class","form-control");
        $type->addMultiOption("","Seleccione");
        $type->addMultiOption("S","Semestral");
        $type->addMultiOption("A","Anual");

        $state= new Zend_Form_Element_Select('state');
        $state->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $state->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $state->setAttrib("title","Estado");
        $state->setAttrib("class","form-control");
        $state->addMultiOption("","Seleccione");
        $state->addMultiOption("A","Activo");
        $state->addMultiOption("C","Cerrado");
        $state->addMultiOption("T","Temporal");
        $state->addMultiOption("B","Borrador");

 //        $submit = new Zend_Form_Element_Submit('save');
 //        $submit->setAttrib('class', 'btn btn-info');
 //        $submit->setLabel('Guardar');
 //        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($curid,$year,$type,$state));
	}
}
