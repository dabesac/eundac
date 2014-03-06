<?php

class Pedagogia_DistributionController extends Zend_Controller_Action {

    public function init(){
		$sesion  = Zend_Auth::getInstance();
      	if(!$sesion->hasIdentity() ){
        	$this->_helper->redirector('index',"index",'default');
      	}
      	$login = $sesion->getStorage()->read();
      	$this->sesion = $login;
	}

    public function indexAction()
    {	
    	//echo "hfh";
    }

    public function viewAction(){	
    	try{
            
            $this->_helper->layout()->disablelayout();
    		$eid=$this->sesion->eid;
    		$oid=$this->sesion->oid;
    		$perid=$this->_getParam('perid');

            $distri= new Distribution_Model_DbTable_Distribution();
            $where=array("eid"=>$eid, "oid"=>$oid,"perid"=>$perid);
            $order=array('escid');
            $dis=$distri->_getFilter($where,$attrib=null,$order);
            $len=count($dis);

            $dbescuela= new Api_Model_DbTable_Speciality();
            $where1=array('eid'=>$eid,'oid'=>$oid);

            for ($i=0; $i < $len; $i++) { 
                $where1['escid']=$dis[$i]['escid'];
                $where1['subid']=$dis[$i]['subid'];
                $datae=$dbescuela->_getOne($where1);
                $dis[$i]['name']=$datae['name'];
            }
            $this->view->dis=$dis;

    	}catch(Exception $ex){
            print "Error: Cargar ".$ex->getMessage();

    	}
    }

    public function distributionAction()
    {
        $this->_helper->layout()->disablelayout();
    	$eid=$this->sesion->eid;
    	$oid=$this->sesion->oid;
    	$perid = $this->_getParam("perid");
    	$escid = $this->_getParam("escid");
    	$distid = $this->_getParam("distid");
    	$subid = $this->_getParam("subid");
    	$obs = $this->_getParam("observation");
    	//$nombre = $this->_getParam("nombre");
    	
    	$distribution= new Distribution_Model_DbTable_Distribution();
    	$where=array("eid"=>$eid, "oid"=>$oid,"perid"=>$perid,"escid"=>$escid,"subid"=>$subid,"distid"=>$distid);
    	$dist=$distribution->_getOne($where);

    	//print_r($dist);

    	$formData['eid']=$eid;
    	$formData['oid']=$oid;
    	$formData['escid']=$escid;
    	$formData['perid']=$perid;
    	$formData['distid']=$distid;
    	$formData['subid']=$subid;
    	$pk =$formData;
    	$formData['observation']=$obs;

    	//print_r($formData);
    	$distr = new Distribution_Model_DbTable_Distribution();
    	$distr->_update($formData,$pk);

    }


}
