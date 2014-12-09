<?php

class Egresado_IndexController extends Zend_Controller_Action {

    public function init()
    {

    }
    
    public function indexAction()
    {
        try {
            $this->_helper->redirector('student',"public",'profile');
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }


    }

}
