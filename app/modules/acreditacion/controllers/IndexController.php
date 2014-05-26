<?php

class Acreditacion_IndexController extends Zend_Controller_Action {

    public function init()
    {
       $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
    }

    public function indexAction()
    {  
        $eid = $this->sesion->eid; 
        $oid = $this->sesion->oid; 
        $escid = $this->sesion->escid; 
        $perid = $this->sesion->period->perid; 
        $anio = date('Y'); 
        $this->view->anio = $anio;

    }

    public function listperiodsAction(){
        $this->_helper->layout()->disableLayout();
        //DataBases
        $periodsDb = new Api_Model_DbTable_Periods();
        $eid  = $this->sesion->eid;
        $oid  = $this->sesion->oid;
        $anio = $this->_getParam('anio');
        $anio = substr($anio, 2, 4);

        $where = array( 'eid'  => $eid,
                        'oid'  => $oid,
                        'year' => $anio);

        $periods = $periodsDb->_getPeriodsxYears($where);
        $c = 0;
        foreach ($periods as $period) {
            $periodFilter = substr($period['perid'], 2, 1);
            if ($periodFilter == 'A' or $periodFilter == 'B') {
                $periodsFilter[$c]['perid'] = $period['perid'];
                $periodsFilter[$c]['name'] = $period['name'];
            }
            $c++;
        }
        $this->view->periods = $periodsFilter;

    }

    public function listestandaresAction(){
        $this->_helper->layout()->disableLayout();
        //DataBases
        $eid   = $this->sesion->eid; 
        $oid   = $this->sesion->oid; 
        $escid = $this->sesion->escid;
        $perid = $this->_getParam('perid');
        $anio  = $this->_getParam('anio');

        $request = array(   'eid'   => base64_encode($eid),
                            'oid'   => base64_encode($oid),
                            'escid' => base64_encode($escid),
                            'anio'  => base64_encode($anio),
                            'perid' => base64_encode($perid) );

        require_once 'Zend/Loader.php';
        Zend_Loader::loadClass('Zend_Rest_Client');

        $base_url = 'http://api.undac.edu.pe:8080/';
        //$base_url = 'http://172.16.0.210:8080/';
        $endpoint = '/'.base64_encode('s3lf.040c0c030$0$0').'/'.base64_encode('__999c0n$um3r999__').'/liststandares_acredit';
        $client = new Zend_Rest_Client($base_url);
        $httpClient = $client->getHttpClient();
        $httpClient->setConfig(array("timeout" => 30000));
        $response = $client->restget($endpoint,$request);
        $lista=$response->getBody();
        if ($lista){
            $estandares = Zend_Json::decode($lista);
            $this->view->estandares = $estandares;
        }
        //print_r($estandares);
    }
}
