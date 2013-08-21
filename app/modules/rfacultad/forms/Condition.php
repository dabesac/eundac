<?php
class Rfacultad_Form_Condition extends Zend_Form
{    
    public function init()
    {
        $this->setName("frmcondicion");

        $uid = new Zend_Form_Element_Hidden('uid');
        $uid->removeDecorator('Label')->removeDecorator("HtmlTag");

        $pid = new Zend_Form_Element_Hidden('pid');
        $pid->removeDecorator('Label')->removeDecorator("HtmlTag");

        $escid = new Zend_Form_Element_Hidden('escid');
        $escid->removeDecorator('Label')->removeDecorator("HtmlTag");

        $subid = new Zend_Form_Element_Hidden('subid');
        $subid->removeDecorator('Label')->removeDecorator("HtmlTag");

        $condi = new Zend_Form_Element_Hidden('condi');
        $condi->removeDecorator('Label')->removeDecorator("HtmlTag");


        $resolucion = new Zend_Form_Element_Text('resolucion');
    //    $resolucion->setAttrib("class","span2");
        $resolucion->removeDecorator("HtmlTag")->removeDecorator("Label");
        $resolucion->setAttrib("maxlength", "300");
         $resolucion->setAttrib("class", "form-control");
         
         $resolucion->setAttrib("style","height:35px;width:500px ");
         //$resolucion->setAttrib("style","width:400px");
         $resolucion->setRequired(true)->addErrorMessage('Este campo es requerido');

        //$resolucion->setIsArray(true);

        $nsemestre = new Zend_Form_Element_Select('nsemestre');
        $nsemestre->setAttrib("class","span1");
        $nsemestre->removeDecorator("HtmlTag")->removeDecorator("Label");
        $nsemestre->setAttrib("maxlength", "2");
        $nsemestre->setRequired(true)->addErrorMessage('Este campo es requerido');
        $nsemestre->setAttrib("class", "form-control col-lg-1");
        $nsemestre->addMultiOption("0","Semestre");
        $nsemestre->addMultiOption("3","3");
        $nsemestre->addMultiOption("4","4");  

        

        $ncreditos = new Zend_Form_Element_Select('ncreditos');
        $ncreditos->setAttrib("class","span1");
        $ncreditos->removeDecorator("HtmlTag")->removeDecorator("Label");
        $ncreditos->setAttrib("maxlength", "1");
        $ncreditos->setAttrib("class", "form-control col-lg-1");
        $ncreditos->setRequired(true)->addErrorMessage('Este campo es requerido');
        $ncreditos->addMultiOption("0","Creditos");
        $ncreditos->addMultiOption("1","1");
        $ncreditos->addMultiOption("2","2");
        $ncreditos->addMultiOption("3","3");
        $ncreditos->addMultiOption("4","4");
        $ncreditos->addMultiOption("5","5");
        $ncreditos->addMultiOption("6","6");


        
        $vmatricula = new Zend_Form_Element_Select('vmatricula');
        $vmatricula->setAttrib("class","span1");
        $vmatricula->removeDecorator("HtmlTag")->removeDecorator("Label");
        $vmatricula->setAttrib("class", "form-control col-lg-1");
        $vmatricula->setAttrib("maxlength", "1");
        $vmatricula->setRequired(true)->addErrorMessage('Este campo es requerido');
        $vmatricula->addMultiOption("0","N° Veces");
        $vmatricula->addMultiOption("3","3");
        $vmatricula->addMultiOption("4","4");
        $vmatricula->addMultiOption("5","5");
        $vmatricula->addMultiOption("6","6");

    

        $detalles = new Zend_Form_Element_Textarea('detalles');
        
        $detalles->removeDecorator("HtmlTag")->removeDecorator("Label");
        $detalles->setAttrib("maxlength", "200")->setAttrib("rows", "2");
         // $detalles->setAttrib("style","width:300px");
        $detalles->setAttrib("style","height:80px;width:500px ");
         $detalles->setAttrib("class", "form-control");
        //$detalles->setIsArray(true);

        $enviar=new Zend_Form_Element_Submit('guardar');
        $enviar->setLabel("Guardar");
        $enviar->setAttrib("class","btn btn-primary ");
        $this->addElements(array($uid,$pid,$escid,$condi,$subid,$resolucion,$nsemestre,$ncreditos,$vmatricula,$detalles,$enviar));
    }
}