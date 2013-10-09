<?php

class Profile_Form_Statistic extends Zend_Form{

	public function init(){

		$cono_comp=new Zend_Form_Element_Select('cono_comp');
		$cono_comp->removeDecorator('HtmlTag')->removeDecorator('Label');
		$cono_comp->setRequired(true)->addErrorMessage("Campo Obligatorio");
		$cono_comp->addMultiOption("N","No");
		$cono_comp->addMultiOption("S","Si");
		$cono_comp->setAttrib("class","form-control");

		$dependencia=new Zend_Form_Element_Select('dependencia');
		$dependencia->removeDecorator('HtmlTag')->removeDecorator('Label');
		$dependencia->setRequired(true)->addErrorMessage("Campo Obligatorio");
		$dependencia->addMultiOption("P","Padres");
		$dependencia->addMultiOption("SP","Solo Papá");
		$dependencia->addMultiOption("SM","Solo Mamá");
		$dependencia->addMultiOption("H","Hermano");
		$dependencia->addMultiOption("T","Tios");
		$dependencia->addMultiOption("C","Conyugue");
		$dependencia->addMultiOption("O","Otros");
		$dependencia->addMultiOption("N","Ninguno");
		$dependencia->setAttrib("class","form-control");

		$dependen_ud=new Zend_Form_Element_Select('dependen_ud');
		$dependen_ud->removeDecorator('HtmlTag')->removeDecorator('Label');
		$dependen_ud->setRequired(true)->addErrorMessage("Campo Obligatorio");
		$dependen_ud->addMultiOption("S","Si");
		$dependen_ud->addMultiOption("N","No");
		$dependen_ud->setAttrib("class","form-control");
		$dependen_ud->setAttrib("id","depud");

		$num_dep_ud=new Zend_Form_Element_Select('num_dep_ud');
		$num_dep_ud->removeDecorator('HtmlTag')->removeDecorator('Label');
		$num_dep_ud->setRequired(true)->addErrorMessage("Campo Obligatorio");
		for ($i=1; $i<=10 ; $i++) { 
			$num_dep_ud->addMultiOption($i,$i);
		};
		$num_dep_ud->setAttrib("class","form-control");

		$vivienda=new Zend_Form_Element_Select('vivienda');
		$vivienda->removeDecorator('HtmlTag')->removeDecorator('Label');
		$vivienda->setRequired(true)->addErrorMessage("Campo Obligatorio");
		$vivienda->addMultiOption("P","Propia");
		$vivienda->addMultiOption("A","Alquilada");
		$vivienda->addMultiOption("H","Hipotecada");
		$vivienda->addMultiOption("PH","Prestada");
		$vivienda->addMultiOption("O","Otros");
		$vivienda->setAttrib("class","form-control");

		$mat_vivienda=new Zend_Form_Element_Select('mat_vivienda');
		$mat_vivienda->removeDecorator('HtmlTag')->removeDecorator('Label');
		$mat_vivienda->setRequired(true)->addErrorMessage("Campo Obligatorio");
		$mat_vivienda->addMultiOption("NA","Ladrillo o Bloque de Cemento Acabado");
		$mat_vivienda->addMultiOption("NI","Ladrillo o Bloque de Cemento Inconcluso");
		$mat_vivienda->addMultiOption("TA","Adobe o Tapia Acabado");
		$mat_vivienda->addMultiOption("TI","Adobe o Tapia Inconcluso");
		$mat_vivienda->addMultiOption("PA","Precario Acabado");
		$mat_vivienda->addMultiOption("PI","Precario Inconcluso");
		$mat_vivienda->setAttrib("class","form-control");

		$vive_con=new Zend_Form_Element_Select('vive_con');
		$vive_con->removeDecorator('HtmlTag')->removeDecorator('Label');
		$vive_con->setRequired(true)->addErrorMessage("Campo Obligatorio");
		$vive_con->addMultiOption("PD","Padres");
		$vive_con->addMultiOption("P","Solo Padre");
		$vive_con->addMultiOption("M","Solo Madre");
		$vive_con->addMultiOption("PH","Prestada");
		$vive_con->addMultiOption("H","Hermanos");
		$vive_con->addMultiOption("CY","Conyugue");
		$vive_con->addMultiOption("PR","Parientes");
		$vive_con->addMultiOption("S","Solo");
		$vive_con->addMultiOption("O","Otros");
		$vive_con->setAttrib("class","form-control");

		$lugar_alimentacion=new Zend_Form_Element_Select('lugar_alimentacion');
		$lugar_alimentacion->removeDecorator('HtmlTag')->removeDecorator('Label');
		$lugar_alimentacion->setRequired(true)->addErrorMessage("Campo Obligatorio");
		$lugar_alimentacion->addMultiOption("CU","Comedor Universitario");
		$lugar_alimentacion->addMultiOption("P","Pension");
		$lugar_alimentacion->addMultiOption("S","Hogar");
		$lugar_alimentacion->addMultiOption("SP","Se lo Prepara Usted Mismo");
		$lugar_alimentacion->addMultiOption("O","Otros");
		$lugar_alimentacion->setAttrib("class","form-control");

		$costo_alimento=new Zend_Form_Element_Text('costo_alimento');
		$costo_alimento->removeDecorator('HtmlTag')->removeDecorator('Label');
		$costo_alimento->setRequired(true)->addErrorMessage("Campo Obligatorio");
		$costo_alimento->setAttrib("maxlength","4")->setAttrib("size","30");
		$costo_alimento->setAttrib("tittle","Costo de Alimento");
		$costo_alimento->setAttrib("class","form-control");
		$costo_alimento->setAttrib("placeholder","En soles ¬¬");

		$prob_alimento=new Zend_Form_Element_Select('prob_alimento');
		$prob_alimento->removeDecorator('HtmlTag')->removeDecorator('Label');
		$prob_alimento->setRequired(true)->addErrorMessage("Campo Obligatorio");
		$prob_alimento->addMultiOption("N","Ninguno");
		$prob_alimento->addMultiOption("DP","Tus Padres te Envian Dinero :)");
		$prob_alimento->addMultiOption("HG","Tus padres no cuentan con dinero :(");
		$prob_alimento->addMultiOption("AD","Vives Demasiado Lejos de la Undac :(");
		$prob_alimento->addMultiOption("VP","Vives con tus Padres pero No te Apoyan Economicamente :(");
		$prob_alimento->addMultiOption("O","Otros");
		$prob_alimento->setAttrib("class","form-control");

		$tipo_vivienda=new Zend_Form_Element_Select('tipo_vivienda');
		$tipo_vivienda->removeDecorator('HtmlTag')->removeDecorator('Label');
		$tipo_vivienda->setRequired(true)->addErrorMessage("Campo Obligatorio");
		$tipo_vivienda->addMultiOption("CI","Casa Independiente");
		$tipo_vivienda->addMultiOption("CA","Campamento");
		$tipo_vivienda->addMultiOption("D","Departamento");
		$tipo_vivienda->addMultiOption("C","Cuarto");
		$tipo_vivienda->addMultiOption("CC","Cuarto Compartido");
		$tipo_vivienda->addMultiOption("O","Otros");
		$tipo_vivienda->setAttrib("class","form-control");

		$servicios=new Zend_Form_Element_Select('servicios');
		$servicios->removeDecorator('HtmlTag')->removeDecorator('Label');
		$servicios->setRequired(true)->addErrorMessage("Campo Obligatorio");
		$servicios->addMultiOption("ALD","Agua - Luz - Desagüe");
		$servicios->addMultiOption("LD","Solo Luz y Desagüe");
		$servicios->addMultiOption("AL","Solo Agua y Luz");
		$servicios->addMultiOption("S","Solo Agua");
		$servicios->addMultiOption("L","Solo Luz");
		$servicios->addMultiOption("N","Ninguno :(");
		$servicios->setAttrib("class","form-control");

		$num_habitacion=new Zend_Form_Element_Select('num_habitacion');
		$num_habitacion->removeDecorator('HtmlTag')->removeDecorator('Label');
		$num_habitacion->setRequired(true)->addErrorMessage("Campo Obligatorio");
		for ($i=1; $i<=20 ; $i++) { 
			$num_habitacion->addMultiOption($i,$i);
		};
		$num_habitacion->setAttrib("class","form-control");


		$num_personas=new Zend_Form_Element_Select('num_personas');
		$num_personas->removeDecorator('HtmlTag')->removeDecorator('Label');
		$num_personas->setRequired(true)->addErrorMessage("Campo Obligatorio");
		for ($i=1; $i<=20 ; $i++) { 
			$num_personas->addMultiOption($i,$i);
		};
		$num_personas->setAttrib("class","form-control");

		$this->addElements(array($cono_comp, $dependencia, $dependen_ud, $num_dep_ud, $vivienda, $mat_vivienda, $vive_con ,$lugar_alimentacion, $costo_alimento, $prob_alimento, $tipo_vivienda, $servicios, $num_habitacion, $num_personas));

	}
}