<?php
class Register_Form_Changerate extends Zend_Form{    
    public function init(){

        $this->setName("frmchangerate");
                
        $sesion  = Zend_Auth::getInstance();
        $login = $sesion->getStorage()->read();
        // print_r($login->period->perid);
    
        $eid = $login->eid;
        $oid = $login->oid;
        $peri = $login->period->perid;

        $perid=new Zend_Form_Element_Text('perid');
        $perid->setAttrib("readonly","true");
        $perid->setAttrib('class','form-control');
      
        $pid= new Zend_Form_Element_Text('pid');
        $pid->setAttrib("readonly","true");
        $pid->setAttrib('class','form-control');
        $pid->setRequired(true)->addErrorMessage('Este campo es requerido y solo acepta numeros');
        $pid->setAttrib("maxlength", "12");
    
        $uid= new Zend_Form_Element_Text('uid');
        $uid->setAttrib("readonly","true");
        $uid->setAttrib('class','form-control');
        $uid->setRequired(true)->addErrorMessage('Este campo es requerido y solo acepta numeros');
        $uid->setAttrib("maxlength", "12");
      
        $ratid= new Zend_Form_Element_Select('ratid');
        $ratid->setAttrib('class','form-control');
        $where['eid']=$eid;
        $where['oid']=$oid;
        $where['perid']=$peri;
        $ratid->addMultiOption("",'- Seleccione -');
        $bdrate = new Api_Model_DbTable_Rates();
        $lrate= $bdrate->_getFilter($where);
        foreach ($lrate as $es){
        $ratid->addMultiOption($es['ratid'],$es['name']);
        };

        $escid= new Zend_Form_Element_Text('escid');
        $escid->setAttrib("readonly","true");
     
        $doc= new Zend_Form_Element_Text('document_auth');
        $doc->setAttrib('class','form-control');
        // $doc->setRequired(true)->addErrorMessage('Este campo es requerido y solo acepta numeros');


              
        $comments= new Zend_Form_Element_Text('comments');
        $comments->setAttrib('class','form-control');
        $comments->setAttrib("maxlength", "8");
      
        $date_payment= new Zend_Form_Element_Text('date_payment');
        $date_payment->setAttrib('class','form-control');
        // $date_payment->setRequired(true)->addErrorMessage('Este campo es requerido y solo acepta numeros');
        $date_payment->setAttrib("maxlength", "15");
        $date_payment->setAttrib("readonly","true");
       
        $submit = new Zend_Form_Element_Submit('guardar');
        $submit->removeDecorator('HtmlTag'); 
        $submit->removeDecorator('Label');
        $submit->setAttrib('class', 'btn btn-success');

        $this->addElements(array($perid,$pid,$uid,$escid,$ratid,$doc,$comments,$date_payment,$submit)); 
    }
}        