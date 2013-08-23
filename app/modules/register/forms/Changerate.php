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
      
        $pid= new Zend_Form_Element_Text('pid');
        $pid->setAttrib("readonly","true");
        $pid->setRequired(true)->addErrorMessage('Este campo es requerido y solo acepta numeros');
        $pid->setAttrib("maxlength", "12");
    
        $uid= new Zend_Form_Element_Text('uid');
        $uid->setAttrib("readonly","true");
        $uid->setRequired(true)->addErrorMessage('Este campo es requerido y solo acepta numeros');
        $uid->setAttrib("maxlength", "12");
      
        $ratid= new Zend_Form_Element_Select('ratid');
        $where['eid']=$eid;
        $where['oid']=$oid;
        $where['perid']=$peri;
        // print_r($where);
        $bdrate = new Api_Model_DbTable_Rates();
        $lrate= $bdrate->_getAll($where);
        foreach ($lrate as $es){
        $ratid->addMultiOption($es['ratid'],$es['name']);
        };

        $escid= new Zend_Form_Element_Text('escid');
        $escid->setAttrib("readonly","true");
     
        $doc= new Zend_Form_Element_Text('document_auth');
        $doc->setRequired(true)->addErrorMessage('Este campo es requerido y solo acepta numeros');
         // $doc->setAttrib("maxlength", "100");
        $doc->setAttrib("style","height:30px");

              
        // $comments= new Zend_Form_Element_Text('comments');
        // $comments->setRequired(true)->addErrorMessage('Este campo es requerido y solo acepta numeros');
        // $comments->setAttrib("maxlength", "8");
      
        // $date_payment= new Zend_Form_Element_Text('date_payment');
        // $date_payment->setRequired(true)->addErrorMessage('Este campo es requerido y solo acepta numeros');
        // $date_payment->setAttrib("maxlength", "15");
      
        // $register= new Zend_Form_Element_Text('register');
        // $register->setRequired(true)->addErrorMessage('Este campo es requerido y solo acepta numeros');
        // $register->setAttrib("maxlength", "15");
  
        // $submit = new Zend_Form_Element_Submit('guardar');
        // $submit->removeDecorator('HtmlTag'); 
        // $submit->removeDecorator('Label');
        // $submit->setAttrib('class', 'btn btn-success');

        // $this->addElements(array($perid,$pid,$uid,$escid,$ratid,$doc,$comments,$date_payment,$register,$submit)); 
    }
}        