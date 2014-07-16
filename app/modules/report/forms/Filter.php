<?php

class Report_Form_Filter extends Zend_Form{    
    public function init(){
        

        $this->setName("frmfiltrar");
        $sesion1  = Zend_Auth::getInstance();                
        $sesion = $sesion1->getStorage()->read(); 

        $anio= new Zend_Form_Element_Select('anio');
        $anio->removeDecorator('Label');
        $anio->removeDecorator('HtmlTag');
        $anio->setAttrib('class','form-control'); 
        $anio->setAttrib("style","height:35px;width:150px "); 
        $where['eid'] = '20154605046';  
        $where['oid'] = '1';

        $bdanio = new Api_Model_DbTable_Periods();
        $a = $bdanio->_getAniosPerids($where);

        $anio->addMultiOption("","- Selecione el Año -");
        foreach ($a as $anios){
            $anio->addMultiOption($anios['anioid'],$anios['anio']);
        }

        $perid= new Zend_Form_Element_Select('perid');
        $perid->removeDecorator('Label');
        $perid->removeDecorator('HtmlTag');
        $perid->addMultiOption("","- Selecione el Periodo -");
        $perid->setAttrib('class','form-control'); 
        $perid->setAttrib("style","height:35px;width:200px "); 
       
        
        $mat = new Zend_Form_Element_Checkbox("mat");
        $mat-> removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $mat->setAttrib("title","Matriculados");
        $mat->setAttrib('class','form-control'); 
        $mat->setAttrib("style","height:20px;width:20px ");
        

        $premat = new Zend_Form_Element_Checkbox("premat");
        $premat-> removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $premat->setAttrib('class','form-control'); 
        $premat->setAttrib("style","height:20px;width:20px ");


        $npremat = new Zend_Form_Element_Checkbox("npremat");
        $npremat-> removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $npremat->setAttrib('class','form-control'); 
        $npremat->setAttrib("style","height:20px;width:20px ");
        

        $obs = new Zend_Form_Element_Checkbox("obs");
        $obs-> removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $obs->setAttrib('class','form-control'); 
        $obs->setAttrib("style","height:20px;width:20px ");

        $res = new Zend_Form_Element_Checkbox("res");
        $res-> removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $res->setAttrib('class','form-control'); 
        $res->setAttrib("style","height:20px;width:20px ");


        $x =new Zend_Form_Element_Radio("x");
        $x->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $x->setAttrib('class','form-control'); 
        $x->setAttrib("style","height:20px;width:20px ");

        if ($sesion->rid == 'RC' || $sesion->rid=='VA' || $sesion->rid=='PD' || $sesion->rid=='ES')
        {
            $x->addMultiOptions(array('xfac' => 'Totales X Facultades','xesc' => 'Totales X Escuelas','xsed' => 'Totales X Sedes',
            ))->setSeparator('');
        }

        if ($sesion->rid == 'RF')
        {
            $x->addMultiOptions(array('xesc' => 'Totales X Escuelas','xsed' => 'Totales X Sedes',
            ))->setSeparator('');
        }

        $submit = new Zend_Form_Element_Submit('enviar');
        $submit->setAttrib('class', 'btn btn-primary');
        $submit->setLabel('Guardar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($anio,$perid,$mat,$premat,$npremat,$obs,$res,$x,$submit));        
    }
}

