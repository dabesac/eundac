<?php
class Rcentral_Form_Gcode extends Zend_Form{    
    public function init(){
        $sesion1  = Zend_Auth::getInstance();                
        $sesion = $sesion1->getStorage()->read(); 
	    $this->setName("frmGcodigo");
        $this->setAction("/rcentral/code/generatecode/");
		$where['eid']=$sesion->eid;
        $where['oid']=$sesion->oid;
        
		$subid= new Zend_Form_Element_Select('subid');
        $subid->removeDecorator('Label'); 
        $subid->setAttrib('class','form-control');
      
        $subid->setRequired(true)->addErrorMessage('Este campo es requerido');
        $listar = new Api_Model_DbTable_Subsidiary();
        $list= $listar->_getAll($where);
        foreach ($list as $pr){
                $subid->addMultiOption($pr['subid'],$pr['name']);
        }
		
		
		$escuela= new Zend_Form_Element_Select('escid');
        $escuela->removeDecorator('Label');
        $escuela->removeDecorator('HtmlTag');        
        $escuela->addMultiOption("","Seleccione la Escuela");
        $escuela->setAttrib('class','form-control');
		$escuela->setRequired(true)->addErrorMessage('Este campo es requerido');
		
		
        $fingreso= new Zend_Form_Element_Text("fingreso");
        $fingreso->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $fingreso->setRequired(true)->addErrorMessage('Este campo es requerido');
        $fingreso->setAttrib("title","Fecha de ingreso");
        $valiDate = new Zend_Validate_Date();
        $valiDate->setFormat('dd-mm-YYYY');
        $fingreso->addValidator($valiDate);
        $fingreso->setAttrib('class','form-control');
		
		$modalidad= new Zend_Form_Element_Select('idmod');
        $modalidad->removeDecorator('Label');
        $modalidad->setAttrib('class','form-control');       
        $modalidad->setRequired(true)->addErrorMessage('Este campo es requerido');
        $modalidad->addMultiOption("","Selecione la modalidad");
		$modalidad->addMultiOption("1","Exoneracion");
        $modalidad->addMultiOption("2","Convenio");
        $modalidad->addMultiOption("3","Concurso Admision");
        $modalidad->addMultiOption("4","Cobertura");
        $modalidad->addMultiOption("5","Segunda Opcion");
        $modalidad->addMultiOption("6","Traslado Externo");
		$modalidad->addMultiOption("7","Traslado Interno");
		$modalidad->addMultiOption("8","Segunda Carrera");
		$modalidad->addMultiOption("9","Otros");
        
        $proceso= new Zend_Form_Element_Select('idproc');
        $proceso->removeDecorator('Label');
        $proceso->setAttrib('class','form-control');            
        $proceso->setRequired(true)->addErrorMessage('Este campo es requerido');
        $proceso->addMultiOption("","Selecione el Proceso Admision");
        $proceso->addMultiOption("1","Centro Pre I");
        $proceso->addMultiOption("2","Examen Ordinario 1er Periodo Ano");
        $proceso->addMultiOption("3","Profesionalizacion Docente");
        $proceso->addMultiOption("4","Examen Ordinario 2do Periodo");
        $proceso->addMultiOption("5","Centro Pre II");
        $proceso->addMultiOption("6","Profesionalizacion Docente Enfermeria");
		$proceso->addMultiOption("7","Complementacion Docente");
		$proceso->addMultiOption("8","Escuela Post Grado");
		$proceso->addMultiOption("9","Centro Pre III");
		$proceso->addMultiOption("0","Examen Extraordinario");

        $orden= new Zend_Form_Element_Text("orden");
        $orden->setAttrib('class','form-control');       
        $orden->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $orden->setRequired(true)->addErrorMessage('Este campo es requerido');
        $orden->setAttrib("title","Orden Merito");

        $resolucion= new Zend_Form_Element_Text("resolucion");
        $resolucion->setAttrib('class','form-control'); 
        $resolucion->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $resolucion->setRequired(true)->addErrorMessage('Este campo es requerido');
        $resolucion->setAttrib("title","Resolucion");
		
        $submit = new Zend_Form_Element_Submit('enviar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label")->removeDecorator('DtDdWrapper');
        $submit->setAttrib("class","btn btn-success");
		
		$this->addElements(array($subid,$escuela,$fingreso,$modalidad,$proceso,$orden,$resolucion,$submit));       
    }
}
		
		