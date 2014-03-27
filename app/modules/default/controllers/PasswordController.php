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
            $this->_helper->layout()->disableLayout();
            if ($this->getRequest()->isPost()) {
                $data_form = $this->getRequest()->getPost();
                $pass_actual = md5(trim($data_form['pass_actual']));
                $pass_change = md5($data_form['pass_change']);
                $pass_verify = md5($data_form['pass_verify']);
                $where = array(
                    'eid'=>$this->sesion->eid,
                    'oid'=>$this->sesion->oid,
                    'uid'=>$this->sesion->uid,
                    'pid'=>$this->sesion->pid,
                    'escid'=>$this->sesion->escid,
                    'password'=>$pass_actual
                    );
                $tb_user = new Api_Model_DbTable_Users();
                if(is_array($tb_user->_getFilter($where))){
                    if ($pass_change == $pass_verify) {
                        $data = array(
                            'password'=>$pass_change,
                            );
                        $pk = array(
                            'eid'=>$this->sesion->eid,
                            'oid'=>$this->sesion->oid,
                            'uid'=>$this->sesion->uid,
                            'pid'=>$this->sesion->pid,
                            'escid'=>$this->sesion->escid,
                            'subid'=>$this->sesion->subid,
                        );
                        if ($tb_user->_update($data,$pk)) {
                             $json = array(
                                'status'=>true,
                                );
                        }else{
                             $json = array(
                                'status'=>false,
                                'error'=>1
                                );
                        }
                    }
                }else{
                    $json = array(
                        'status'=>false,
                        'error'=>0
                        );
                }
            $this->_response->setHeader('Content-Type','application/json');
            $this->view->json = Zend_Json::encode($json);
            }
          }
        catch (Exception $ex) 
        {
            print "Error al momento de modificar la clave de Usuario: ".$ex->getMessage();
        }
    }

}
