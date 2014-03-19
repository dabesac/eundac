<?php
class Profile_Form_Userinfo extends Zend_Form{    

    public function init(){
        
        $numdoc = new Zend_Form_Element_Text('numdoc');
        $numdoc->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $numdoc->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $numdoc->setAttrib("class","form-control");
        $numdoc->setAttrib("title","Numero de Documento");
        $numdoc->setAttrib("maxlength", "8")->setAttrib("pattern","[0-9]{8}");

        $typedoc =new Zend_Form_Element_Select('typedoc');
        $typedoc->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $typedoc->addMultiOption("D","DNI");
        $typedoc->addMultiOption("P","Pasaporte");
        $typedoc->addMultiOption("C","Carnet Extranjeria");
        $typedoc->setAttrib("class","form-control");


        $year = new Zend_Form_Element_Select("year");
        $year   ->removeDecorator('Label')
                ->setRequired(true)
                ->setAttrib("class","form-control")
                ->setAttrib("title","Año")
                ->addMultiOption("","Año");
        $anio = date('Y');
        for ($i = $anio; $i >= 1940 ; $i--) { 
            $year->addMultiOption($i, $i);
        }
        

        $month= new Zend_Form_Element_Select("month");
        $month  ->removeDecorator('Label')
                ->setRequired(true)
                ->setAttrib("class","form-control")
                ->setAttrib("title","Mes")
                ->addMultiOptions(array(''   => 'Mes',
                                        '01' => 'Enero',
                                        '02' => 'Febrero',
                                        '03' => 'Marzo',
                                        '04' => 'Abril',
                                        '05' => 'Mayo',
                                        '06' => 'Junio',
                                        '07' => 'Julio',
                                        '08' => 'Agosto',
                                        '09' => 'Septiembre',
                                        '10' => 'Octubre',
                                        '11' => 'Noviembre',
                                        '12' => 'Diciembre' ));
                

        $day= new Zend_Form_Element_Select("day");
        $day->removeDecorator('Label')
            ->setRequired(true)
            ->setAttrib("title","Dia")
            ->setAttrib("class", "form-control")
            ->addMultiOption("", "Día");
        for ($i=1; $i <= 31 ; $i++) { 
            $day->addMultiOption($i, $i);
        }

        $sex = new Zend_Form_Element_Select('sex');
        $sex->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $sex->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $sex->addMultiOption("F","Femenino");
        $sex->addMultiOption("M","Masculino");
        $sex->setAttrib("class","form-control");

        $civil = new Zend_Form_Element_Select('civil');
        $civil->removeDecorator('HtmlTag')->removeDecorator('Label');     
        $civil->setRequired(true)->addErrorMessage('Es necesario que selecciones el estado.');
        $civil->addMultiOption("S","Soltero/a");
        $civil->addMultiOption("C","Casado/a");
        $civil->addMultiOption("D","Divorciado/a");
        $civil->addMultiOption("V","Viudo/a");
        $civil->setAttrib("class","form-control");

        $mail_person= new Zend_Form_Element_Text("mail_person");
        $mail_person->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $mail_person->setRequired(true)->addErrorMessage('Ingrese un e-mail correcto');
        $mail_person->setAttrib("maxlength", "50");
        $mail_person->setAttrib("title","Email");
        $mail_person->setAttrib("class","form-control");
        $mail_person->addValidator('EmailAddress',true);

        $mail_work= new Zend_Form_Element_Text("mail_work");
        $mail_work->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $mail_work->setAttrib("maxlength", "50")->setAttrib("size", "30");
        // $mail_work->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $mail_work->setAttrib("title","Email Work");
        $mail_work->setAttrib("class","form-control");
        $mail_work->addValidator('EmailAddress',true);

        $phone= new Zend_Form_Element_Text("phone");
        $phone->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $phone->setAttrib("maxlength", "6")->setAttrib("pattern","[0-9]{6}");
        $phone->setAttrib("title","Phone");
        $phone->setAttrib("class","form-control");
        $phone->setAttrib("value","No Tiene");

        $cellular= new Zend_Form_Element_Text("cellular");
        $cellular->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $cellular->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        $cellular->setAttrib("maxlength", "9")->setAttrib("pattern","[0-9]{9}");
        $cellular->setAttrib("title","Celular");
        $cellular->setAttrib("class","form-control");

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

        $country_s=new Zend_Form_Element_Select('country_s');
        $country_s->removeDecorator('HtmlTag')->removeDecorator('Label');
        $country_s->setRequired(true)->addErrorMessage("Campo Obligatorio");
        $country_s->setRegisterInArrayValidator(false); 
        $country_s->setAttrib("class","form-control");
        $country_s->addMultiOption("","- Seleccione un Pais -");

        $country_p=new Zend_Form_Element_Select('country_p');
        $country_p->removeDecorator('HtmlTag')->removeDecorator('Label');
        $country_p->setRequired(true)->addErrorMessage("Campo Obligatorio");
        $country_p->setRegisterInArrayValidator(false); 
        $country_p->setAttrib("class","form-control");
        $country_p->addMultiOption("","- Seleccione un Departamento -");

        $country_d=new Zend_Form_Element_Select('country_d');
        $country_d->removeDecorator('HtmlTag')->removeDecorator('Label');
        $country_d->setRequired(true)->addErrorMessage("Campo Obligatorio");
        $country_d->setRegisterInArrayValidator(false); 
        $country_d->setAttrib("class","form-control");
        $country_d->addMultiOption("","- Seleccione una Provincia -");
        
        $this->addElements(array($numdoc, $typedoc, $year, $month, $day, $sex, $civil, 
            $mail_person, $mail_work, $phone, $cellular,$country,$country_s,$country_p,$country_d));
    }
}
