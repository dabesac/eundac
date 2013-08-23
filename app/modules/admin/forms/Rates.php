<?php
class Admin_Form_Rates extends Zend_Form{    
    public function init(){
        $this->setName("frmtasas");
        
        // $eid=new Zend_Form_Element_Hidden('eid');

        // $oid=new Zend_Form_Element_Hidden('oid');
        $periodo = new Zend_Form_Element_Text('perid');
        $periodo->setRequired(true)->addErrorMessage('Este campo es requerido');
        $periodo->removeDecorator('Label')->removeDecorator("HtmlTag");
        $periodo->setAttrib("maxlength", "3");

        $ratid= new Zend_Form_Element_Text('ratid');
        $ratid->setRequired(true)->addErrorMessage('Este campo es requerido');
        $ratid->removeDecorator('Label')->removeDecorator("HtmlTag");
        $ratid->setAttrib("class", "input-small");
        $ratid->setAttrib("readonly","true");

        $resolucion = new Zend_Form_Element_Text('resolucion');
        $resolucion->setRequired(true)->addErrorMessage('Este campo es requerido');
        $resolucion->removeDecorator('Label')->removeDecorator("HtmlTag");

        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(true)->addErrorMessage('Este campo es requerido');
        $name->removeDecorator('Label')->removeDecorator("HtmlTag");

        $tasanormal = new Zend_Form_Element_Text('t_normal');
        $tasanormal->setRequired(true)->addErrorMessage('Este campo es requerido');
        $tasanormal->removeDecorator('Label')->removeDecorator("HtmlTag");
        $tasanormal->setAttrib("class", "input-small");
        $tasanormal->setAttrib("maxlength", "5");

        $fecha_inicionormal = new Zend_Form_Element_Text('f_ini_tn');
        $fecha_inicionormal->setRequired(true)->addErrorMessage('Este campo es requerido');
        $fecha_inicionormal->removeDecorator('Label')->removeDecorator("HtmlTag");

        $fecha_finalnormal = new Zend_Form_Element_Text('f_fin_tnd');
        $fecha_finalnormal->setRequired(true)->addErrorMessage('Este campo es requerido');
        $fecha_finalnormal->removeDecorator('Label')->removeDecorator("HtmlTag");

        $incremento1 = new Zend_Form_Element_Text('v_t_incremento1');
        $incremento1->setRequired(true)->addErrorMessage('Este campo es requerido');
        $incremento1->removeDecorator('Label')->removeDecorator("HtmlTag");
        $incremento1->setAttrib("class", "input-small");
        $incremento1->setAttrib("maxlength", "5");

        $valortotal1 = new Zend_Form_Element_Text('t_incremento1');
        $valortotal1->setRequired(true)->addErrorMessage('Este campo es requerido');
        $valortotal1->removeDecorator('Label')->removeDecorator("HtmlTag");
        $valortotal1->setAttrib("maxlength", "5");
        // $valortotal1->setAttrib("readonly","true");
        $fecha_final1 = new Zend_Form_Element_Text('f_fin_ti1');
        $fecha_final1->setRequired(true)->addErrorMessage('Este campo es requerido');
        $fecha_final1->removeDecorator('Label')->removeDecorator("HtmlTag");

        $incremento2 = new Zend_Form_Element_Text('v_t_incremento2');
        $incremento2->setRequired(true)->addErrorMessage('Este campo es requerido');
        $incremento2->removeDecorator('Label')->removeDecorator("HtmlTag");
        $incremento2->setAttrib("class", "input-small");
        $incremento2->setAttrib("maxlength", "5");

        $valortotal2 = new Zend_Form_Element_Text('t_incremento2');
        $valortotal2->setRequired(true)->addErrorMessage('Este campo es requerido');
        $valortotal2->removeDecorator('Label')->removeDecorator("HtmlTag");
        $valortotal2->setAttrib("maxlength", "5");
        // $valortotal2->setAttrib("readonly","true");
        $fecha_final2 = new Zend_Form_Element_Text('f_fin_ti2');
        $fecha_final2->setRequired(true)->addErrorMessage('Este campo es requerido');
        $fecha_final2->removeDecorator('Label')->removeDecorator("HtmlTag");

      
        $incremento3 = new Zend_Form_Element_Text('v_t_incremento3');
        $incremento3->setRequired(true)->addErrorMessage('Este campo es requerido');
        $incremento3->removeDecorator('Label')->removeDecorator("HtmlTag");
        $incremento3->setAttrib("class", "input-small");
        $incremento3->setAttrib("maxlength", "5");

        $valortotal3 = new Zend_Form_Element_Text('t_incremento3');
        $valortotal3->setRequired(true)->addErrorMessage('Este campo es requerido');
        $valortotal3->removeDecorator('Label')->removeDecorator("HtmlTag");
        $valortotal3->setAttrib("maxlength", "5");
        // $valortotal3->setAttrib("readonly","true");
      
        $fecha_final3 = new Zend_Form_Element_Text('f_fin_ti3');
        $fecha_final3->setRequired(true)->addErrorMessage('Este campo es requerido');
        $fecha_final3->removeDecorator('Label')->removeDecorator("HtmlTag");


        $submit = new Zend_Form_Element_Submit('guardar');
        $submit->removeDecorator('HtmlTag'); 
        $submit->removeDecorator('Label');
        // $submit->removeDecorator('Label')->removeDecorator("HtmlTag");
        $submit->setAttrib('class', 'btn btn-success');

    
        $this->addElements(array($matricula,$eid,$oid,$ratid,$periodo,$resolucion,$name,$tasanormal,$fecha_inicionormal,$fecha_finalnormal,$incremento1,$fecha_final1,$incremento2,$fecha_final2,$incremento3,$fecha_final3,$valortotal1,$valortotal2,$valortotal3,$submit)); 

    }
}
