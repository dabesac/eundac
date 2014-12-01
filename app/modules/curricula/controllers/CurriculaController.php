<?php
class Curricula_CurriculaController extends Zend_Controller_Action
{
	public function init(){

           $sesion  = Zend_Auth::getInstance();
            if(!$sesion->hasIdentity() ){
                  $this->_helper->redirector('index',"index",'default');
            }
            $login = $sesion->getStorage()->read();
            // if (!$login->rol['module']=="docente"){
            //       $this->_helper->redirector('index','index','default');
            // }
            $this->sesion = $login;
	}

	public function indexAction(){
		$eid=$this->sesion->eid;
        $oid=$this->sesion->oid;

        //dataBases
        $facultyDb = new Api_Model_DbTable_Faculty();

        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'state' => 'A');
        $attrib = array('facid', 'name');
        $pdFaculties = $facultyDb->_getFilter($where, $attrib);

        foreach ($pdFaculties as $c => $faculty) {
            if ($faculty['facid'] != 'TODO') {
                $dataFaculty[$c] = array(
                                            'facid' => base64_encode($faculty['facid']),
                                            'name'  => $faculty['name'] );
            }
        }
        $this->view->dataFaculty = $dataFaculty;
        //print_r($pdFaculties);
	}

    public function listcurriculumsAction(){
        $this->_helper->layout()->disableLayout();

        //DataBases
        $schoolsDb    = new Api_Model_DbTable_Speciality();
        $curriculumDb = new Api_Model_DbTable_Curricula();

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        $dataGet = base64_decode($this->_getParam('id'));
        $dataGet = explode('|', $dataGet);
        $escid = $dataGet[0];
        $subid = $dataGet[1];

        //Ver si tiene especialidades
        $where = array( 'eid'    => $eid,
                        'oid'    => $oid,
                        'parent' => $escid,
                        'subid'  => $subid,
                        'state'  => 'A' );
        $attrib = '';
        $order = array('escid ASC');

        $pdSpecialities = $schoolsDb->_getFilter($where, $attrib, $order);

        $dataView['specialities'] = array();

        if ($pdSpecialities) {
            $letters = strlen($escid);
            $where = array( 'eid'                       => $eid,
                            'oid'                       => $oid,
                            'left(escid, '.$letters.')' => $escid );
            $attrib = '';
            $order = array('escid ASC');
            $pdCurriculums = $curriculumDb->_getFilter($where, $attrib, $order);

            //lista de especialidades
            if ($pdCurriculums) {
                foreach ($pdSpecialities as $cSpe => $speciality) {
                    $dataView['specialities'][$cSpe] = array(   'escid' => $speciality['escid'],
                                                                'subid' => $speciality['subid'],
                                                                'name'  => $speciality['name'] );

                    $dataCurriculums[$cSpe]['active']    = array();
                    $dataCurriculums[$cSpe]['temporary'] = array();
                    $dataCurriculums[$cSpe]['close']     = array();
                    $dataCurriculums[$cSpe]['draft']     = array();

                    $cTemp  = 0;
                    $cClose = 0;
                    $cDraft = 0;
                    foreach ($pdCurriculums as $curriculum) {
                        if ($curriculum['escid'] == $speciality['escid'] and
                            $curriculum['subid'] == $speciality['subid'] ) {
                            $id = base64_encode($curriculum['curid'].'_'.
                                                $curriculum['escid'].'_'.
                                                $curriculum['subid'] );
                            $dataCurriculums[$cSpe]['code'] = $curriculum['escid'].'_'.$curriculum['subid'];
                            if ($curriculum['state'] == 'A') {
                                $dataCurriculums[$cSpe]['active'] = array(  'curid' => $curriculum['curid'],
                                                                            'name'  => $curriculum['name'],
                                                                            'year'  => $curriculum['year'],
                                                                            'escid' => $curriculum['escid'],
                                                                            'subid' => $curriculum['subid'],
                                                                            'id'    => $id );
                            }elseif ($curriculum['state'] == 'T'){
                                $dataCurriculums[$cSpe]['temporary'][$cTemp] = array(   'curid' => $curriculum['curid'],
                                                                                        'name'  => $curriculum['name'],
                                                                                        'year'  => $curriculum['year'],
                                                                                        'escid' => $curriculum['escid'],
                                                                                        'subid' => $curriculum['subid'],
                                                                                        'id'    => $id );
                                $cTemp++;
                            }elseif ($curriculum['state'] == 'C'){
                                $dataCurriculums[$cSpe]['close'][$cClose] = array(  'curid' => $curriculum['curid'],
                                                                                    'name'  => $curriculum['name'],
                                                                                    'year'  => $curriculum['year'],
                                                                                    'escid' => $curriculum['escid'],
                                                                                    'subid' => $curriculum['subid'],
                                                                                    'id'    => $id );
                                $cClose++;
                            }elseif ($curriculum['state'] == 'B'){
                                $dataCurriculums[$cSpe]['draft'][$cDraft] = array(  'curid' => $curriculum['curid'],
                                                                                'name'  => $curriculum['name'],
                                                                                'year'  => $curriculum['year'],
                                                                                'escid' => $curriculum['escid'],
                                                                                'subid' => $curriculum['subid'],
                                                                                'id'    => $id );
                                $cDraft++;
                            }
                        }
                    }
                }
            }
        }else{
            $dataCurriculums[0]['active']    = array();
            $dataCurriculums[0]['temporary'] = array();
            $dataCurriculums[0]['close']     = array();
            $dataCurriculums[0]['draft']     = array();
            $dataCurriculums[0]['code']      = $escid.'_'.$subid;

            $where = array( 'eid'   => $eid,
                            'oid'   => $oid,
                            'escid' => $escid,
                            'subid' => $subid );
            $attrib = '';
            $order = array('year DESC');
            $pdCurriculums = $curriculumDb->_getFilter($where, $attrib, $order);

            if ($pdCurriculums) {
                $cTemp  = 0;
                $cClose = 0;
                $cDraft = 0;
                foreach ($pdCurriculums as $curriculum) {
                    $id = base64_encode($curriculum['curid'].'_'.
                                        $curriculum['escid'].'_'.
                                        $curriculum['subid'] );
                    if ($curriculum['state'] == 'A') {
                        $dataCurriculums[0]['active'] = array(  'curid' => $curriculum['curid'],
                                                                'name'  => $curriculum['name'],
                                                                'year'  => $curriculum['year'],
                                                                'escid' => $curriculum['escid'],
                                                                'id'    => $id );
                    }elseif ($curriculum['state'] == 'T'){
                        $dataCurriculums[0]['temporary'][$cTemp] = array(   'curid' => $curriculum['curid'],
                                                                            'name'  => $curriculum['name'],
                                                                            'year'  => $curriculum['year'],
                                                                            'escid' => $curriculum['escid'],
                                                                            'id'    => $id );
                        $cTemp++;
                    }elseif ($curriculum['state'] == 'C'){
                        $dataCurriculums[0]['close'][$cClose] = array(  'curid' => $curriculum['curid'],
                                                                        'name'  => $curriculum['name'],
                                                                        'year'  => $curriculum['year'],
                                                                        'escid' => $curriculum['escid'],
                                                                        'id'    => $id );
                        $cClose++;
                    }elseif ($curriculum['state'] == 'B'){
                        $dataCurriculums[0]['draft'][$cDraft] = array(  'curid' => $curriculum['curid'],
                                                                        'name'  => $curriculum['name'],
                                                                        'year'  => $curriculum['year'],
                                                                        'escid' => $curriculum['escid'],
                                                                        'id'    => $id );
                        $cDraft++;
                    }
                }
            }
        }
        $dataView['dataCurriculums'] = $dataCurriculums;
        $this->view->dataView = $dataView;
    }

	public function schoolsAction(){
		try {
			$this->_helper->layout()->disableLayout();
            $facid=base64_decode($this->_getParam('facid'));
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $where= array('eid'=>$eid,'oid'=>$oid,'facid'=>$facid,'state'=>'A');
            $attrib=array('escid','name','subid');
            $esc= new Api_Model_DbTable_Speciality();
            $escuelas=$esc->_getFilter($where,$attrib);
            $this->view->escuelas=$escuelas;
            $is_director=$this->sesion->infouser['teacher']['is_director'];
            if ($is_director=="S") {
                $escid = $this->sesion->escid;
                $subid = $this->sesion->subid;
                $this->view->escid = $escid;
                $this->view->subid = $subid;
            }
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function curriculasAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $get=split('--', base64_decode($this->_getParam('escid')));
            $escid=$get[0];
            $subid=$get[1];
            $this->view->escid=$escid;
            $this->view->subid=$subid;
            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
            $curr= new Api_Model_DbTable_Curricula();
            $curriculas=$curr->_getFilter($where);
            $this->view->curriculas=$curriculas;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

    public function newcurriculaAction(){
        $this->_helper->layout()->disableLayout();

        //DataBases
        $curriculumDb = new Api_Model_DbTable_Curricula();
        $facultyDb    = new Api_Model_DbTable_Faculty();
        $specialityDb = new Api_Model_DbTable_Speciality();

        $ids = $this->_getParam('id');
        $ids = explode('_', $ids);
        $escid = $ids[0];
        $subid = $ids[1];

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        //Verificar si es especialidad o escuela
        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'escid' => $escid,
                        'subid' => $subid );
        $attrib = array('parent', 'name', 'facid');
        $pdSpeciality = $specialityDb->_getFilter($where, $attrib);

        if ($pdSpeciality[0]['parent']) {
            $where = array( 'eid'   => $eid,
                            'oid'   => $oid,
                            'escid' => $pdSpeciality[0]['parent'] );
            $attrib = array('name');
            $pdParent = $specialityDb->_getFilter($where, $attrib);
            $dataView = array(  'isEsp'   => 'yes',
                                'espid'   => $escid,
                                'escid'   => $pdSpeciality[0]['parent'],
                                'subid'   => $subid,
                                'nameEsp' => $pdSpeciality[0]['name'],
                                'nameEsc' => $pdParent[0]['name'] );

        }else{
            $dataView = array(  'isEsp'   => 'no',
                                'escid'   => $escid,
                                'subid'   => $subid,
                                'nameEsc' => $pdSpeciality[0]['name'] );
        }
        //nombre de la facultad
        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'facid' => $pdSpeciality[0]['facid'] );
        $attrib = array('name');
        $pdFaculty = $facultyDb->_getFilter($where, $attrib);
        
        $dataView['facid']   = $pdSpeciality[0]['facid'];
        $dataView['nameFac'] = $pdFaculty[0]['name'];

        //Curriculas anteriores
        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'escid' => $escid,
                        'subid' => $subid );
        $attrib = array('curid', 'name');
        $pdCurriculums = $curriculumDb->_getFilter($where, $attrib);

        $dataView['curriculumsBefore'] = array();
        if ($pdCurriculums) {
            foreach ($pdCurriculums as $c => $curriculum) {
                $dataView['curriculumsBefore'][$c] = array( 'id'   => base64_encode($curriculum['curid']),
                                                            'name' => $curriculum['curid'].' '.$curriculum['name'] );
            }
        }

        //Form
        $curriculumForm = new Rcentral_Form_Curricula();
        $dataView['curriculumForm'] = $curriculumForm;

        $this->view->dataView = $dataView;
    }

    public function savenewAction(){
        $this->_helper->layout->disableLayout();
        //DataBases
        $curriculumDb = new Api_Model_DbTable_Curricula();

        //Forms
        $curriculumForm = new Rcentral_Form_Curricula();

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $uid = $this->sesion->uid;

        $formData = $this->getRequest()->getPost();
       
        //print_r($formData);
        if ($curriculumForm->isValid($formData)) {
            $id = explode('|', base64_decode($formData['id']));
            $escid = $id[0];
            $subid = $id[1];
            $yearSub = mb_substr((string)$formData['year'],2,2);
            $curid = $yearSub.$formData['type_periods'].$escid;

            $dataSave = array(  'eid'               => $eid,
                                'oid'               => $oid,
                                'escid'             => $escid,
                                'subid'             => $subid,
                                'curid'             => $curid,
                                'type'              => $formData['type'],
                                'year'              => $formData['year'],
                                'name'              => $formData['name'],
                                'alias'             => $formData['alias'],
                                'number_periods'    => $formData['number_periods'],
                                'mandatory_credits' => $formData['mandatory_credits'],
                                'elective_credits'  => $formData['elective_credits'],
                                'mandatory_course'  => $formData['mandatory_course'],
                                'elective_course'   => $formData['elective_course'],
                                'cur_per_ant'       => base64_decode($formData['cur_per_ant']),
                                'register'          => $uid,
                                'state'             => 'B' );
            if ($curriculumDb->_save($dataSave)) {
                $result = array('success' => 1,
                                'dataNew' => array( 'id'    => base64_encode(   $curid.'_'.
                                                                                $escid.'_'.
                                                                                $subid ),
                                                    'idJs'  =>  $curid.'_'.
                                                                $escid.'_'.
                                                                $subid,
                                                    'curid' => $curid,
                                                    'name'  => $formData['name'],
                                                    'year'  => $formData['year'] ),
                                'errors'  => array(),
                                'cErrors' => 0 );
            }else{
                $result = array('success' => 1,
                                'errors'  => ':( Error en los servidores, intentelo mas tarde...',
                                'cErrors' => 0 );
            }
        }else{
            $result['success'] = 0;
            $cError = 0;
            $error['isEmpty']   = array();
            $error['notDigits'] = array();
            foreach ($curriculumForm->getMessages() as $tipeError) {
                foreach ($tipeError as $error) {
                    $result['errors'][$cError] = $error;
                }
                if ($cError == 2) {
                    break;
                }
                $cError++;
            }
        }

        print json_encode($result);
    }

    public function admincurriculumAction(){
        $this->_helper->layout()->disableLayout();
        //DataBases
        $curriculumDb = new Api_Model_DbTable_Curricula();
        $facultyDb    = new Api_Model_DbTable_Faculty();
        $specialityDb = new Api_Model_DbTable_Speciality();

        $state = $this->_getParam('state');

        $ids = explode('_', base64_decode($this->_getParam('id')));
        $curid = $ids[0];
        $escid = $ids[1];
        $subid = $ids[2];

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        //Verificar si es especialidad o escuela
        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'escid' => $escid,
                        'subid' => $subid );
        $attrib = array('parent', 'name', 'facid');
        $pdSpeciality = $specialityDb->_getFilter($where, $attrib);

        if ($pdSpeciality[0]['parent']) {
            $where = array( 'eid'   => $eid,
                            'oid'   => $oid,
                            'escid' => $pdSpeciality[0]['parent'] );
            $attrib = array('name');
            $pdParent = $specialityDb->_getFilter($where, $attrib);
            $dataView = array(  'isEsp'   => 'yes',
                                'espid'   => $escid,
                                'escid'   => $pdSpeciality[0]['parent'],
                                'subid'   => $subid,
                                'nameEsp' => $pdSpeciality[0]['name'],
                                'nameEsc' => $pdParent[0]['name'] );
        }else{
            $dataView = array(  'isEsp'   => 'no',
                                'escid'   => $escid,
                                'subid'   => $subid,
                                'nameEsc' => $pdSpeciality[0]['name'] );
        }
        //Estado de la curricula
        $dataView['state']   = $state;
        
        //nombre de la facultad
        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'facid' => $pdSpeciality[0]['facid'] );
        $attrib = array('name');
        $pdFaculty = $facultyDb->_getFilter($where, $attrib);
        
        $dataView['facid']   = $pdSpeciality[0]['facid'];
        $dataView['nameFac'] = $pdFaculty[0]['name'];
        
        $this->view->dataView = $dataView;
    }

    public function detailcurriculumAction(){
        $this->_helper->layout()->disableLayout();

        //DataBases
        $curriculumDb = new Api_Model_DbTable_Curricula();

        $ids = explode('_', base64_decode($this->_getParam('id')));
        $curid = $ids[0];
        $escid = $ids[1];
        $subid = $ids[2];

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        //Datos de la curricula
        $dataView['id'] = base64_encode($curid.'|'.
                                        $escid.'|'.
                                        $subid );
        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'curid' => $curid,
                        'escid' => $escid,
                        'subid' => $subid );
        $dataCurriculum = $curriculumDb->_getOne($where);
        $dataView['dataCurriculum']       = $dataCurriculum;
        $dataView['dataCurriculumBefore'] = array();

        //Datos de Curricula Equivalencia si la tuviera
        if ($dataCurriculum['cur_per_ant']) {
            $where = array( 'eid'   => $eid,
                            'oid'   => $oid,
                            'curid' => $dataCurriculum['cur_per_ant'],
                            'escid' => $escid,
                            'subid' => $subid );
            $dataCurriculumBefore = $curriculumDb->_getOne($where);
            $dataView['dataCurriculumBefore'] = $dataCurriculumBefore;
        }
        //print_r($dataCurriculum);

        $this->view->dataView = $dataView;
    }

    public function editcurriculumAction(){
        $this->_helper->layout()->disableLayout();

        //DataBases
        $curriculumDb = new Api_Model_DbTable_Curricula();

        $ids = explode('_', base64_decode($this->_getParam('id')));
        $curid = $ids[0];
        $escid = $ids[1];
        $subid = $ids[2];

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        //Datos de la curricula
        $dataView['id'] = base64_encode($curid.'_'.
                                        $escid.'_'.
                                        $subid );
        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'curid' => $curid,
                        'escid' => $escid,
                        'subid' => $subid );
        $dataCurriculum = $curriculumDb->_getOne($where);

        //Form
        $curriculumForm = new Rcentral_Form_Curricula();
        $dataCurriculum['cur_per_ant'] = base64_encode($dataCurriculum['cur_per_ant']);
        $curriculumForm->populate($dataCurriculum);
        $dataView['curriculumForm'] = $curriculumForm;

        //Curriculas anteriores
        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'escid' => $escid,
                        'subid' => $subid );
        $attrib = array('curid', 'name');
        $pdCurriculums = $curriculumDb->_getFilter($where, $attrib);

        $dataView['curriculumsBefore'] = array();
        if ($pdCurriculums) {
            foreach ($pdCurriculums as $c => $curriculum) {
                $dataView['curriculumsBefore'][$c] = array( 'id'   => base64_encode($curriculum['curid']),
                                                            'name' => $curriculum['curid'].' '.$curriculum['name'] );
            }
        }

        $this->view->dataView = $dataView;
    }

    public function saveeditAction(){
        $this->_helper->layout()->disableLayout();

        //DataBases
        $curriculumDb = new Api_Model_DbTable_Curricula();

        //Forms
        $curriculumForm = new Rcentral_Form_Curricula();

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $uid = $this->sesion->uid;

        $formData = $this->getRequest()->getPost();

        //Variables type_periods y year que solo sirven para pasar la validación...
            $formData['type_periods'] = 'A';
            $formData['year']         = '1993';
        //--------------------------------------------------------------------------
       
        if ($curriculumForm->isValid($formData)) {
            $ids   = explode('_', base64_decode($formData['id']));
            $curid = $ids[0];
            $escid = $ids[1];
            $subid = $ids[2];

            $pk = array('eid'   => $eid,
                        'oid'   => $oid,
                        'curid' => $curid,
                        'escid' => $escid,
                        'subid' => $subid );

            $dataSave = array(  'type'              => $formData['type'],
                                'name'              => $formData['name'],
                                'alias'             => $formData['alias'],
                                'number_periods'    => $formData['number_periods'],
                                'mandatory_credits' => $formData['mandatory_credits'],
                                'elective_credits'  => $formData['elective_credits'],
                                'mandatory_course'  => $formData['mandatory_course'],
                                'elective_course'   => $formData['elective_course'],
                                'cur_per_ant'       => base64_decode($formData['cur_per_ant']),
                                'modified'          => $uid,
                                'updated'           => date('Y-m-d h:m:s'),
                                'state'             => 'B' );

            if ($curriculumDb->_update($dataSave, $pk)) {
                $result = array('success' => 1,
                                'errors'  => array(),
                                'cErrors' => 0 );
            }else{
                $result = array('success' => 1,
                                'errors'  => ':( Error en los servidores, intentelo mas tarde...',
                                'cErrors' => 0 );
            }
        }else{
            $result['success'] = 0;
            $cError = 0;
            $error['isEmpty']   = array();
            $error['notDigits'] = array();
            foreach ($curriculumForm->getMessages() as $tipeError) {
                foreach ($tipeError as $error) {
                    $result['errors'][$cError] = $error;
                }
                if ($cError == 2) {
                    break;
                }
                $cError++;
            }
        }

        print json_encode($result);
    }

    public function icallyouAction(){
        $this->_helper->layout()->disableLayout();

        //DataBase
        $curriculumDb = new Api_Model_DbTable_Curricula();

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $uid = $this->sesion->uid;

        $ids = explode('_', base64_decode($this->_getParam('id')));
        $why = $this->_getParam('why');

        $curid = $ids[0];
        $escid = $ids[1];
        $subid = $ids[2];

        $pk = array(    'eid'   => $eid,
                        'oid'   => $oid,
                        'curid' => $curid,
                        'escid' => $escid,
                        'subid' => $subid );

        if ($why != 'D') {
            $data_update = array('state'    => $why,
                                'modified' => $uid,
                                'updated'  => date('Y-m-d h:m:s') );

            if ($why != 'A') {
                if ($curriculumDb->_update($data_update, $pk)) {
                    $result = array('success' => 1);
                }
            }else{
                //buscar si hay una curricula activa para reemplazarla
                $where = array( 'eid'   => $eid,
                                'oid'   => $oid,
                                'escid' => $escid,
                                'subid' => $subid,
                                'state' => 'A' );
                $attrib = array('curid');
                $pdCurriculum = $curriculumDb->_getFilter($where, $attrib);
                if ($pdCurriculum) {
                    $curid_active = $pdCurriculum[0]['curid'];
                    $pk_cur_active = array( 'eid'   => $eid,
                                            'oid'   => $oid,
                                            'curid' => $curid_active,
                                            'escid' => $escid,
                                            'subid' => $subid );
                    $data_update_active = array('state'    => 'T',
                                                'modified' => $uid,
                                                'updated'  => date('Y-m-d h:m:s'));

                    if ($curriculumDb->_update($data_update, $pk)) {
                        if ($curriculumDb->_update($data_update_active, $pk_cur_active)) {
                            $result = array('success' => 1);
                        }
                    }
                }else{
                    if ($curriculumDb->_update($data_update, $pk)) {
                        $result = array('success' => 1);
                    }
                }
            }
        }else{
            //borra nada mas la wea esta
            if ($curriculumDb->_delete($pk)) {
                $result = array('success' => 1);
            }
            
        }
        print json_encode($result);
    }

	public function modifycurriculaAction(){
		try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $curid = base64_decode($this->_getParam('curid'));
            $escid = base64_decode($this->_getParam('escid'));
            $subid = base64_decode($this->_getParam('subid'));
            $accion = base64_decode($this->_getParam('opti'));
            $where = array('eid'=>$eid, 'oid'=>$oid, 'escid'=>$escid,
                        'curid'=>$curid, 'subid'=>$subid);
            $curr= new Api_Model_DbTable_Curricula();
            $curricula=$curr->_getOne($where);

            if ($accion=="V") {
                $this->view->option=$accion;
                $this->view->curricula=$curricula;
            }else{
                $form = new Rcentral_Form_Curricula();
                $form->year->setAttrib("disabled",'disabled');
                if ($this->getRequest()->isPost()) {
                    $formData = $this->getRequest()->getPost();
                    if ($form->isValid($formData)) {
                        $pk=array('eid'=>$this->sesion->eid, 'oid'=>$this->sesion->oid,
                                'escid'=>$formData['escid_cur'], 'curid'=>$formData['curid'],
                                'subid'=>$formData['subid']);
                        $formData['updated']=date('Y-m-d h:m:s');
                        $formData['modified']=$this->sesion->uid;
                        unset($formData['escid_cur']);
                        $base_curricula = new Api_Model_DbTable_Curricula();
                        if ($base_curricula->_update($formData,$pk)) {
                            $this->view->escid=$pk['escid'];
                            $this->view->subid=$pk['subid'];
                            $this->view->act=1;
                        }
                    }else{
                        $form->populate($formData);
                        $this->view->form=$form;
                    }
                }else{
                    $form->escid_cur->setvalue($curricula['escid']);
                    $form->populate($curricula);
                    $this->view->form=$form;
                }
            }
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}
      public function listcoursesAction(){
            try {
                $eid=$this->sesion->eid;
                $oid=$this->sesion->oid;

                $ids    = explode('_', base64_decode($this->_getParam('id')));
                $curid  = $ids[0];
                $escid  = $ids[1];
                $subid  = $ids[2];
                $action = 'A';

                $this->view->action=$action;
                $this->view->eid=$eid;
                $this->view->oid=$oid;
                $this->view->escid=$escid;
                $this->view->curid=$curid;
                $this->view->subid=$subid;
                $where=array('eid'=>$eid,'oid'=>$oid,
                              'curid'=>$curid,'escid'=>$escid,
                              'subid'=>$subid);
                $base_course= new Api_Model_DbTable_Curricula();
                $data_course = $base_course->_getAmountCourses($curid,$subid,$escid,$oid,$eid);
                $this->view->data_course=$data_course;
            } catch (Exception $e) {
                  print "Error: listcourses ".$e->getMessage();
            }
      }

      public function addcoursesAction(){
            try {
                $this->_helper->layout()->disableLayout();
                $eid=$this->sesion->eid;
                $oid=$this->sesion->oid;
                $curid=$this->_getParam('curid');
                $escid=$this->_getParam('escid');
                $subid=$this->_getParam('subid');
                $this->view->escid=$escid;
                $this->view->curid=$curid;
                $this->view->subid=$subid;
                if ($escid) {
                    $base_cur= new Api_Model_DbTable_Curricula();
                    $curant=$base_cur->_getCurriculaAnterior($curid,$escid);
                    $wherecour=array('eid'=>$eid,'oid'=>$oid,
                                  'curid'=>$curant[0]['curricula_ant'],'escid'=>$escid,
                                  'subid'=>$subid, 'state'=>"A");
                    $course = new Api_Model_DbTable_Course();
                    $datacourses_ant=$course->_getFilter($wherecour,$attrib=null,$orders=array('semid','courseid asc'));
                    $wherecour['curid']=$curid;
                    $datacourses=$course->_getFilter($wherecour,$attrib=null,$orders=array('semid','courseid asc'));
                }
                $form = new Rcentral_Form_Course();
                foreach ($datacourses_ant as $datacourses_ant) {
                    $form->course_equivalence->addMultiOption($datacourses_ant['courseid'],$datacourses_ant['courseid']." | ".$datacourses_ant['name']);
                    $form->course_equivalence_2->addMultiOption($datacourses_ant['courseid'],$datacourses_ant['courseid']." | ".$datacourses_ant['name']);
                }
                foreach ($datacourses as $datacourses) {
                    $form->req_1->addMultiOption($datacourses['courseid'],$datacourses['courseid']." | ".$datacourses['name']);
                    $form->req_2->addMultiOption($datacourses['courseid'],$datacourses['courseid']." | ".$datacourses['name']);
                    $form->req_3->addMultiOption($datacourses['courseid'],$datacourses['courseid']." | ".$datacourses['name']);
                }
                if ($this->getRequest()->isPost()) {
                    $formData = $this->getRequest()->getPost();
                    if ($form->isValid($formData)) {
                        $formData['eid']=$eid;
                        $formData['oid']=$oid;
                        $formData['created']=date('Y-m-d h:m:s');
                        $formData['register']=$this->sesion->uid;
                        $base_course = new Api_Model_DbTable_Course();
                        $formData['req_1'] = (!empty($formData['req_1']))?$formData['req_1']:null;
                        $formData['req_2'] = (!empty($formData['req_2']))?$formData['req_2']:null;
                        $formData['req_3'] = (!empty($formData['req_3']))?$formData['req_3']:null;
                        $formData['course_equivalence'] = (!empty($formData['course_equivalence']))?$formData['course_equivalence']:null;
                        $formData['course_equivalence_2'] = (!empty($formData['course_equivalence_2']))?$formData['course_equivalence_2']:null;
                        $base_course->_save($formData);
                        $this->view->msg=1;
                    }else{
                        $this->view->form=$form;
                    }
                }else{
                    $this->view->form=$form;
                }
            } catch (Exception $e) {
                  print "Error : in addcourse".$e->getMessage();
            }
      }

      public function modifycoursesAction(){
        try {
                $this->_helper->layout()->disableLayout();
                $eid=$this->sesion->eid;
                $oid=$this->sesion->oid;
                $curid=$this->_getParam('curid');
                $escid=$this->_getParam('escid');
                $subid=$this->_getParam('subid');
                $courseid=$this->_getParam('courseid');
                $this->view->escid=$escid;
                $this->view->curid=$curid;
                $this->view->subid=$subid;
                $this->view->courseid=$courseid;
                if ($courseid) {
                    $base_cur= new Api_Model_DbTable_Curricula();
                    $curant=$base_cur->_getCurriculaAnterior($curid,$escid);

                    if (!$curant[0]['curricula_ant']) {
                        $dbschool = new Api_Model_DbTable_Speciality();
                        $dataschool= $dbschool->_getSchoolnewFaculty($where=array('escid'=>$escid));

                        $curant=$base_cur->_getCurriculaAnterior($curid,$dataschool[0]['escid']);
                        $wherecour=array('eid'=>$eid,'oid'=>$oid,
                                  'curid'=>$curant[0]['curricula_ant'],'escid'=>$dataschool[0]['escid'],
                                  'subid'=>$subid, 'state'=>"A");

                        $course = new Api_Model_DbTable_Course();
                        $wherecourse=array('eid'=>$eid,'oid'=>$oid,
                                      'curid'=>$curid,'escid'=>$escid,
                                      'subid'=>$subid,'courseid'=>$courseid);
                        $coursedata=$course->_getOne($wherecourse);

                        $datacourses_ant=$course->_getFilter($wherecour,$attrib=null,$orders=array('semid','courseid asc'));
                        $wherecour['curid']=$curid;
                        $wherecour['escid']=$escid;
                        $datacourses=$course->_getFilter($wherecour,$attrib=null,$orders=array('semid','courseid asc'));
                    }
                    else{
                        $wherecour=array('eid'=>$eid,'oid'=>$oid,
                                  'curid'=>$curant[0]['curricula_ant'],'escid'=>$escid,
                                  'subid'=>$subid, 'state'=>"A");

                        $course = new Api_Model_DbTable_Course();
                        $wherecourse=array('eid'=>$eid,'oid'=>$oid,
                                      'curid'=>$curid,'escid'=>$escid,
                                      'subid'=>$subid,'courseid'=>$courseid);
                        $coursedata=$course->_getOne($wherecourse);

                        $datacourses_ant=$course->_getFilter($wherecour,$attrib=null,$orders=array('semid','courseid asc'));
                        $wherecour['curid']=$curid;
                        $datacourses=$course->_getFilter($wherecour,$attrib=null,$orders=array('semid','courseid asc'));
                    }
                }
                $form= new Rcentral_Form_Course();
                if ($datacourses_ant) {
                    foreach ($datacourses_ant as $datacourses_ant) {
                        $form->course_equivalence->addMultiOption($datacourses_ant['courseid'],$datacourses_ant['courseid']." | ".$datacourses_ant['name']);
                        $form->course_equivalence_2->addMultiOption($datacourses_ant['courseid'],$datacourses_ant['courseid']." | ".$datacourses_ant['name']);
                    }                    
                }
                else{
                    $form->course_equivalence->addMultiOption('','No tiene currícula anterior');
                    $form->course_equivalence_2->addMultiOption('','No tiene currícula anterior');
                    $this->view->clave='1';
                }
                if ($datacourses) {
                    foreach ($datacourses as $datacourses) {
                        $form->req_1->addMultiOption($datacourses['courseid'],$datacourses['courseid']." | ".$datacourses['name']);
                        $form->req_2->addMultiOption($datacourses['courseid'],$datacourses['courseid']." | ".$datacourses['name']);
                        $form->req_3->addMultiOption($datacourses['courseid'],$datacourses['courseid']." | ".$datacourses['name']);
                    }
                }
                
                $form->courseid->setAttrib('readonly','readonly');
                if ($this->getRequest()->isPost()) {
                    $formData = $this->getRequest()->getPost();
                    if ($form->isValid($formData)) {
                        $pk=array('eid'=>$this->sesion->eid, 'oid'=>$this->sesion->oid,
                                'escid'=>$formData['escid'], 'curid'=>$formData['curid'],
                                'subid'=>$formData['subid'], 'courseid'=>$formData['courseid']);
                        $formData['updated']=date('Y-m-d h:m:s');
                        $formData['modified']=$this->sesion->uid;
                        $base_cour = new Api_Model_DbTable_Course();
                        if ($base_cour->_update($formData,$pk)) {
                            $this->view->msg=1;
                        }
                    }else{
                        $form->populate($formData);
                        $this->view->form=$form;
                    }
                }else{
                    $form->populate($coursedata);
                    $this->view->form=$form;
                }
        } catch (Exception $e) {
              print "Error : in modifycourse".$e->getMessage();
        }
      }
}