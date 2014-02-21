<?php 

class Admin_AccessapiController extends Zend_Controller_Action
{
	public function init()
	{
		$sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        if (!$login->rol['module']=="admin"){
            $this->_helper->redirector('index','index','admin');
        }
        $this->sesion = $login;
	}

	public function indexAction(){
		$allquery=new Api_Model_DbTable_Accessapi();
		$this->view->allquery=$allquery->_getAll();
	}

	public function newAction()
	{

		$form=new Admin_Form_Accessapi();
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			if($form->isValid($formData)){

			$data=$this->getRequest()->getPost();
			$query=new Api_Model_DbTable_Accessapi();
			$data['register']=$this->sesion->uid;
			$v_ip=$data['ip'];
			unset($data['guardar']);
			if($query->addNew($data)){
				$this->_redirect('/admin/accessapi/index');
			}
			}
		}
        $this->view->form=$form;
	}

	public function editAction(){

		$ip=$this->_getParam('ip');
		$key=base64_decode($this->_getParam('key'));

		$form=new Admin_Form_Accessapi();
		$this->view->form=$form;

		$data=new Api_Model_DbTable_Accessapi();
        $pk=array('ip'=>$ip,'key'=>$key);
        $query=$data->_getOne($pk);  
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
             
            if ($form->isValid($formData)) {
            	$v_ip=$formData['ip'];
                unset($formData['guardar']);
                $str = "ip ='$ip' and key='$key'";
                $query2 = new Api_Model_DbTable_Accessapi();
                $change = $query2->addChange($formData,$str);
                $this->_redirect('/admin/accessapi/index');
             	print_r($formData);
             }
            
            
        }else{
        	$form->populate($query);
        	
        }
    }

	
	public function deleteAction(){
		$ip=$this->_getParam('ip');
        $key=base64_decode($this->_getParam('key'));
        $req=new Api_Model_DbTable_Accessapi();
    
        $str = "ip ='$ip' and key='$key'";
        $req->deleteRow($str);
        $this->_redirect('/admin/accessapi/index');
	}
}
?>