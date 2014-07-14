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
            $dataFaculty[$c]['facid'] = $faculty['facid'];
            $dataFaculty[$c]['name']  = $faculty['name'];
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
        $eid   = $this->sesion->eid;
        $oid   = $this->sesion->oid;
        
        $where = array( 'eid'    => $eid,
                        'oid'    => $oid,
                        'parent' => $escid,
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
        $escid = base64_decode($dataSearchExplode[1]);
        $subid = base64_decode($dataSearchExplode[2]);

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
            $dataCourses[$c]['nameCourse']   = $nameCourse[0]['name'];
            $dataCourses[$c]['semestre']     = $nameCourse[0]['semid'];
            $dataCourses[$c]['bibliografia'] = $course['sources'];
        }
        $this->view->dataCourses = $dataCourses;
    }
}

