<?php

class Rest_YearController extends Zend_Rest_Controller {
	public function init() {
		$this->_helper->layout()->disableLayout();

        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
	}

    public function headAction() {} public function indexAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $perid = $this->sesion->period->perid;
    	
        $year = date('Y');
        $c_y  = 0;
        for ($i=$year+1; $i>1990; $i--) { 
            $years[$c_y] = $i;
            $c_y++;
        }
        $currentYear = '20'.substr($perid, 0, 2);

        $data['years']   = $years;
        $data['current'] = $currentYear;

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