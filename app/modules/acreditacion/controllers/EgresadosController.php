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

        $server = new Eundac_Connect_openerp();

		$anio  = $this->_getParam('anio');
		$escid = $this->sesion->escid;
		$subid = $this->sesion->subid;
		$eid   = $this->sesion->eid;
		$oid   = $this->sesion->oid;

		$this->view->anio = $anio;

		$userDb       = new Api_Model_DbTable_Users();
		$specialityDb = new Api_Model_DbTable_Speciality();

    	$preDataEgresados = $userDb->_getEgresadosxAnioxEscuela($escid, $anio);

        //Cantidad de Egresados
        $numEgresados = count($preDataEgresados);
        $this->view->numEgresados = $numEgresados;
        if ($preDataEgresados) {
            foreach ($preDataEgresados as $c => $egresado) {
                $dataEgresados[$c]['uid']      = $egresado['uid'];
                $dataEgresados[$c]['pid']      = $egresado['pid'];
                $dataEgresados[$c]['dni']      = $egresado['numdoc'];
                $dataEgresados[$c]['fullName'] = $egresado['nombre'];

                //DataEscuela
                $where = array( 'eid'   => $eid,
                                'oid'   => $oid,
                                'escid' => $egresado['escid'],
                                'subid' => $subid );
                $attrib = array('name');
                $specialityName = $specialityDb->_getFilter($where, $attrib);

                $dataEgresados[$c]['escid']       = $egresado['escid'];
                $dataEgresados[$c]['nameEscuela'] = $specialityName[0]['name'];

                //Datos de Titulo
                $query = array(
                              array('column'   => 'code_registered',
                                    'operator' => '=',
                                    'value'    =>  $egresado['uid'],
                                    'type'     => 'string' )
                            );

                $idTitleGrade  = $server->search('grade.title.student', $query);
                if ($idTitleGrade) {
                    $attributes     = array('type_document', 'f_resol_general');
                    $dataTitleGrade = $server->read($idTitleGrade, $attributes, 'grade.title.student');

                    $dataEgresados[$c]['grados'][1]['grado'] = '';
                    $dataEgresados[$c]['grados'][2]['grado'] = '';
                    $dataEgresados[$c]['fechaBachiller'] = '';
                    $dataEgresados[$c]['fechaTitulo'] = '';
                    foreach ($dataTitleGrade as $title) {
                        if ($title['type_document'] == 'BC' and $title['f_resol_general']) {
                            $dataEgresados[$c]['grados'][1]['grado'] = 'Bachiller';
                            $dataEgresados[$c]['fechaBachiller']     = $title['f_resol_general'];
                        }elseif ($title['type_document'] == 'T' and $title['f_resol_general']) {
                            $dataEgresados[$c]['grados'][2]['grado'] = 'Titulado';
                            $dataEgresados[$c]['fechaTitulo']        = $title['f_resol_general'];
                        }
                    }
                }else{
                    $dataEgresados[$c]['grados'] = '';
                }
            }
            $this->view->dataEgresados = $dataEgresados;
        }
    }
}