<?php

class Alumno_TutorshipController extends Zend_Controller_Action {

    public function init() {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	$this->sesion = $login;

    }

    public function listtutorsAction(){
    	$escid = $this->sesion->escid;

        $query = array(
	                 array(	'column'   => 'state',
							'operator' => '=',
							'value'    => 'A',
							'type'     => 'string' )
            		);
        $data_project = array();
        $server = new Eundac_Connect_openerp();
        /*$ids = $server->search('sede', $query);
        if ($ids) {
            $data_project = $server->read($ids, $attributes, 'sedes');
        }*/
        print_r($data_project);
    }

}