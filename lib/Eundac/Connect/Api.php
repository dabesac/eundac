<?php 
  /*
    Conexion an configiracion al web Service
  */
class Eundac_Connect_Api extends Zend_Rest_Client{
  /**
  **@param auth ***
  ** user password
  **/
  protected $_user = 's1st3m4s';
  protected $_password = 'und4c';
  protected $_auth = false;
  /**
  **
  **@param sever Zend_Rest_Client ***
  ** user password
  **/



	const API_HOST_SERVER ="http://api.undac.edu.pe:8080/";
  //const API_HOST_SERVER ="http://172.16.0.210:8080/";
	//const API_HOST_SERVER ="http://localhost:8080/";


  protected $_params = array(); 
  protected $_model = null;
  protected $_url= null;

    public function __construct($model,$params){
      $this->setUri(self::API_HOST_SERVER);
      $this->setConfig(array("timeout" => 180000));
      $this->_params=$params;
      $this->_model=$model;
    }

    public function connectAuth(){
      $client = $this->getHttpClient();
      $this->_url="/".base64_encode($this->_user)."/".base64_encode($this->_password).'/'.$this->_model;
      $response = $this->restget($this->_url,$this->_params);
      $data = Zend_Json::decode($response->getBody());
      return $data;
    }

    /*$base_url = 'http://172.16.0.110:8080/';
          $endpoint = '/'.base64_encode('s1st3m4s').'/'.base64_encode('und4c').'/standares_acredit';
        $data = array(
          'eid' => base64_encode($this->sesion->eid),
          'oid' =>base64_encode($this->sesion->oid),
            'escid' => base64_encode($this->sesion->escid),
        );
        // print_r($data);
        $client = new Zend_Rest_Client($base_url);
        $httpClient = $client->getHttpClient();
        $httpClient->setConfig(array("timeout" => 1800));
        $response = $client->restget($endpoint,$data);
        $lista= $response->getBody();
        $data = Zend_Json::decode($lista);
        print_r($data);/*

      /*$server = new Zend_XmlRpc_Client('http://erp.undac.edu.pe:8069/xmlrpc/common');
    $client = $server->getProxy();
    try {
      $database = 'acreditacion';
      $user = 'admin';
      $password = 'sistemas';
      $auth = $client->login($database,$user,$password);
        $object = new Zend_XmlRpc_Client('http://erp.undac.edu.pe:8069/xmlrpc/');
        $estandar = $object->getProxy();
        //$data = $estandar->execute($database,$user,$password,'search','ac.estandar.school',array('escid'=>'4SI'));

    } catch (Zend_XmlRpc_Client_FaultException $e) {
      print "error ".$e->getMassage();
    }*/

}
