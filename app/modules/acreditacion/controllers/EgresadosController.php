
<?php

class Acreditacion_EgresadosController extends Zend_Controller_Action {

    public function init()
    {
       $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
    }

    public function indexAction(){	
    	$currentYear = date('Y');
    	$this->view->currentYear = $currentYear;
	   	/*$model = "standares_acredit";
    	$params = array(
			'eid' => base64_encode($this->sesion->eid),
			'oid' =>base64_encode($this->sesion->oid),
            'escid' => base64_encode($this->sesion->escid),
    		'anio' => base64_encode('2013'),
    		);
    	$prueba = new Eundac_Connect_Api($model,$params);
    	$data= $prueba->connectAuth();
    	$this->view->dimensions = $data;
    	print_r($data);*/
    }

    public function listegresadosAction(){
    	$this->_helper->layout()->disableLayout();

    	$userDb = new Api_Model_DbTable_Users();

    	$egresados = $userDb->_getEgresadosxAnioxEscuela();

    	$anio = $this->_getParam('anio');
    }
}