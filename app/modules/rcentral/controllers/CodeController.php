<?php
class Rcentral_CodeController extends Zend_Controller_Action
{
      public function init(){
            $sesion  = Zend_Auth::getInstance();
            if(!$sesion->hasIdentity() ){
                  $this->_helper->redirector('index',"index",'default');
            }
            $login = $sesion->getStorage()->read();
            if (!$login->rol['module']=="rcentral"){
                  $this->_helper->redirector('index','index','default');
            }
            $this->sesion = $login;

      }

	public function indexAction(){
		try {
            $fm=new Admin_Form_Personnew();
            $this->view->fm=$fm;
		} catch (Exception $e) {
			// print "Error: ".$e->getMessage();
		}
	}


    public function listuserAction() 
    {
    try {
           
            $this->_helper->layout()->disableLayout();    
            $pid=$this->_getParam("pid");
            $eid = $this->sesion->eid;        
            $oid = $this->sesion->oid;
            $rid = $this->sesion->rid;        
            if ($rid=='RC')
            {
            $listar = new Api_Model_DbTable_Users();
            $list= $listar->_getUsers($eid,$oid,$pid,$uid,$nom);
            $uid=$list[0]['uid'];
            if($uid){
            $this->view->paginator = $list;
            $this->view->nombre=$list[0]['nombrecompleto'];
            $this->view->dni=$list[0]['pid'];            
            }
            }  
            
        } catch (Exception $ex) {
            print "Error filtrar rol : ".$ex->getMessage();
        }
    }

}