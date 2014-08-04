<?php

class Syllabus_BibliotecaController extends Zend_Controller_Action {

    public function init()
    {
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        /*if (!$login->modulo=="biblioteca"){
            $this->_helper->redirector('index','index','default');
        }*/
        $this->sesion = $login;
    }
    public function indexAction()
    {
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        //dataBases
        $facultyDb = new Api_Model_DbTable_Faculty();

        $where = array( 
                        'eid'   => $eid,
                        'oid'   => $oid,
                        'state' => 'A' );

        $preDataFaculties = $facultyDb->_getFilter($where);
        foreach ($preDataFaculties as $c => $faculty) {
            if ($faculty['facid'] != 'TODO') {
                $dataFaculty[$c]['facid'] = $faculty['facid'];
                $dataFaculty[$c]['name']  = $faculty['name'];
            }
        }
        $this->view->dataFaculty = $dataFaculty;
    } 

    public function periodsAction(){
        $this->_helper->layout()->disableLayout();

        //dataBases
        $periodDb = new Api_Model_DbTable_Periods();

        $anio = base64_decode($this->_getParam('anio'));
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        $where = array(
                        'eid'  => $eid,
                        'oid'  => $oid,
                        'year' => substr($anio, 2, 4) );
        $periods = $periodDb->_getPeriodsxYears($where);
        $this->view->periods = $periods;
    }

    public function listschoolsAction(){
        $this->_helper->layout()->disableLayout();

        //dataBases
        $schoolDb = new Api_Model_DbTable_Speciality();

        $facid = base64_decode($this->_getParam('facid'));
        $eid   = $this->sesion->eid;        
        $oid   = $this->sesion->oid;        

        $where = array( 'eid'    => $eid,
                        'oid'    => $oid,
                        'facid'  => $facid,
                        'parent' => '',
                        'state'  => 'A' );

        $attrib = array('escid', 'name', 'subid');

        $dataEscuelas = $schoolDb->_getFilter($where, $attrib);

        $this->view->dataEscuelas = $dataEscuelas;
    }

    public function listspecialitiesAction(){
        $this->_helper->layout()->disableLayout();

        //dataBases
        $specialityDb = new Api_Model_DbTable_Speciality();

        $escid = base64_decode($this->_getParam('escid'));
        $escidExplode = explode('-', $escid);
        $eid   = $this->sesion->eid;
        $oid   = $this->sesion->oid;
        
        $where = array( 'eid'    => $eid,
                        'oid'    => $oid,
                        'parent' => $escidExplode[0],
                        'state'  => 'A' );

        $attrib = array('escid', 'name', 'subid');

        $dataEspecialidades = $specialityDb->_getFilter($where, $attrib);

        $this->view->dataEspecialidades = $dataEspecialidades;
    }

    public function listarcursosAction(){
        $this->_helper->layout()->disableLayout();
        //DataBases
        $courseDb  = new Api_Model_DbTable_Course();
        $syllabusDb = new Api_Model_DbTable_Syllabus();

        $dataSearch = $this->_getParam('datasearch');

        $dataSearchExplode = explode('-', $dataSearch);

        $eid   = $this->sesion->eid;
        $oid   = $this->sesion->oid;
        $perid = base64_decode($dataSearchExplode[0]);

        $dataSearchExplode = base64_decode($dataSearchExplode[1]);
        $dataSearchExplode = explode('-', $dataSearchExplode);
        
        $escid = $dataSearchExplode[0];
        $subid = $dataSearchExplode[1];

        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'perid' => $perid,
                        'escid' => $escid,
                        'subid' => $subid );

        $preDataCourses = $syllabusDb->_getFilter($where);
        foreach ($preDataCourses as $c => $course) {
            $where = array( 'eid'      => $eid,
                            'oid'      => $oid,
                            'courseid' => $course['courseid'],
                            'curid'    => $course['curid'] );
            $attrib = array('name', 'semid');

            $nameCourse = $courseDb->_getFilter($where, $attrib);


            $dataCourses[$c]['courseid']     = $course['courseid'];
            $dataCourses[$c]['curid']        = $course['curid'];
            $dataCourses[$c]['escid']        = $course['escid'];
            $dataCourses[$c]['subid']        = $course['subid'];
            $dataCourses[$c]['perid']        = $course['perid'];
            $dataCourses[$c]['nameCourse']   = $nameCourse[0]['name'];
            $dataCourses[$c]['turno']        = $course['turno'];
            $dataCourses[$c]['semestre']     = $nameCourse[0]['semid'];
            $dataCourses[$c]['bibliografia'] = $course['sources'];
        }
        $this->view->dataCourses = $dataCourses;
    }

    public function printAction(){
        $this->_helper->layout()->disableLayout();

        //dataBases
        $courseDb     = new Api_Model_DbTable_Course();
        $syllabusDb   = new Api_Model_DbTable_Syllabus();
        $specialityDb = new Api_Model_DbTable_Speciality();
        $facultyDb    = new Api_Model_DbTable_Faculty();

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        $dataSilabo = explode('-', base64_decode($this->_getParam('datasilabo')));

        $escid    = $dataSilabo[0];
        $subid    = $dataSilabo[1];
        $courseid = $dataSilabo[2];
        $curid    = $dataSilabo[3];
        $turno    = $dataSilabo[4];
        $perid    = $dataSilabo[5];

        $dataCourse['courseid'] = $courseid;
        $dataCourse['curid']    = $curid;
        $dataCourse['turno']    = $turno;
        $dataCourse['perid']    = $perid;

        $where = array( 'eid'      => $eid,
                        'oid'      => $oid,
                        'escid'    => $escid,
                        'subid'    => $subid,
                        'courseid' => $courseid,
                        'curid'    => $curid,
                        'turno'    => $turno,
                        'perid'    => $perid );

        $attrib = array('sources');

        $preDataSilabo = $syllabusDb->_getFilter($where, $attrib);

        $dataCourse['bibliografia'] = $preDataSilabo[0]['sources'];

        //nombre del Curso
        $where = array( 'eid'      => $eid,
                        'oid'      => $oid,
                        'courseid' => $courseid,
                        'curid'    => $curid );

        $attrib = array('name', 'semid');

        $nameCourse = $courseDb->_getFilter($where, $attrib);

        $dataCourse['nameCourse'] = $nameCourse[0]['name'];
        $dataCourse['semestre']   = $nameCourse[0]['semid'];

        //datos de la Escuela
        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'escid' => $escid,
                        'subid' => $subid );

        $attrib = array('name', 'facid');

        $dataEscuelas = $specialityDb->_getFilter($where, $attrib);

        $dataCourse['escid']       = $escid;
        $dataCourse['nameEscuela'] = $dataEscuelas[0]['name'];

        //data Facultad        
        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'facid' => $dataEscuelas[0]['facid'] );
        $attrib = array('name', 'facid');

        $dataFacultad = $facultyDb->_getFilter($where, $attrib);
        $dataCourse['facid']        = $dataEscuelas[0]['facid'];
        $dataCourse['nameFacultad'] = $dataFacultad[0]['name'];

        $this->view->dataCourse = $dataCourse;
    }
}

