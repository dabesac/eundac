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
        //dataBases
        $facultyDb = new Api_Model_DbTable_Faculty();

        $eid   = $this->sesion->eid;
        $oid   = $this->sesion->oid;
        $rid   = $this->sesion->rid;
        $escid = $this->sesion->escid;
        $subid = $this->sesion->subid;


        $dataView['rid'] = $rid;
        if ($rid == 'RC') {
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
            $dataView['faculties'] = $dataFaculty;
        }elseif ($rid == 'DR') {
            $dataView['id_school'] = base64_encode($escid.'|'.$subid);
        }

        $this->view->dataView = $dataView;
        //print_r($pdFaculties);
	}

    public function listcurriculumsAction(){
        $this->_helper->layout()->disableLayout();

        //DataBases
        $schoolsDb    = new Api_Model_DbTable_Speciality();
        $curriculumDb = new Api_Model_DbTable_Curricula();

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $rid = $this->sesion->rid;

        $dataView['rid'] = $rid;

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
        $curriculumForm = new Curricula_Form_Curricula();
        $dataView['curriculumForm'] = $curriculumForm;

        $this->view->dataView = $dataView;
    }

    public function savenewAction(){
        $this->_helper->layout->disableLayout();
        //DataBases
        $curriculumDb = new Api_Model_DbTable_Curricula();

        //Forms
        $curriculumForm = new Curricula_Form_Curricula();

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
        $rid = $this->sesion->rid;

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
        $dataView['state']  = $state;
        $dataView['rid']    = $rid;
        $dataView['id_cur'] = base64_encode($curid.'|'.
                                            $escid.'|'.
                                            $subid );
        
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
        $rid = $this->sesion->rid;

        $dataView['rid'] = $rid;

        //Datos de la curricula
        $dataView['id'] = base64_encode($curid.'_'.
                                        $escid.'_'.
                                        $subid );

        $dataView['id_alone'] = base64_encode($curid);

        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'curid' => $curid,
                        'escid' => $escid,
                        'subid' => $subid );
        $dataCurriculum = $curriculumDb->_getOne($where);

        //Form
        $curriculumForm = new Curricula_Form_Curricula();
        $dataCurriculum['cur_per_ant'] = base64_encode($dataCurriculum['cur_per_ant']);
        $curriculumForm->populate($dataCurriculum);
        $dataView['curriculumForm'] = $curriculumForm;

        //Bloqueo para directores, esto para que regularicen los creditos...
        if ($rid == 'DR') {
            $curriculumForm->type->setAttrib('disabled', 'true');
            $curriculumForm->name->setAttrib('readonly', 'true');
            $curriculumForm->alias->setAttrib('readonly', 'true');
            $curriculumForm->number_periods->setAttrib('readonly', 'true');
            $curriculumForm->cur_per_ant->setAttrib('disabled', 'true');
        }

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
        $curriculumForm = new Curricula_Form_Curricula();

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $uid = $this->sesion->uid;
        $rid = $this->sesion->rid;

        $formData = $this->getRequest()->getPost();

        //Variables type_periods y year que solo sirven para pasar la validación...
            $formData['type_periods'] = 'A';
            $formData['year']         = '1993';
            if ($rid == 'DR') {
                $formData['type']  = 'S';
                $formData['alias'] = 'nothing';
            }
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

            if ($rid == 'DR') {
                $dataSave = array(  'mandatory_credits' => $formData['mandatory_credits'],
                                    'elective_credits'  => $formData['elective_credits'],
                                    'mandatory_course'  => $formData['mandatory_course'],
                                    'elective_course'   => $formData['elective_course'],
                                    'modified'          => $uid,
                                    'updated'           => date('Y-m-d h:m:s') );               
            }else {
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
                                    'updated'           => date('Y-m-d h:m:s') );
            }

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

    public function admincoursesAction(){
        $this->_helper->layout()->disableLayout();

        //dataBase
        $coursesDb    = new Api_Model_DbTable_Course();
        $curriculumDb = new Api_Model_DbTable_Curricula();

        $ids = explode('_', base64_decode($this->_getParam('id')));
        $curid = $ids[0];
        $escid = $ids[1];
        $subid = $ids[2];

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'curid' => $curid,
                        'escid' => $escid,
                        'subid' => $subid );

        $dataCurriculum = $curriculumDb->_getOne($where);

        $dataView['dataCurriculum'] = $dataCurriculum;

        //Cursos de la Curricula
        $dataView['dataCourses'] = array();

        $attrib = '';
        $order = array('semid ASC');

        $pdCourses   = $coursesDb->_getFilter($where, $attrib, $order);
        $dataCourses = array();

        if ($pdCourses) {
            $semid   = '_';
            $cCursos = 0;
            $cSem    = -1;
            foreach ($pdCourses as $c => $course) {
                if ($course['semid'] != $semid) {
                    $semid   = $course['semid'];
                    $cCursos = 0;
                    $cSem++;
                }else {
                    $dataCourses[$cSem]['courses'][$cCursos] = array(   'idget'             => base64_encode($course['courseid'].'|'.
                                                                                                                $course['curid'].'|'.
                                                                                                                $course['escid'].'|'.
                                                                                                                $course['subid'] ),
                                                                        'id'                => $course['courseid'],
                                                                        'semid'             => $course['semid'],
                                                                        'name'              => $course['name'],
                                                                        'abbreviation'      => $course['abbreviation'],
                                                                        'type'              => $course['type'],
                                                                        'credits'           => $course['credits'],
                                                                        'hours_theoretical' => $course['hours_theoretical'],
                                                                        'hours_practical'   => $course['hours_practical'],
                                                                        'req_1'             => $course['req_1'],
                                                                        'req_2'             => $course['req_2'],
                                                                        'req_3'             => $course['req_3'],
                                                                        'state'             => $course['state'] );

                    if (!$course['req_1']) {
                        $dataCourses[$cSem]['courses'][$cCursos]['req_1'] = '-';
                    }

                    if ($course['type'] == 'O') {
                        $dataCourses[$cSem]['courses'][$cCursos]['name_type'] = 'Obligatorio';
                    }elseif ($course['type'] == 'E'){
                        $dataCourses[$cSem]['courses'][$cCursos]['name_type'] = 'Electivo';
                    }else {
                        $dataCourses[$cSem]['courses'][$cCursos]['name_type'] = 'No Tiene';
                        $dataCourses[$cSem]['courses'][$cCursos]['type'] = 'N';
                    }
                    $dataCourses[$cSem]['semid'] = $course['semid'];
                    $cCursos++;
                }
            }

            //ordernar por codigo
            foreach ($dataCourses as $cSem => $semester) {
                $codigo = array();
                foreach ($semester['courses'] as $c => $course) {
                    $codigo[$c] = $course['id'];
                }
                array_multisort($dataCourses[$cSem]['courses'], SORT_ASC, $codigo);
            }
        }

        $dataView['dataCourses'] = $dataCourses;

        //form para las ediciones
        $dataView['form_course'] = new Curricula_Form_Course();
        for ($i=1; $i<=$semid; $i++) { 
            $dataView['form_course']->semid->addMultiOption($i, $i);
        }

        $this->view->dataView = $dataView;
    }

    public function newcourseAction(){
        $this->_helper->layout()->disableLayout();

        //DataBase
        $curriculumDb = new Api_Model_DbTable_Curricula();
        $courseDb     = new Api_Model_DbTable_Course();

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        $ids = $this->_getParam('id');
        $dataView['id'] = $ids;

        $ids = explode('_', base64_decode($ids));
        $curid = $ids[0];
        $escid = $ids[1];
        $subid = $ids[2];

        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'curid' => $curid,
                        'escid' => $escid,
                        'subid' => $subid );

        $dataCurriculum = $curriculumDb->_getOne($where);

        $dataView['type']    = $dataCurriculum['type'];
        $dataView['periods'] = $dataCurriculum['number_periods'];

        $form_course = new Curricula_Form_Course();
        $dataView['form_course'] = $form_course;

        //All Courses
        $where['state'] = 'A';
        $attrib = array('courseid', 'name', 'semid');
        $order = array('semid ASC', 'courseid ASC');
        $dataCourses = $courseDb->_getFilter($where, $attrib, $order);
        $dataView['data_courses'] = $dataCourses;

        $this->view->dataView = $dataView;
    }

    public function editcourseAction(){
        $this->_helper->layout()->disableLayout();
        
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        //dataBases
        $courseDb     = new Api_Model_DbTable_Course();
        $curriculumDb = new Api_Model_DbTable_Curricula();

        $dataView['id'] = $this->_getParam('id');

        $ids = explode('|', base64_decode($this->_getParam('id')));
        $courseid = $ids[0];
        $curid    = $ids[1];
        $escid    = $ids[2];
        $subid    = $ids[3];

        $where = array(
                        'eid'      => $eid,
                        'oid'      => $oid,
                        'escid'    => $escid,
                        'subid'    => $subid,
                        'curid'    => $curid,
                        'courseid' => $courseid );
        $course_data = $courseDb->_getOne($where);

        $course_form = new Curricula_Form_Course();
        $course_form->populate($course_data);

        //periodos de curricula
        $dataView['exist_periods'] = false;
        $where = array(
                        'eid'   => $eid,
                        'oid'   => $oid,
                        'escid' => $escid,
                        'subid' => $subid,
                        'curid' => $curid );
        $curriculum_data = $curriculumDb->_getOne($where);
        $periods = $curriculum_data['number_periods'];
        if ($periods) {
            $dataView['exist_periods'] = true;
            for ($i=1; $i<=$periods; $i++) { 
                $course_form->semid->addMultiOption($i, $i);
            }
        }

        //prerequisitos
        $where = array(
                        'eid'   => $eid,
                        'oid'   => $oid,
                        'escid' => $escid,
                        'subid' => $subid,
                        'curid' => $curid );
        $attrib = array('courseid', 'semid', 'name');
        $order = array('semid ASC');
        $courses_pre_pd = $courseDb->_getFilter($where, $attrib, $order);

        $courses_pre_data = array();
        $requisites_data  = array();
        if ($courses_pre_pd) {
            $c_req = 0;
            foreach ($courses_pre_pd as $c => $course) {
                if ($course['courseid'] != $courseid) {
                    $courses_pre_data[$c] = array(
                                                    'id'    => base64_encode($course['courseid']),
                                                    'code'  => $course['courseid'],
                                                    'name'  => $course['name'],
                                                    'semid' => $course['semid'] );
                }
                for ($i=1; $i<=3; $i++) { 
                    if ($course_data['req_'.$i] == $course['courseid']) {
                        $requisites_data[$c_req] = array(   
                                                            'id'    => base64_encode($course['courseid']),
                                                            'code'  => $course['courseid'],
                                                            'name'  => $course['name'] );
                        $c_req++;
                    }
                }
            }
        }

        $dataView['requisites_data']  = $requisites_data;
        $dataView['courses_pre_data'] = $courses_pre_data;
        $dataView['course_form']      = $course_form;

        $this->view->dataView = $dataView;
    }

    public function savecourseAction(){
        $this->_helper->layout()->disableLayout();

        //dataBase
        $courseDb = new Api_Model_DbTable_Course();

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $uid = $this->sesion->uid;

        $formData['pre_1'] = null;
        $formData['pre_2'] = null;
        $formData['pre_3'] = null;
        $formData = $this->getRequest()->getPost();

        //form para validar
        $form_course = new Curricula_Form_Course();
        if ($form_course->isValid($formData)) {
            $ids = explode('_', base64_decode($formData['id']));
            $curid = $ids[0];
            $escid = $ids[1];
            $subid = $ids[2];

            $dataSave = array(
                                'eid'               => $eid,
                                'oid'               => $oid,
                                'curid'             => $curid,
                                'escid'             => $escid,
                                'subid'             => $subid,
                                'courseid'          => $formData['courseid'],
                                'credits'           => $formData['credits'],
                                'semid'             => $formData['semid'],
                                'name'              => $formData['name'],
                                'abbreviation'      => $formData['abbreviation'],
                                'type'              => $formData['type'],
                                'hours_theoretical' => $formData['hours_theoretical'],
                                'hours_practical'   => $formData['hours_practical'],
                                'year_course'       => round($formData['semid']/2),
                                'state'             => 'A',
                                'register'          => $uid,
                                'created'           => date('Y-m-d h:i:s') );
            
            //prerequisitos
            for ($i=1; $i<=3 ; $i++) { 
                if ($formData['pre_'.$i]) {
                    $dataSave['req_'.$i] = base64_decode($formData['pre_'.$i]);
                }
            }

            if ($courseDb->_save($dataSave)) {
                $result = array('success'  => 1,
                                'errors'   => array(),
                                'semester' => $formData['semid'] );
            }
        }else{
            $result['success'] = 0;
            $cError = 0;
            foreach ($form_course->getMessages() as $typeError) {
                foreach ($typeError as $error) {
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

    public function saveeditcourseAction() {
        $this->_helper->layout()->disableLayout();

        //database
        $courseDb = new Api_Model_DbTable_Course();

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $uid = $this->sesion->uid;

        $formData = $this->getRequest()->getPost();

        $formData['courseid'] = '666';
        //form
        $course_form = new Curricula_Form_Course();
        if ($course_form->isValid($formData)) {
            $ids = explode('|', base64_decode($formData['id']));
            $courseid = $ids[0];
            $curid    = $ids[1];
            $escid    = $ids[2];
            $subid    = $ids[3];

            $course_pk = array(
                                'eid'      => $eid,
                                'oid'      => $oid,
                                'escid'    => $escid,
                                'subid'    => $subid,
                                'courseid' => $courseid,
                                'curid'    => $curid );

            $course_data_update = array('name'              => $formData['name'],
                                        'abbreviation'      => $formData['abbreviation'],
                                        'type'              => $formData['type'],
                                        'credits'           => $formData['credits'],
                                        'hours_theoretical' => $formData['hours_theoretical'],
                                        'hours_practical'   => $formData['hours_practical'],
                                        'semid'             => $formData['semid'],
                                        'updated'           => date('Y-m-d h:i:s'),
                                        'modified'          => $uid );

            //prerequisitos
            for ($i=1; $i<=3 ; $i++) { 
                if ($formData['pre_'.$i]) {
                    $course_data_update['req_'.$i] = base64_decode($formData['pre_'.$i]);
                }
            }
            if ($courseDb->_update($course_data_update, $course_pk)) {
                $result = array('success' => 1,
                                'semid'   => $formData['semid'] );
            }
        } else {
            $result['success'] = 0;
            $cError = 0;
            foreach ($course_form->getMessages() as $typeError) {
                foreach ($typeError as $error) {
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

    public function printAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            // $uid_update = $this->sesion->uid;
            // $f_update = date("Y-m-d");

            $ids = base64_decode($this->_getParam('id'));
            $ids = explode('|', $ids);
            $curid = $ids[0];
            $escid = $ids[1];
            $subid = $ids[2];
            // $state=base64_decode($this->_getParam('state'));
            $this->view->curid=$curid;

            // $this->view->eid=$eid;
            // $this->view->oid=$oid;
            // $this->view->escid=$escid;
            // $this->view->subid=$subid;
            // $this->view->state=$state;
            $bdcurricula = new Api_Model_DbTable_Curricula();
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['subid']=$subid;
            $where['curid']=$curid;
            $lcurricula=$bdcurricula->_getOne($where);
            $this->view->nombre_curricula=$lcurricula["name"];
            $semestre=$bdcurricula->_getSemesterXCurricula($curid,$subid,$escid,$oid,$eid);

            $bdcursos = new Api_Model_DbTable_Course();
            $where1= array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid);
            $attrib=array('semid','courseid','name','type','credits','hours_theoretical','hours_practical',
                        'req_1','req_2','req_3','course_equivalence','course_equivalence_2');
            $order=array('courseid');
            $i=0;
            foreach ($semestre as $semestre) {
                $where1['semid']=$semestre['semid'];
                $semestres[$i]['semid']=$semestre['semid'];
                $semestres[$i]['name']=$semestre['name'];
                $cursos=$bdcursos->_getFilter($where1,$attrib,$order);
                $semestres[$i]['cursos']=$cursos;
                $i++;
            }
            
            $this->view->datasemestre=$semestres;

            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
            $base_speciality =  new Api_Model_DbTable_Speciality();        
            $speciality = $base_speciality ->_getOne($where);
            $parent=$speciality['parent'];
            $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
            $parentesc= $base_speciality->_getOne($wher);

            if ($parentesc) {
                $pala='ESPECIALIDAD DE ';
                $spe['esc']=$parentesc['name'];
                $spe['parent']=$pala.$speciality['name'];
            }
            else{
                $spe['esc']=$speciality['name'];
                $spe['parent']='';  
            }
            $names=strtoupper($spe['esc']);
            $namep=strtoupper($spe['parent']);
            $namev=$names." ".$namep;
            $this->view->namev=$namev;
            $namefinal=$names." <br> ".$namep;

            $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";
            
            $fac = array('eid'=>$eid,'oid'=>$oid,'facid'=>$speciality['facid']);
            $base_fac =  new Api_Model_DbTable_Faculty();        
            $datafa= $base_fac->_getOne($fac);
            $namef = strtoupper($datafa['name']);  

            $dbimpression = new Api_Model_DbTable_Countimpressionall();
            
            $uid=$this->sesion->uid;
            $uidim=$this->sesion->pid;
            $pid=$uidim;

            $data = array(
                'eid'=>$eid,
                'oid'=>$oid,
                'uid'=>$uid,
                'escid'=>$escid,
                'subid'=>$subid,
                'pid'=>$pid,
                'type_impression'=>'curricula_'.$curid,
                'date_impression'=>date('Y-m-d H:i:s'),
                'pid_print'=>$uidim
                );
            // print_r($data);exit();
            $dbimpression->_save($data);            

            $wheri = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,
                'subid'=>$subid,'type_impression'=>'curricula_'.$curid);
            $dataim = $dbimpression->_getFilter($wheri);
                        
            $co=count($dataim);            
            $codigo=$co." - ".$uidim;

            $header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
            $header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", $namelogo, $header);
            $header = str_replace("?codigo", $codigo, $header);
          
            $this->view->header=$header;

            $this->view->footer=$footer;
        }
        catch (Exception $ex)
        {
            print "Error: Cargar Curriculas".$ex->getMessage();
        }
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
                $form = new Curricula_Form_Curricula();
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