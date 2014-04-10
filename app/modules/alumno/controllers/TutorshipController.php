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
        $server = new Eundac_Connect_openerp();

        $query = array(
                      array( 'column'   => 'of_id',
                            'operator' => '=',
                            'value'    => '4SI',
                            'type'     => 'string' )
                    );
        $data_project = array();
        $idsDepartment = $server->search('hr.department', $query);
        $attributes = array();
        if ($idsDepartment) {
            $data_project = $server->read($idsDepartment, $attributes, 'hr.department');
        }
        
        $departmentId = $data_project[0]['id'];

        $query = array(
                    array(  'column'   => 'department_id',
                            'operator' => '=',
                            'value'    => $departmentId,
                            'type'     => 'string' )
                    );

        $idsTutoring = $server->search('tutoring', $query);
        print_r($idsTutoring);


    }

}