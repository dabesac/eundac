<?php

class Period_IndexController extends Zend_Controller_Action {

    public function init()
    {
        /*$sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;*/
    }

    public function indexAction()
    {
        $period_form = new Period_Form_Period();
        $view_data['period_form'] = $period_form;
        $this->view->view_data = $view_data;
    }

}