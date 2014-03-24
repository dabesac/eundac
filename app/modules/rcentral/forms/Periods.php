<?php
class Rcentral_Form_Periods extends Zend_Form{    
    public function init(){
        $this->setName("frmperiodonuevo");
        
        $perid=new Zend_Form_Element_Text('perid');
		$perid->setAttrib("class","form-control"); 
        $perid->removeDecorator('HtmlTag')->removeDecorator('Label');
        $perid->setRequired(true)->addErrorMessage('Este campo es requerido');
        //$perid->setAttrib("size", "3");
        $perid->setAttrib("maxlength","3");

        $oid=new Zend_Form_Element_Hidden('oid');
		$oid->removeDecorator('HtmlTag')->removeDecorator('Label');
        
        $eid=new Zend_Form_Element_Hidden('eid');
		$eid->removeDecorator('HtmlTag')->removeDecorator('Label');
		
        $document_auth = new Zend_Form_Element_Text('document_auth');
        $document_auth->setAttrib("class","form-control"); 
		$document_auth->removeDecorator('HtmlTag')->removeDecorator('Label');
        $document_auth->setRequired(true)->addErrorMessage('Este campo es requerido');

        $nperiodo = new Zend_Form_Element_Text('name');
        $nperiodo->setAttrib("class","form-control"); 
		$nperiodo->removeDecorator('HtmlTag')->removeDecorator('Label');
        $nperiodo->setRequired(true)->addErrorMessage('Este campo es requerido');
        $nperiodo->setAttrib("maxlength","50");

        $fechaini = new Zend_Form_Element_Text('class_start_date');
		$fechaini->removeDecorator('HtmlTag')->removeDecorator('Label');
		$fechaini->setAttrib("class","form-control"); 
        $fechaini->setRequired(true)->addErrorMessage('Este campo es requerido');

        $fechafin = new Zend_Form_Element_Text('class_end_date');
        $fechafin->setRequired(true)->addErrorMessage('Este campo es requerido');
		$fechafin->setAttrib("class","form-control"); 
		$fechafin->removeDecorator('HtmlTag')->removeDecorator('Label');
             
        $finimat = new Zend_Form_Element_Text('start_registration');
        $finimat->setRequired(true)->addErrorMessage('Este campo es requerido');
		$finimat->setAttrib("class","form-control"); 
		$finimat->removeDecorator('HtmlTag')->removeDecorator('Label');

        $ffinmat = new Zend_Form_Element_Text('end_registration');
        $ffinmat->setRequired(true)->addErrorMessage('Este campo es requerido');
		$ffinmat->setAttrib("class","form-control"); 
		$ffinmat->removeDecorator('HtmlTag')->removeDecorator('Label');
		
		$fip1 = new Zend_Form_Element_Text('start_register_note_p');
        $fip1->setRequired(true)->addErrorMessage('Este campo es requerido');
		$fip1->setAttrib("class","form-control"); ;
		$fip1->removeDecorator('HtmlTag')->removeDecorator('Label');
		
		$ffp1 = new Zend_Form_Element_Text('end_register_note_p');
        $ffp1->setRequired(true)->addErrorMessage('Este campo es requerido');
		$ffp1->setAttrib("class","form-control"); 
		$ffp1->removeDecorator('HtmlTag')->removeDecorator('Label');
		
		$fip2 = new Zend_Form_Element_Text('start_register_note_s');
        $fip2->setRequired(true)->addErrorMessage('Este campo es requerido');
		$fip2->setAttrib("class","form-control"); 
		$fip2->removeDecorator('HtmlTag')->removeDecorator('Label');
		
		$ffp2 = new Zend_Form_Element_Text('end_register_note_s');
        $ffp2->setRequired(true)->addErrorMessage('Este campo es requerido');
		$ffp2->setAttrib("class","form-control"); 
		$ffp2->removeDecorator('HtmlTag')->removeDecorator('Label');		
		
		
        $submit = new Zend_Form_Element_Submit('guardar');
        $submit->removeDecorator('Label')->removeDecorator("HtmlTag");
		$submit->setLabel("Guardar");
        $submit->setAttrib("class","btn btn-success");

        $this->addElements(array($perid,$eid,$oid,$nperiodo,$fechaini,$fechafin,$state,
        	$fip1, $ffp1,$fip2,$ffp2, $document_auth,$finimat,$ffinmat,$submit)); 

    }
}