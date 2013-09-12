<?php

class Rcentral_IndexController extends Zend_Controller_Action {

    public function init()
    {
       
    }
    public function indexAction()
    {
		$pid=$this->sesion->pid;
        $this->view->pid=$pid;

    }
}
