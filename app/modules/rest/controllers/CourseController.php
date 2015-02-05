<?php

class Rest_CourseController extends Zend_Rest_Controller {
	public function init() {
		$this->_helper->layout()->disableLayout();

	}

    public function headAction() {

    }
    
	public function indexAction() {
    	$this->_helper->viewRenderer->setNoRender(true);
    	
        $data = array(
        				'full_name' => 'JKSatyner Santiago',
        				'charge' => 'Developer' );

        return $this->_helper->json->sendJson($data);
	}

    public function getAction() {

    }


    public function postAction() {

    }

    public function putAction() {

    }

    public function deleteAction() {

    }
}