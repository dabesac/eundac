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
		$module=$this->sesion->rol['module'];
		$this->view->module->$module;
		$anio='2014';
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
		$perid='14A';
		$data = array(
			'fecha' => base64_encode($fecha),
			'turno' =>base64_encode($turno),
			'perid' =>base64_encode($perid));
        $server = new Eundac_Connect_Api('up_receipt',$data);
        $subject = $server->connectAuth();
    } 
	catch (Exception $ex)
		{
			print "Error en listar los recibos bancos: ".$ex->getMessage();
		}
}




}