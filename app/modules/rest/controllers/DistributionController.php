<?php

class Rest_DistributionController extends Zend_Rest_Controller {
	public function init() {
		$this->_helper->layout()->disableLayout();
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;

	}

    public function headAction() {}public function indexAction() {}

    public function getAction() {
        $id = $this->_getParam('id');
        $periods_data = $id;
        return $this->_helper->json->sendJson($periods_data);
    }


    public function postAction() {
        $periods_data = 'post';
        return $this->_helper->json->sendJson($periods_data);
    }

    public function putAction() {
        $periods_data = 'put';
        return $this->_helper->json->sendJson($periods_data);
    }

    public function deleteAction() {
        $periods_data = 'delete';
        return $this->_helper->json->sendJson($periods_data);
    }
}