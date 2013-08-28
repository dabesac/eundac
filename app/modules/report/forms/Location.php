<?php

class Report_Form_Location extends Zend_Form{    
    public function init(){
        

        $this->setName("frmlocation");
        
        $facid= new Zend_Form_Element_Select('facid');
        $facid->removeDecorator('Label');
        $facid->removeDecorator('HtmlTag');
        $facid->setAttrib('class','form-control');
        $facid->setAttrib("style","height:35px;width:350px ");       
        $where['eid']="20154605046";
        $where['oid']="1";
        $where['state']="A";


        $bdfacultad = new Api_Model_DbTable_Faculty();
        $facultades= $bdfacultad->_getFilter($where);
        $facid->addMultiOption("","- Selecione la Facultad -");
            foreach ($facultades as $facultad){
                $facid->addMultiOption($facultad['facid'],$facultad['name']);
                }
        $escid= new Zend_Form_Element_Select('escid');
        $escid->setRegisterInArrayValidator(false);
        $escid->removeDecorator('Label');
        $escid->removeDecorator('HtmlTag');  
        $escid->setAttrib('class','form-control'); 
        $escid->setAttrib("style","height:35px;width:350px ");            
        $escid->addMultiOption("0","- Selecione la Escuela -");                
        
        $submit = new Zend_Form_Element_Submit('enviar');
        $submit->setAttrib('class', 'btn btn-primary');
        $submit->setLabel('Guardar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($facid,$escid,$submit));        
    }
}

