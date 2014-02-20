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
	const API_HOST_SERVER ="http://172.16.0.110:8080/";
	protected $_params = array(); 
	protected $_model = null;
	protected $_url= null;

    public function __construct($model,$params){
    	$this->setUri(self::API_HOST_SERVER);
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

}