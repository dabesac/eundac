<?php

<<<<<<< HEAD
class Register_Form_Buscar extends Zend_Form{
    public function init(){
               
        $this->setName('frmbuscar');

        $uid= new Zend_Form_Element_Text('uid');
        $uid->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $uid->setAttrib('maxlength','10')->setAttrib('size','10');
        $uid->setAttrib('class','form-control');
        $uid->setAttrib('onkeypress','return validNumber(event)');
        $uid->setRequired(true)->addErrorMessage('Este campo es Obligatorio');

        $this->addElements(array($uid));
    }
}
=======
class Register_Form_Buscar extends Zend_Form{    
    public function init(){
        

        $this->setName("frmbuscar");
        
        $uid= new Zend_Form_Element_Text("uid");
        $uid->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $uid->setAttrib("maxlength", "10")->setAttrib("size", "10");
        $uid->setAttrib("class", "form-control");
        $uid->setAttrib("style","height:35px;width:200px ");
        $uid->setRequired(true)->addErrorMessage('Este campo es requerido');
        
        $nombre= new Zend_Form_Element_Text("nombre");
        $nombre->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $nombre->setAttrib("maxlength", "80")->setAttrib("size", "40");
        $nombre->setAttrib("class", "form-control");
        $nombre->setAttrib("style","height:35px;width:500px ");
        $nombre->setRequired(true)->addErrorMessage('Este campo es requerido');
        
        $submit = new Zend_Form_Element_Submit('enviar');
        $submit->setAttrib('class', 'btn btn-primary');
        $submit->setLabel('Guardar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($uid,$nombre,$submit));        
    }
}

>>>>>>> ee73adb541eff90c8a79b7213319bbfd8e1dd394
