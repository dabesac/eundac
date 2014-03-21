<?php
class Profile_Form_Academic extends Zend_Form{    

    public function init(){

    	$institution= new Zend_Form_Element_Text('institution');
        $institution->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $institution->setAttrib("maxlength", "50");
        $institution->setRequired(true)->addErrorMessage("Campo Obligatorio");
        $institution->setAttrib("title","Institucion");
        $institution->setAttrib("class","form-control");

        // $location= new Zend_Form_Element_Text('location');
        // $location->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        // $location->setAttrib("maxlength", "100")->setAttrib("size", "30");
        // $location->setRequired(true)->addErrorMessage('Este campo es requerido');
        // $location->setAttrib("title","Institucion");
        // $location->setAttrib("class","form-control");

        $dbcountry=new Api_Model_DbTable_Country();
        $data=$dbcountry->_getAll();

        $country=new Zend_Form_Element_Select('country');
        $country->removeDecorator('HtmlTag')->removeDecorator('Label');
        $country->setRequired(true)->addErrorMessage("Campo Obligatorio");
        $country->setAttrib("class","form-control");
        $country->addMultiOption("","- Seleccione -");        
        foreach ($data as $data) {
            $country->addMultiOption($data['coid'],$data['name_c']);            
        }   
        
        $dbcountrys=new Api_Model_DbTable_CountryState();
        $datas=$dbcountrys->_getAll();

        $country_s=new Zend_Form_Element_Select('country_s');
        $country_s->removeDecorator('HtmlTag')->removeDecorator('Label');
        $country_s->setRequired(true)->addErrorMessage("Campo Obligatorio");
        $country_s->setRegisterInArrayValidator(false); 
        $country_s->setAttrib("class","form-control");
        $country_s->addMultiOption("","- Seleccione un Departamento -");
        foreach ($datas as $datas) {
            $country_s->addMultiOption($datas['cosid'],$datas['name_s']);            
        }

        $dbcountryp=new Api_Model_DbTable_CountryProvince();
        $datap=$dbcountryp->_getAll();

        $country_p=new Zend_Form_Element_Select('country_p');
        $country_p->removeDecorator('HtmlTag')->removeDecorator('Label');
        $country_p->setRequired(true)->addErrorMessage("Campo Obligatorio");
        $country_p->setRegisterInArrayValidator(false); 
        $country_p->setAttrib("class","form-control");
        $country_p->addMultiOption("","- Seleccione un Departamento -");
        foreach ($datap as $datap) {
            $country_p->addMultiOption($datap['proid'],$datap['name_p']);            
        }

        $dbcountryd=new Api_Model_DbTable_CountryDistrict();
        $datad=$dbcountryd->_getAll();

        $country_d=new Zend_Form_Element_Select('country_d');
        $country_d->removeDecorator('HtmlTag')->removeDecorator('Label');
        $country_d->setRequired(true)->addErrorMessage("Campo Obligatorio");
        $country_d->setRegisterInArrayValidator(false); 
        $country_d->setAttrib("class","form-control");
        $country_d->addMultiOption("","- Seleccione una Provincia -");
        foreach ($datad as $datad) {
            $country_d->addMultiOption($datad['disid'],$datad['name_d']);            
        }

		$year_end= new Zend_Form_Element_Text('year_end');
        $year_end->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $year_end->setAttrib("maxlength", "4")->setAttrib("size", "10");
        $year_end->setRequired(true)->addErrorMessage("Campo Obligatorio");
        $year_end->setAttrib("title","Institucion");
        $year_end->setAttrib("class","form-control");

        $type = new Zend_Form_Element_Select('type');
        $type->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $type->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $type->addMultiOption("E","Estatal");
        $type->addMultiOption("P","Particular");
        $type->addMultiOption("PA","Parroquial");
        $type->setAttrib("class","form-control"); 

        $title = new Zend_Form_Element_Select('title');
        $title->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $title->setRequired(true)->addErrorMessage("Campo Obligatorio");
        $title->addMultiOption("SE","Secundaria");
        $title->addMultiOption("SU","Superior");
        $title->addMultiOption("DI","Diplomado");
        $title->addMultiOption("PT","Post Grado");
        $title->addMultiOption("PH","PHD");
        $title->addMultiOption("O","Otros");
        $title->setAttrib("class","form-control"); 

        $this->addElements(array($institution, $country,$country_s,$country_p,$country_d, $year_end, $type, $title));

   	}

}