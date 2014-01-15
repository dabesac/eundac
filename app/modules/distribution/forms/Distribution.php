<?php

class Distribution_Form_Distribution extends Zend_Form{    
    public function init(){
    	
    	$sesion  = Zend_Auth::getInstance();
    	$login = $sesion->getStorage()->read();
    	$this->setName("frmDistribution");
    	$this->setAction("/distribution/distribution/new");
		$eid= new Zend_Form_Element_Hidden("eid");
        $eid->setAttrib("class","form-control");
        $eid->setValue(base64_encode($login->eid));
        $eid->setAttrib('readonly',true);
        $eid->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $oid= new Zend_Form_Element_Hidden("oid");
        $oid->setAttrib("class","form-control");
        $oid->setValue(base64_encode($login->oid));
        $oid->setAttrib('readonly',true);
        $oid->removeDecorator("HtmlTag")->removeDecorator("Label");

        $escid= new Zend_Form_Element_Hidden("escid");
        $escid->setAttrib("class","form-control");
        $escid->setValue(base64_encode($login->escid));
        $escid->setAttrib('readonly',true);
        $escid->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $subid= new Zend_Form_Element_Hidden("subid");
        $subid->setAttrib("class","form-control");
        $subid->setValue(base64_encode($login->subid));
        $subid->setAttrib('readonly',true);
        $subid->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $distid= new Zend_Form_Element_Hidden("distid");
        $distid->setAttrib("class","form-control");
        $distid->setAttrib('readonly',true);
        $distid->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $perid = new Zend_Form_Element_Select("perid");
        $perid->setRequired(true);
        $perid->removeDecorator('Label');
        $perid->removeDecorator('HtmlTag');
        $perid->setAttrib("class","form-control");
        $lperid = new Api_Model_DbTable_Periods();        
        $rows_lperiod=$lperid->_getPeriodsxYears(array("eid"=> $login->eid,"oid"=> $login->oid,"year"=>date('y')));
        $perid->addMultiOption(base64_encode(""),"Seleccione");
        if ($rows_lperiod){
            $niv = $lperid->_getOne($where=array("eid"=> $login->eid,"oid"=> $login->oid,'perid'=>'13N'));
        	if ($niv) $perid->addMultiOption(base64_encode($niv['perid']),$niv['perid']."-".$niv['name']);
            foreach ($rows_lperiod as $_perid ){
                $perid->addMultiOption(base64_encode($_perid['perid']),$_perid['perid']."-".$_perid['name']);
            }
        }
        
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
        $dateaccept->setAttrib("required","");
        $dateaccept->setRequired(true);
        $dateaccept->removeDecorator("HtmlTag")->removeDecorator("Label");
        
        $state = new Zend_Form_Element_Select("state");
        $state->setRequired(true);
        $state->removeDecorator('Label');
        $state->removeDecorator('HtmlTag');
        $state->setAttrib("class","form-control");
        $state->addMultiOption("B","Borrador");
        $state->addMultiOption("A","Activo");
        $state->addMultiOption("C","Cerrado");
        
        $save= new Zend_Form_Element_Submit('save');
        $save->removeDecorator("DtDdWrapper");
        $save->setAttrib("class","btn btn-success");  
        $save->setAttrib("data-loading-text","Guardando...");
        
        $save->setLabel("GUARDAR");

        $this-> addElements(array($eid,$oid,$distid,$escid,$subid,$perid,
                            $number,$datepress,$dateaccept,$state,$save));
    }
}