<?php
class Soporte_Form_Speciality extends Zend_Form{    
    public function init(){


        $facid= new Zend_Form_Element_Select('facid');
        $facid->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $facid->setAttrib("class","input-large");
        $where['eid']="20154605046";
        $where['oid']="1";
        $bdfaculty = new Api_Model_DbTable_Faculty();
        $facul= $bdfaculty->_getAll($where);
        $facid->addMultiOption("","- Selecione la Facultad -");
        $facid->setRequired(true)->addErrorMessage('Es necesario que selecciones la facultad.');
        foreach ($facul as $fa){
            if ($fa['state']=='A') {
            $facid->addMultiOption($fa['facid'],$fa['name']);
            }
        }

        $subid= new Zend_Form_Element_Select('subid');
        $subid->removeDecorator('Label');
        $subid->removeDecorator('HtmlTag');
        $where['eid']="20154605046";
        $where['oid']="1";
        $bdsubsidiary = new Api_Model_DbTable_Subsidiary();
        $sed= $bdsubsidiary->_getAll($where);
        $subid->addMultiOption("","- Selecione la Sede -");
        $subid->setRequired(true)->addErrorMessage('Es necesario que selecciones la sede.');
        foreach ($sed as $sub){
            $subid->addMultiOption($sub['subid'],$sub['name']);
        }

        $escid= new Zend_Form_Element_Text('escid');
        $escid->removeDecorator('Label')->removeDecorator("HtmlTag");
        $escid->setAttrib("maxlength","15")->removeDecorator('Label');
        $escid->setRequired(true)->addErrorMessage('Este campo es requerido.');
        $escid->setAttrib("title","Ingrese un Codigo");
        
        $name= new Zend_Form_Element_Text('name');
        $name->removeDecorator('Label')->removeDecorator("HtmlTag");
        $name->setAttrib("maxlength", "150")->setAttrib("class","input-medium");
        $name->setRequired(true)->addErrorMessage('Este campo es requerido.');
        $name->setAttrib("title","Ingrese Nombre");

        $abbreviation= new Zend_Form_Element_Text('abbreviation');
        $abbreviation->removeDecorator('Label')->removeDecorator("HtmlTag");
        $abbreviation->setAttrib("maxlength", "20")->setAttrib("class","input-medium");
        $abbreviation->setAttrib("title","Ingrese una Abreviación");

        $parent= new Zend_Form_Element_Select('parent');
        $parent->removeDecorator('Label');
        $parent->removeDecorator('HtmlTag');
        $parent->setRegisterInArrayValidator(false);
        $parent->addMultiOption("","- Selecione la Escuela -");

        $state = new Zend_Form_Element_Select('state');
        $state->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $state->setRequired(true)->addErrorMessage('Es necesario que selecciones un estado.');
        $state->addMultiOption("","- Seleccione Estado -");
        $state->addMultiOption("A","Activo");
        $state->addMultiOption("I","Inactivo");


        $submit = new Zend_Form_Element_Submit('send');
        $submit->setAttrib('class', 'btn btn-primary');
        $submit->setLabel('Guardar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($escid,$facid,$subid,$name,$abbreviation,$state,$parent,$submit));        
    }
}