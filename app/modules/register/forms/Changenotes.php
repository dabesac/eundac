<?php
class Register_Form_Changenotes extends Zend_Form{    
    public function init(){
        

        $this->setName("frm");
        
        $notafinal= new Zend_Form_Element_Text('notafinal');
        $notafinal->removeDecorator('Label')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $notafinal->setAttrib('maxlength','10')->setAttrib('size','10');
        $notafinal->setAttrib('class','form-control');
        $notafinal->setAttrib('onkeypress','return validNumber(event)');
        $notafinal->setRequired(true)->addErrorMessage('Este campo es Obligatorio');
        
        $document_auth= new Zend_Form_Element_Text("document_auth");
        $document_auth->removeDecorator('Label')->removeDecorator("HtmlTag")->removeDecorator("Label");
        $document_auth->setAttrib("maxlength", "50")->setAttrib("size", "40");
        $document_auth->setAttrib("class", "form-control");
        $document_auth->setAttrib("style","height:35px;width:300px ");
        $document_auth->setRequired(true)->addErrorMessage('Este campo es requerido');
        
        $submit = new Zend_Form_Element_Submit('Guardar');
        $submit->setAttrib('class', 'btn btn-primary');
        $submit->setLabel('Guardar');
        $submit->removeDecorator("HtmlTag")->removeDecorator("Label");

        $this->addElements(array($notafinal,$document_auth,$submit));        
    }

}
