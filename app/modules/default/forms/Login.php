<?php

class Default_Form_Login extends Zend_Form
{
    public function init(){
        //DataBases
        $rids = new Api_Model_DbTable_Rol();

        /* Form Elements & Other Definitions Here ... */
        $this->setName("frmLogin");
        $this->setAttrib("class","form-control");
        /*$eid = new Zend_Form_Element_Select("eid");
        $eid->setRequired(true);
        $eids = new Api_Model_DbTable_Entity();
        $rows_eids=$eids->_getAll();
        foreach ($rows_eids as $_eid ){
            $eid->addMultiOption(base64_encode($_eid['eid']),$_eid['name']);
        }
        $eid->setAttrib("readonly","");*/
        
        /*$oid = new Zend_Form_Element_Select("oid");
        $oid->setRequired(true);
        $oid->removeDecorator('Label');
        $oid->removeDecorator('HtmlTag');        
        $oids = new Api_Model_DbTable_Org();
        $rows_oids=$oids->_getAll($data);
        $oid->addMultiOption(base64_encode(""),"Seleccione");
        if ($rows_oids){
            foreach ($rows_oids as $_oid ){
                $oid->addMultiOption(base64_encode($_oid['oid']),$_oid['name']);
            }
        }
        $oid->setAttrib("readonly","");*/
        
        $data['eid'] = "20154605046";
        $data['oid'] = "1";
        $rid = new Zend_Form_Element_Select("rid");
        $rid->setRequired(true)->addErrorMessage('Este campo es requerido');
        $rid->addMultiOption("","Seleccione un Rol")->removeDecorator('Label');
        $rid->removeDecorator('HtmlTag')->addFilters(array('StringTrim', 'StripTags'));
        $rid->setAttrib("class","form-control");
        $order = array('name');
        $rrows_oids=$rids->_getAllACL($data, $order);
        if ($rrows_oids){
        	foreach ($rrows_oids as $_rid ){
        		$rid->addMultiOption(base64_encode($_rid['rid']).";--;".$_rid['prefix'],$_rid['name']);
        	}
        }
        $rid->setAttrib("rel","tooltip");
        $rid->setAttrib("placeholder","Seleccione su Rol");
    
        
        
        $usuario  = new Zend_Form_Element_Text('usuario');
        $usuario->setRequired(true)->addErrorMessage('Este campo es requerido');
        $usuario->setAttrib("title","Código de Matricula o DNI ");
        $usuario->removeDecorator('Label');
        $usuario->setAttrib("class","form-control");
        $usuario->setAttrib("maxlength","10");
        $usuario->removeDecorator('HtmlTag');        
		$usuario->setAttrib("rel","tooltip");
		$usuario->setAttrib("placeholder","Código de Matricula o DNI");
		

        $clave = new Zend_Form_Element_Password("clave");
        $clave->setRequired(true)->addErrorMessage('Este campo es requerido');;
        $clave->setAttrib("title","Ingrese su contraseña");
		$clave->setAttrib("class","form-control");
		$clave->setAttrib("rel","tooltip");
		$clave->setAttrib("placeholder","Contraseña");
		
        $clave->removeDecorator('HtmlTag');
        $clave->removeDecorator('Label')->addFilters(array('StringTrim', 'StripTags'));
        
        $submit = new Zend_Form_Element_Submit('enviar');
        $submit->setAttrib('class', 'form-control sendForm')->setLabel("Ingresar");
		$submit->setAttrib('id', 'enviarf');
        $submit->removeDecorator('HtmlTag');

        $this->addElements(array($rid,$usuario,$clave,$submit));        
    }

}



