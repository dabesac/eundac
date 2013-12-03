<?php
class Admin_Form_Receipt extends Zend_Form{    
    public function init(){
        $sesion  = Zend_Auth::getInstance();
        $sesion1 = $sesion->getStorage()->read();
        $eid = $sesion1->eid;
        $oid = $sesion1->oid;
        $anio = date('Y');
        $anio = substr($anio, 2,3);
        
        $operation= new Zend_Form_Element_Text('operation');
        $operation->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $operation->setAttrib("maxlength", "250")->setAttrib("size", "12");
        $operation->setRequired(true)->addErrorMessage('Este campo es requerido');
        $operation->setAttrib("title","# Operación");
        $operation->setAttrib("class","form-control");
        $operation->setAttrib("onkeypress","return soloNumero(event)");

        $code_student= new Zend_Form_Element_Text("code_student");
        $code_student->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $code_student->setAttrib("maxlength", "10")->setAttrib("size", "12");
        $code_student->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $code_student->setAttrib("title","Codigo");
        $code_student->setAttrib("class","form-control");
        $code_student->setAttrib("onkeypress","return soloNumero(event)");
        
        $info_person = new Zend_Form_Element_Text('info_person');
        $info_person->removeDecorator('Label')->removeDecorator('HtmlTag');     
        $info_person->setAttrib("maxlength", "250")->setAttrib("size", "12");
        $info_person->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $info_person->setAttrib("title","Apellidos y Nombres");
        $info_person->setAttrib("class","form-control");
        $info_person->setAttrib("onkeypress","return soloLetras(event)");

        $payment_date = new Zend_Form_Element_Text('payment_date');
        $payment_date->removeDecorator('Label')->removeDecorator("HtmlTag");
        $payment_date->setRequired(true)->addErrorMessage('Este campo es requerido.');
        $payment_date->setAttrib("class","form-control");

        $amount = new Zend_Form_Element_Text('amount');
        $amount->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $amount->setAttrib("maxlength", "10")->setAttrib("size", "12");
        $amount->setRequired(true)->addErrorMessage('Este campo es requerido.');
        $amount->setAttrib("title","Monto");
        $amount->setAttrib("class","form-control");
        $amount->setAttrib("onkeypress","return soloNumero(event)");

        $concept = new Zend_Form_Element_Text('concept');
        $concept->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $concept->setAttrib("maxlength", "10")->setAttrib("size", "12");
        $concept->setRequired(true)->addErrorMessage('Este campo es requerido.');
        $concept->setAttrib("title","Concepto.");
        $concept->setAttrib("class","form-control");
        $concept->setAttrib("onkeypress","return soloNumero(event)");

        $perid = new Zend_Form_Element_Select('perid');
        $perid->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $perid->setRequired(true)->addErrorMessage('Este campo es requerido.');
        $perid->setAttrib("class","form-control");
        $perid->addMultiOption("","- Seleccione Periodo -");
        $data = array('eid' => $eid, 'oid' => $oid, 'year' => $anio);
        $per = new Api_Model_DbTable_Periods();
        $data_per = $per->_getPeriodsxYears($data);
        foreach ($data_per as $periodos) {
            $perid->addMultiOption($periodos['perid'],$periodos['perid']." - ".$periodos['name']);
        }


        $save = new Zend_Form_Element_Submit('save');
        $save->setAttrib('class', 'btn btn-success');
        $save->removeDecorator("HtmlTag")->removeDecorator("Label");
        $save->setLabel('Guardar');

        $this->addElements(array($operation,$code_student,$info_person,$payment_date,$amount,$perid,$concept,$save));        
    }
}
