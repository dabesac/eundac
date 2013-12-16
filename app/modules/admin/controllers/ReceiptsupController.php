<?php
class Admin_ReceiptsupController extends Zend_Controller_Action {
public function init() {
	$sesion1  = Zend_Auth::getInstance();      
        if($sesion1->getIdentity()){        
            $sesion = $sesion1->getStorage()->read(); 
        }  
        $this->sesion=$sesion;
	require_once 'Zend/Loader.php';
  Zend_Loader::loadClass('Zend_Rest_Client');
}

public function indexAction() {
$this->_helper->redirector("recordreceipts");
}
public function recordreceiptsAction()
{
	try{
		$anio='2013';
		$recibo = new Api_Model_DbTable_Bankreceipts();
		$listar = $recibo->_getbankreceiptsXAnio($anio);
		$this->view->listarrecibos=$listar;
		} 
	catch (Exception $ex)
		{
			print "Error en listar los recibos bancos: ".$ex->getMessage();
		}
}

public function loadreceiptsAction()
{
	try{
		$this->_helper->layout()->disableLayout();
		$fecha = $this->_getParam("fecha");
		$turno = $this->_getParam("turno");
		$perid='13D';
		require_once 'Zend/Loader.php';
        Zend_Loader::loadClass('Zend_Rest_Client');
        $base_url = 'http://localhost:8080/';
        $endpoint = '/s1st3m4s/und4c/up_receipt';
		$data = array('fecha' => $fecha,'turno' =>$turno,'perid' =>$perid);
        $client = new Zend_Rest_Client($base_url);
        $httpClient = $client->getHttpClient();
        $httpClient->setConfig(array("timeout" => 680));
        $response = $client->restget($endpoint,$data);
    } 
	catch (Exception $ex)
		{
			print "Error en listar los recibos bancos: ".$ex->getMessage();
		}
}




}