<?php

class GlobalController extends Zend_Controller_Action {

    public function init()
    {
        $sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		$this->sesion = $login;
    }

    public function listschoolsAction(){
    	$this->_helper->layout()->disableLayout();

    	//DataBases
    	$schoolDb = new Api_Model_DbTable_Speciality();
    	
    	$facid = base64_decode($this->_getParam('id'));

    	$eid = $this->sesion->eid;
    	$oid = $this->sesion->oid;
    	$where = array(	'eid'    => $eid,
						'oid'    => $oid,
						'facid'  => $facid,
						'state'  => 'A',
						'parent' => '' );
    	$attrib = array('escid', 'name', 'subid');
    	$preDataSchool = $schoolDb->_getFilter($where, $attrib);
    	if ($preDataSchool) {
	    	foreach ($preDataSchool as $c => $school) {
	    		$dataSchool[$c]['escid'] = $school['escid'];
	    		$dataSchool[$c]['subid'] = $school['subid'];
	    		$dataSchool[$c]['name']  = $school['name'];
	    	}
	    	$this->view->dataSchool = $dataSchool;
    	}
    }

}