<?php
class Rcentral_GeneratecodeController extends Zend_Controller_Action{

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
		$form = new Rcentral_Form_Generatecode();
		$this->view->form=$form;

		if ($this->getRequest()->isPost()){
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)){
            	$name = $form->getValues();
				$name_o = $name['csv'];
            	
            	unset($formData['MAX_FILE_SIZE']);
                unset($formData['save']);
                $upload = new Zend_File_Transfer_Adapter_Http();
                $filterRename = new Zend_Filter_File_Rename(array('target' => '/srv/www/eundac/html/filecsv/'.$name_o, 'overwrite' => false));

                $nombre_file = '/srv/www/eundac/html/filecsv/'.$name_o;
                if (file_exists($nombre_file)) {
                    unlink($nombre_file);
                }
                $upload->addFilter($filterRename);
                if (!$upload->receive()) {
                    $this->view->message = 'Error receiving the file';
                }
                else{
                	$this->_redirect('/rcentral/generatecode/readcsv/name/'.base64_encode($name_o)); 
                }
            }		
        }
	}

	public function readcsvAction(){
		$name_f = $this->_getParam('name');

		if ($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            $name_final = $data['namefile'];

            $where = array('file'=>$name_final);

			$server = new Eundac_Connect_Api('ingresantes',$where);
            $subject = $server->connectAuth();
		}
		else{
			$this->view->name=$name_f;
		}

	}
}