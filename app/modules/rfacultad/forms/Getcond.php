<?php

class Rfacultad_Form_Getcond extends Zend_Form{    
    public function init(){
        //$sesion1  = Zend_Auth::getInstance();
        //$sesion = $sesion1->getStorage()->read(); 
        $eid="20154605046";
        $oid="1";
        $this->setName("formgetCod");
        
        $uid= new Zend_Form_Element_Text("uid");
        $uid->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $uid->setAttrib("maxlength", "10");
        $uid->setAttrib("class", "input-small");
        $uid->setRequired(true)->addErrorMessage('Este campo es requerido');
       
        $apepat= new Zend_Form_Element_Text("ap");
        $apepat->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $apepat->setAttrib("maxlength", "40")->setAttrib("size", "40");
        $apepat->setAttrib("class", "input-medium");
        $apepat->setRequired(true)->addErrorMessage('Este campo es requerido');
        
        $apemat= new Zend_Form_Element_Text("am");
        $apemat->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $apemat->setAttrib("maxlength", "40")->setAttrib("size", "40");
        $apemat->setRequired(true)->addErrorMessage('Este campo es requerido');
        $apemat->setAttrib("class", "input-medium");
        
        $name= new Zend_Form_Element_Text("name");
        $name->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $name->setAttrib("maxlength", "40")->setAttrib("size", "40");
        $name->setRequired(true)->addErrorMessage('Este campo es requerido');
        $name->setAttrib("class", "input-medium");
        $name->setAttrib("title","Nombre");

        
        $facid= new Zend_Form_Element_Select('facid');
        $facid->removeDecorator('Label');
        $facid->removeDecorator('HtmlTag');   

        $where['eid']=$eid;
        $where['oid']=$oid;        
        $dbfac = new Api_Model_DbTable_Faculty();
        $faculties= $dbfac->_getAll($where);

        $facid->addMultiOption("","- Selecione la Facultad -");
        foreach ($faculties as $fac)
        {
            $facid->addMultiOption($fac['facid'],$fac['name']);
        }


        $escid= new Zend_Form_Element_Select('escid');
        $escid->setRegisterInArrayValidator(false);
        $escid->removeDecorator('Label');
        $escid->removeDecorator('HtmlTag');        
        // $escid->setRequired(true)->addErrorMessage('Este campo es requerido');
        $escid->addMultiOption("0","- Selecione la Escuela -");


        $cond= new Zend_Form_Element_Select('cond');
        $cond->removeDecorator('Label');
        $cond->removeDecorator('HtmlTag')->setAttrib("class", "input-medium");        
        $cond->addMultiOption('S','Sin Condicion');
        $cond->addMultiOption('C','Con Condicion');
                
        
        $submit = new Zend_Form_Element_Submit('send');
        $submit->setAttrib('class', 'btn btn-primary');
        $submit->setLabel('Guardar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($uid,$name,$apepat,$apemat,$facid,$escid,$cond,$submit));
    }
}
