<?php
class Admin_Form_Personnew extends Zend_Form{    
    public function init(){
        $this->setName("frmperson");
        
        $pid= new Zend_Form_Element_Text('pid');
        $pid->setRequired(true)->addErrorMessage('Este campo es requerido y solo acepta numeros');
        $pid->removeDecorator('Label')->removeDecorator("HtmlTag");
        $pid->setAttrib("maxlength", "8");
        $pid->setAttrib('class','form-control');
        $pid->setAttrib("title","Ingrese Dni")->addValidator("digits",true);
        $pid->setAttrib("onkeypress","return validNumber(event)");
    
        $first_name = new Zend_Form_Element_Text('first_name');
        $first_name->setRequired(true)->addErrorMessage('Este campo es requerido');
        $first_name->removeDecorator('Label')->removeDecorator("HtmlTag");
        $first_name->setAttrib('class','form-control');
        $first_name->setAttrib("title","Ingrese Nombre");
        $first_name->setAttrib("onkeypress","return soloLetras(event)");

        $last_name0 =new Zend_Form_Element_Text('last_name0');
        $last_name0->setRequired(true)->addErrorMessage('Este campo es requerido');
        $last_name0->removeDecorator('Label')->removeDecorator("HtmlTag");
        $last_name0->setAttrib('class','form-control');
        $last_name0->setAttrib("title","Ingrese Apellido Paterno");
        $last_name0->setAttrib("onkeypress","return soloLetras(event)");

        $last_name1 =new Zend_Form_Element_Text('last_name1');
        $last_name1->setRequired(true)->addErrorMessage('Este campo es requerido');
        $last_name1->removeDecorator('Label')->removeDecorator("HtmlTag");
        $last_name1->setAttrib('class','form-control');
        $last_name1->setAttrib("title","Ingrese Apellido Materno");
        $last_name1->setAttrib("onkeypress","return soloLetras(event)");
        
        $typedoc = new Zend_Form_Element_Select('typedoc');
        $typedoc->removeDecorator('HtmlTag')->setRequired(true)->addErrorMessage('Es necesario que ingrese el tipo de documento');
        $typedoc->setLabel("Ingrese el Tipo de Documento: ");
        $typedoc->removeDecorator('Label');
        $typedoc->setAttrib('class','form-control');
        $typedoc->addMultiOption('','- Seleccione -');
        $typedoc->addMultiOption('D',"DNI");
        $typedoc->addMultiOption('P',"Pasaporte");

        $numdoc= new Zend_Form_Element_Text('numdoc');
        $numdoc->removeDecorator('Label')->removeDecorator("HtmlTag");
        $numdoc->setAttrib("maxlength", "15");
        $numdoc->setAttrib('class','form-control');
        $numdoc->setAttrib("title","Ingrese el documento")->addValidator("digits",true);
        $numdoc->setAttrib("onkeypress","return validNumber(event)");

        $birthday = new Zend_Form_Element_Text('birthday');
        $birthday->removeDecorator('Label')->removeDecorator("HtmlTag");
        $birthday->setAttrib('class','form-control');
        $birthday->setRequired(true)->addErrorMessage('Este campo es requerido');

        $civil =new Zend_Form_Element_Select('civil');
        $civil->setRequired(true)->addErrorMessage('Este campo es requerido');
        $civil->setAttrib('class','form-control');
        $civil->addMultiOption('','- Seleccione -');
        $civil->addMultiOption("S","Soltero");
        $civil->addMultiOption("C","Casado");
        $civil->addMultiOption("V","Viudo");
        $civil->addMultiOption("D","Divorsiado");
        $civil->addMultiOption("O","Otros");
        $civil->removeDecorator('Label')->removeDecorator("HtmlTag");
    
        $sex =new Zend_Form_Element_Select('sex');
        $sex->setRequired(true)->addErrorMessage('Este campo es requerido');
        $sex->setAttrib('class','form-control');
        $sex->addMultiOption('','- Seleccione -');
        $sex->addMultiOption("M","Masculino");
        $sex->addMultiOption("F","Femenino");
        $sex->removeDecorator('Label')->removeDecorator("HtmlTag");

        $mail_person = new Zend_Form_Element_Text('mail_person');
        $mail_person->removeDecorator('Label')->removeDecorator("HtmlTag");
        $mail_person->setAttrib('class','form-control');
        $mail_person->setAttrib("title","tunombre@gmail.com/hotmail.com");

        $mail_work = new Zend_Form_Element_Text('mail_work');
        $mail_work->removeDecorator('Label')->removeDecorator("HtmlTag");
        $mail_work->setAttrib('class','form-control');
        $mail_work->setAttrib("title","tunombre@undac.edu.pe/tunombre@gmail.com/hotmail.com");

        $phone = new Zend_Form_Element_Text('phone');
        $phone->setAttrib("maxlength", "20");
        $phone->setAttrib("title","# de teléfono")->addValidator("digits",true);
        $phone->removeDecorator('Label')->removeDecorator("HtmlTag");
        $phone->setAttrib('class','form-control');
        $phone->setAttrib("onkeypress","return validNumber(event)");        

        $cellular = new Zend_Form_Element_Text('cellular');
        $cellular->setAttrib("maxlength", "15");
        $cellular->setAttrib("title","# de celular")->addValidator("digits",true);
        $cellular->removeDecorator('Label')->removeDecorator("HtmlTag");
        $cellular->setAttrib('class','form-control');
        $cellular->setAttrib("onkeypress","return validNumber(event)");

        $address = new Zend_Form_Element_Text('address');
        $address->removeDecorator('Label')->removeDecorator("HtmlTag");
        $address->setAttrib('class','form-control');
        $address->setAttrib("title","Ingrese su dirección");

        $photografy  = new Zend_Form_Element_File('photografy');
        $photografy->setAttrib('class','form-control');
        $photografy->setAttrib("size", "10");
        $photografy ->addValidator('Extension',false,'jpg,png,gif,jpeg,plain,doc,docx');
        $photografy->addValidator('Size', false, '10024000');
        $photografy ->setLabel('subir foto');
    
        $send = new Zend_Form_Element_Submit('Guardar');
        $send->removeDecorator('Label')->removeDecorator('DtDdWrapper');
        $send->removeDecorator('Label')->removeDecorator("HtmlTag");
        $send->setAttrib('class', 'btn btn-success');

        $send1 = new Zend_Form_Element_Submit('Actualizar');
        $send1->removeDecorator('Label')->removeDecorator('DtDdWrapper');
        $send1->removeDecorator('Label')->removeDecorator("HtmlTag");
        $send1->setAttrib('class', 'btn btn-success');
        
        $this->addElements(array($pid,$first_name,$last_name0,$last_name1,$typedoc,$numdoc,$birthday,$sex,$civil,$mail_person,$mail_work,$phone,$cellular,$address,$send,$send1)); 

    }
}
