<?php 
class Admin_Form_Usernew extends Zend_Form{

public function init(){
    $sesion  = Zend_Auth::getInstance();
    $login = $sesion->getStorage()->read();

	$this->setName("frmuser");
    $eid=$login->eid;
    $oid=$login->oid;
    $where=array('eid'=>$eid,'oid'=>$oid);

    $rid = new Zend_Form_Element_Select('rid');
    // $rid->setRequired(true)->addErrorMessage('Este campo es requerido');
    $rid->removeDecorator('Label')->removeDecorator("HtmlTag");
    $rid->setAttrib('class','form-control');
    $rid->setAttrib("title","Seleccione un rol")->addValidator("alpha",true);
    $rid->addMultiOption('','- Seleccione -');
    $dbrol= new Api_Model_DbTable_Rol();
    $datar= $dbrol->_getAll($where);
    foreach ($datar as $datarol){
        $rid->addMultiOption($datarol['rid'],$datarol['name']);
    }

    $subid= new Zend_Form_Element_Select('subid');
    $subid->removeDecorator('Label')->removeDecorator('HtmlTag');
    $subid->setAttrib('class','form-control');
    $subid->setRequired(true)->addErrorMessage('Este campo es requerido');
    $subid->setAttrib('title','Seleccione una sede')->addValidator('alpha',true);
    $subid->addMultiOption('','- Seleccione -');
    $dbsub= new Api_Model_DbTable_Subsidiary();
    $datas= $dbsub->_getAll($where);
    foreach ($datas as $datasub){
        $subid->addMultiOption($datasub['subid'],$datasub['name']);
    }

    $escid=new Zend_Form_Element_Select('escid');
    $escid->removeDecorator('Label')->removeDecorator('HtmlTag');
    $escid->setAttrib('class','form-control');
    $escid->setAttrib('title','Seleccione una escuela')->addValidator("alpha",true);
    $escid->addMultiOption('','- Seleccione -');
    $dbesc= new Api_Model_DbTable_Speciality();
    $datae= $dbesc->_getAll($where);
    foreach ($datae as $dataesc) {
        if ($dataesc['state']='A' or $dataesc['state']='B') {
        $escid->addMultiOption($dataesc['escid'],$dataesc['name']);
        }
    }

    $state= new Zend_Form_Element_Select('state');
    $state->removeDecorator('Label')->removeDecorator('HtmlTag');
    $state->setAttrib('class','form-control');
    $state->setAttrib('title','Seleccione un estado')->addValidator('alpha',true);
    $state->addMultiOption('','- Seleccione -');
    $state->addMultiOption('A','ACTIVO');
    $state->addMultiOption('I','INACTIVO');
    $state->addMultiOption('B','BLOQUEADO');
    $state->addMultiOption('S','SUSPENDIDO');
    $state->addMultiOption('E',"BAJA");

    $resolution= new Zend_Form_Element_Text('resolution');
    $resolution->removeDecorator('Label')->removeDecorator('HtmlTag');
    $resolution->setAttrib('class','form-control');
    $resolution->setAttrib('title','Ingrese una resolución')->addValidator('alpha',true);

    $send= new Zend_Form_Element_Submit('Guardar');
    $send->removeDecorator('Label')->removeDecorator('DtDdWrapper');
    $send->removeDecorator('Label')->removeDecorator("HtmlTag");
    $send->setAttrib('class', 'btn btn-success');

    $this->addElements(array($rid,$subid,$escid,$state,$resolution,$send));
}
}