<?php
class Admin_Form_New extends Zend_Form{    
    public function init(){
    	//Datos del Usuario
    	$sesion  = Zend_Auth::getInstance();
    	$sesion = $sesion->getStorage()->read();
        
        $title = new Zend_Form_Element_Text('title');
        $title	->removeDecorator('Label')
        		->setRequired(true)
        		->setAttrib('class', 'form-control input-lg')
        		->setAttrib('title', 'Título')
        		->setAttrib('maxlength', '100');

        $description = new Zend_Form_Element_Textarea('description');
        $description->removeDecorator('Label')
        			->setRequired(true)
	        		->setAttrib('class', 'form-control')
	        		->setAttrib('rows', '14')
	        		->setAttrib('style', 'resize : none;')
	        		->setAttrib('title', 'Descripción');

	    $img = new Zend_Form_Element_File('img');
	    $img	->removeDecorator('Label')->removeDecorator('HtmlTag')
				->addValidator('Count', false, 1)
				->addValidator('Size', false, 2097152)
		      	->setMaxFileSize(2097152)
				->addValidator('Extension', false, 'jpg,png,gif')
				->setValueDisabled(true);

	    $rolDb = new Api_Model_DbTable_Rol();
	    $eid = $sesion->eid;
	    $oid = $sesion->oid;
	    $where = array(	'eid'   => $eid,
						'oid'   => $oid,
						'state' => 'A' );

	    $attrib = array('rid', 'name');
	    $order = array('name');

	    $rols = $rolDb->_getFilter($where, $attrib, $order);


	    $rid = new Zend_Form_Element_Select('rid');
        $rid->removeDecorator('Label')
    		->setAttrib('class', 'form-control')
    		->setAttrib('rows', '15')
    		->setAttrib('style', 'resize : none;')
    		->setAttrib('title', 'Descripción')
    		->addMultiOption('Todos', 'Todos');
        foreach ($rols as $rol) {
        	$rid->addMultiOption($rol['rid'], $rol['name']);
        }
       	
       	$type = new Zend_Form_Element_Select('type');
        $type	->removeDecorator('Label')
        		->setAttrib('class', 'form-control')
        		->setAttrib('title', 'Tipo')
        		->addMultiOptions(array('NE' => 'Informativo',
    									'SY' => 'Sistema' ));

        $state = new Zend_Form_Element_Select('state');
        $state	->removeDecorator('Label')
        		->setAttrib('class', 'form-control')
        		->setAttrib('title', 'Tipo')
        		->addMultiOptions(array('A' => 'Activo',
        								'B' => 'Borrador',
    									'C' => 'Cerrado' ));

        $this->addElements(array($title, $description ,$img, $rid, $type, $state, $save));
	}
}