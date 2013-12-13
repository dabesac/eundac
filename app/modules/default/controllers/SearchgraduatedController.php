<?php 
class SearchgraduatedController extends Zend_Controller_Action
{

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
	 //print_r($this->sesion);
	$eid= $this->sesion->eid;
	$oid= $this->sesion->oid;
	//echo $eid;
	//echo $oid;
	
	$company= new Api_Model_DbTable_Jobs();
	$com=$company->_getCompanyXDistinct($eid,$oid);
	//print_r($com);
	$this->view->companias=$com;

	}


	public function listreportAction()
	{	
      $this->_helper->layout()->disableLayout();

	  $cursoid=$this->_getParam('compania');
	  $mil=$this->_getParam('mil');
	  $mil2=$this->_getParam('mil2');
	  $mil3=$this->_getParam('mil3');
	  
	  // echo $mil;
	  // echo $mil2;
	  // echo $mil3;
	  //print_r($cursoid);break;

	  foreach ($cursoid as $cur) 
	  {
	  	$comp = new Api_Model_DbTable_Jobs();
		$c=$comp->_getBuscarxSeleccion($cur,$mil,$mil2,$mil3);

		$co[]=$c;
	  }

	  	$this->view->companias=$co;
	 	//print_r($co);
	}

}