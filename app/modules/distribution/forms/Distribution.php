<?php

class Distribution_Form_Distribution extends Zend_Form{    
    public function init(){
    	
        $sesion  = Zend_Auth::getInstance();
        $sesion = $sesion->getStorage()->read();
        $eid = $sesion->eid;
        $oid = $sesion->oid;

        $perid = new Zend_Form_Element_Select("perid");
        $perid->setRequired(true);
        $perid->removeDecorator('Label');
        $perid->removeDecorator('HtmlTag');
        $perid->setAttrib("class","form-control");
        $perid->addMultiOption("","Selecione Periodo");
        $periodsDb = new Api_Model_DbTable_Periods();
        $where = array(
                        'eid'  => $eid,
                        'oid'  => $oid,
                        'year' => 14 );

        $periods = $periodsDb->_getPeriodsxYears($where);
        foreach ($periods as $period) {
            if ($period['perid']['2'] == 'A' or $period['perid']['2'] == 'B'or $period['perid']['2'] == 'N') {
                $perid->addMultiOption($period['perid'],$period['perid'].' | '.$period['name']);
            }
        }
        //$this->view->periodsDistribution = $periodsDistribution;

        
        $number= new Zend_Form_Element_Text("number");
        $number->setAttrib("class","form-control");
        $number->setRequired(true);
        $number->setAttrib("required","");
        $number->setAttrib('readonly',true);
        $number->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $datepress= new Zend_Form_Element_Text("datepress");
        $datepress->setAttrib("class","form-control");
        $datepress->setAttrib("required","");
        $datepress->setRequired(true);
        $datepress->removeDecorator("HtmlTag")->removeDecorator("Label");

        $dateaccept= new Zend_Form_Element_Text("dateaccept");
        $dateaccept->setAttrib("class","form-control");
        $dateaccept->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $state = new Zend_Form_Element_Select("state");
        $state->setRequired(true);
        $state->removeDecorator('Label');
        $state->removeDecorator('HtmlTag');
        $state->setAttrib("class","form-control");
        $state->addMultiOption("B","Borrador");
        $state->addMultiOption("A","Activo");
        /*$state->addMultiOption("C","Cerrado");*/
        
        $this-> addElements(array($perid, $number, $datepress, $dateaccept, $state));
    }
}