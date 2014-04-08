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
	                 array(	'column'   =>'author',
							'operator' =>'=',
							'value'    =>$this->sesion->infouser['numdoc'] )
            		);

        /*$data_project = array();
        $connect = new Eundac_Connect_openerp();
        $ids = $connect->search('inv.pro.project',$query);
        if ($ids) {
            $data_project = $connect->read($ids, $attributes, 'inv.pro.project');
        }
        print_r($query);
        $this->view->data_project=$data_project;*/
    }

}