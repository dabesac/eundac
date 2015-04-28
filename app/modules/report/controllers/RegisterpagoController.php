<?php
 class Report_RegisterpagoController extends Zend_Controller_Action{

 	public function init(){
 		$sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		$this->sesion = $login;

 	}

 	public function indexAction(){
            $fm=new Report_Form_Buscar();
            $this->view->fm=$fm;
/*
*/
 	}
public function getpagoAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $uid=$this->sesion->uid;

            $name_cache='siaf'.$uid;
            $frontendOptions = array(
                'lifetime' => 3600, // tiempo de vida de caché de 2 horas
            'automatic_serialization' => true
            );

            $ruta = "/tmp/mat/";
            if (!is_dir($ruta)){
                if (!mkdir($ruta, 0777)){
                    $ruta=$ruta;
                }
            }
            $backendOptions = array(
                'cache_dir' => "$ruta" // Carpeta donde alojar los archivos de caché
            );

            $cache = Zend_Cache::factory('Core',
                'File',
            $frontendOptions,
            $backendOptions);

            $frmdata=$this->getRequest()->getPost();
            $anhio = base64_encode($frmdata['anhio']);
            $name = base64_encode($frmdata['nombre']);
            $where = array(
                        'report' => "c2lhZg==",
                        'year'=> $anhio,
                        'name'=> $name);

            if(!$subject = $cache->load("$name_cache")) 
            {
                require_once 'Zend/Loader.php';
                Zend_Loader::loadClass('Zend_Rest_Client');
                $base_url = 'http://localhost:8080/';
                $route = '/'.base64_encode('s3lf.040c0c030$0$0').'/'.base64_encode('__999c0n$um3r999__').'/api_siaf';
                $client = new Zend_Rest_Client($base_url);
                $httpClient = $client->getHttpClient();
                $httpClient->setConfig(array("timeout" => 12680));
                $response = $client->restget($route,$where);
                $lista=$response->getBody();
                $subject = Zend_Json::decode($lista);
                $cache->save($subject,$name_cache);
            }
            $this->view->subjects = $subject;
        }
        catch(Exception $ex )
        {
            print ("Error Controller get Datos: ".$ex->getMessage());
        } 
    }

 	public function printAction(){}
}