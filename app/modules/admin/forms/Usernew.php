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
    $rid->setRequired(true)->addErrorMessage('Este campo es requerido');
    $rid->removeDecorator('Label')->removeDecorator("HtmlTag");
    $rid->setAttrib('class','form-control');
    $rid->setAttrib("title","Seleccione un rol");
    $rid->addMultiOption('','- Seleccione -');
    $dbrol= new Api_Model_DbTable_Rol();
    $datar= $dbrol->_getAll($where);
    foreach ($datar as $datarol){
        if ($datarol['rid']!="AL" and $datarol['rid']!="EG") {
            $rid->addMultiOption($datarol['rid'],$datarol['name']);
        }
    }

    $subid= new Zend_Form_Element_Select('subid');
    $subid->removeDecorator('Label')->removeDecorator('HtmlTag');
    $subid->setAttrib('class','form-control');
    $subid->setRequired(true)->addErrorMessage('Este campo es requerido');
    $subid->setAttrib('title','Seleccione una sede');
    $subid->addMultiOption('','- Seleccione -');
    $dbsub= new Api_Model_DbTable_Subsidiary();
    $datas= $dbsub->_getAll($where);
    foreach ($datas as $datasub){
        $subid->addMultiOption($datasub['subid'],$datasub['name']);
    }

    $escid=new Zend_Form_Element_Select('escid');
    $escid->removeDecorator('Label')->removeDecorator('HtmlTag');
    $escid->setAttrib('class','form-control');
    $escid->setRequired(true)->addErrorMessage('Este campo es requerido');
    $escid->setAttrib('title','Seleccione una escuela');
    $escid->addMultiOption("",'- Seleccione una Sede -');
    $dbesc= new Api_Model_DbTable_Speciality();
    $datas= $dbesc->_getAll($where);
    foreach ($datas as $datae){
        $escid->addMultiOption($datae['escid'],$datae['name']);
    }

    $state= new Zend_Form_Element_Select('state');
    $state->removeDecorator('Label')->removeDecorator('HtmlTag');
    $state->setAttrib('class','form-control');
    $state->setAttrib('title','Seleccione un estado');
    $state->addMultiOption('A','ACTIVO');
    $state->addMultiOption('I','INACTIVO');
    $state->addMultiOption('B','BLOQUEADO');
    $state->addMultiOption('S','SUSPENDIDO');
    $state->addMultiOption('E',"BAJA");

    $comments= new Zend_Form_Element_Text('comments');
    $comments->removeDecorator('Label')->removeDecorator('HtmlTag');
    $comments->setAttrib('class','form-control');
    $comments->setAttrib('title','Ingrese un comentario');
    
    $send= new Zend_Form_Element_Submit('Guardar');
    $send->removeDecorator('Label')->removeDecorator('DtDdWrapper');
    $send->removeDecorator('Label')->removeDecorator("HtmlTag");
    $send->setAttrib('class', 'btn btn-success');

    $update= new Zend_Form_Element_Submit('Actualizar');
    $update->removeDecorator('Label')->removeDecorator('DtDdWrapper');
    $update->removeDecorator('Label')->removeDecorator("HtmlTag");
    $update->setAttrib('class', 'btn btn-success');

    $this->addElements(array($rid,$subid,$escid,$state,$comments,$send,$update));
}
}