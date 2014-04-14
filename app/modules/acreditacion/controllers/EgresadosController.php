
<?php

class Acreditacion_EgresadosController extends Zend_Controller_Action {

    public function init()
    {
       $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
    }

    public function indexAction(){	
    	$specialityDb = new Api_Model_DbTable_Speciality();

    	$currentYear = date('Y');
    	$this->view->currentYear = $currentYear;

    	$escid = $this->sesion->escid;
		$subid = $this->sesion->subid;
		$eid   = $this->sesion->eid;
		$oid   = $this->sesion->oid;

    	$where = array(	'eid'   => $eid,
						'oid'   => $oid,
						'escid' => $escid,
						'subid' => $subid );
		$attrib = array('name');
		$specialityName = $specialityDb->_getFilter($where, $attrib);
		$this->view->specialityName = $specialityName[0]['name'];
    }

    public function listegresadosAction(){
    	$this->_helper->layout()->disableLayout();

		$anio  = $this->_getParam('anio');
		$escid = $this->sesion->escid;
		$subid = $this->sesion->subid;
		$eid   = $this->sesion->eid;
		$oid   = $this->sesion->oid;

		$this->view->anio = $anio;

		$userDb       = new Api_Model_DbTable_Users();
		$specialityDb = new Api_Model_DbTable_Speciality();

    	$egresados = $userDb->_getEgresadosxAnioxEscuela($escid, $anio);
    	$this->view->egresados = $egresados;

    	$numEgresados = count($egresados);
    	$this->view->numEgresados = $numEgresados;

    	$c = 0;
    	foreach ($egresados as $egresado) {
			$where = array(	'eid'   => $eid,
							'oid'   => $oid,
							'escid' => $egresado['escid'],
							'subid' => $subid );
			$attrib = array('name');
			$specialityName[$c] = $specialityDb->_getFilter($where, $attrib);
			$c++;
    	}

		$this->view->specialityName = $specialityName;

    }
}