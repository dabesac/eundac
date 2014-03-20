<?php

class PasswordController extends Zend_Controller_Action{

	public function init(){
		$sesion  = Zend_Auth::getInstance();
      if(!$sesion->hasIdentity() ){
        $this->_helper->redirector('index',"index",'default');
      }
      $login = $sesion->getStorage()->read();
      
      $this->sesion = $login;
		
	}

	public function changeAction(){
		try{
            
          }
        catch (Exception $ex) 
            {
                print "Error al momento de modificar la clave de Usuario: ".$ex->getMessage();
            }
    }

}
