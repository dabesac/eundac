<?php

class SendmailController extends Zend_Controller_Action{

	public function init(){
		$sesion  = Zend_Auth::getInstance();
      	if(!$sesion->hasIdentity() ){
        	$this->_helper->redirector('index',"index",'default');
      	}
      	$login = $sesion->getStorage()->read();
      	$this->sesion = $login;
	}

	public function indexAction(){
		try {
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;           
            $where = array('eid' => $eid, 'oid' => $oid, 'state' => 'A');
            $fac= new Api_Model_DbTable_Faculty();
            $facultad=$fac->_getFilter($where,$attrib=null,$orders=null);
            // print_r($facultad);
            $this->view->facultades=$facultad;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

            public function enviarAction(){
            try {
                  $this->_helper->layout()->disableLayout();
                  $de = $this->_getParam('de');
                  $para = $this->_getParam('para');
                  $asunto = $this->_getParam('asunto');
                  $contenido = $this->_getParam('contenido');  
                  $this->view->para=$para;
                  $this->view->asunto=$asunto;
                  $this->view->contenido=$contenido;
              }
                  catch (Exception $e) {
                  print "Error: ".$e->getMessage();
            }
      }

          public function schoolsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $facid = $this->_getParam('facid');
            if ($facid=="TODO") $this->view->facid=$facid;
            else{
                $where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid, 'state' => 'A');
                $es = new Api_Model_DbTable_Speciality();
                $escu = $es->_getFilter($where);
                $this->view->escuelas=$escu;
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

	
}