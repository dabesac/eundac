<?php

class Controlactivity_IndexController extends Zend_Controller_Action {

    public function init()
    {
      $sesion  = Zend_Auth::getInstance();
      if(!$sesion->hasIdentity() ){
        $this->_helper->redirector('index',"index",'default');
      }
      $login = $sesion->getStorage()->read();
      if (!$login->rol['module']=="docente"){
        $this->_helper->redirector('index','index','default');
      }
      $this->sesion = $login;   
    }
    
    public function indexAction()
    {
        $coursesDb         = new Api_Model_DbTable_Course();
        $coursesPeriodsDb  = new Api_Model_DbTable_PeriodsCourses();
        $syllabusDb        = new Api_Model_DbTable_Syllabus();
        $syllabusContentDb = new Api_Model_DbTable_Syllabusunitcontent();
        $syllabusControlDb = new Api_Model_DbTable_ControlActivity();

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        $data = base64_decode($this->_getParam('data'));
        $data = explode('-', $data);

        $escid    = $data[0];
        $subid    = $data[1];
        $perid    = $data[2];
        $courseid = $data[3];
        $curid    = $data[4];
        $turno    = $data[5];

        $currentDate = date('d-m-Y');
        $dataCourse['currentDate']  = $currentDate;

        //Datos de Curso
        $where = array( 'eid'      => $eid,
                        'oid'      => $oid,
                        'escid'    => $escid,
                        'subid'    => $subid,
                        'courseid' => $courseid,
                        'curid'    => $curid );
        $attrib = array('name', 'credits');
        $preDataCourse = $coursesDb->_getFilter($where, $attrib);

        $dataCourse['name']     = $preDataCourse[0]['name'];
        $dataCourse['perid']    = $perid;
        $dataCourse['escid']    = $escid;
        $dataCourse['subid']    = $subid;
        $dataCourse['courseid'] = $courseid;
        $dataCourse['curid']    = $curid;
        $dataCourse['turno']    = $turno;
        $dataCourse['credits']  = $preDataCourse[0]['credits'];

        //Curso Objetivo o Competencia
        $where = array( 'eid'      => $eid,
                        'oid'      => $oid,
                        'perid'    => $perid,
                        'escid'    => $escid,
                        'subid'    => $subid,
                        'courseid' => $courseid,
                        'curid'    => $curid,
                        'turno'    => $turno );
        $attrib = array('type_rate');

        $preDataTypeCourse = $coursesPeriodsDb->_getFilter($where, $attrib);
        if ($preDataTypeCourse[0]['type_rate'] == 'O') {
            $nameSession = 'obj_content';
        }else{
            $nameSession = 'com_conceptual';
        }

        //Verificar si el syllabo esta rellenado
        $attrib = array('state');

        $preDataSyllabus = $syllabusDb->_getFilter($where, $attrib);

        $dataCourse['syllabus'] = $preDataSyllabus[0]['state'];

        if ($dataCourse['syllabus'] == 'C') {
            $attrib = '';
            $order  = array('session ASC');
            $preDataSyllabusContent = $syllabusContentDb->_getFilter($where, $attrib, $order);
            foreach ($preDataSyllabusContent as $c => $content) {
                $dataCourse['syllabusContent'][$c] = array( 'unit'    => $content['unit'],
                                                            'week'    => $content['week'],
                                                            'session' => $content['session'],
                                                            'name'    => $content[$nameSession] );
            }

            $preDataSyllabusControl = $syllabusControlDb->_getFilter($where);
            if ($preDataSyllabusControl) {
                $manySessionRealized = count($preDataSyllabusControl);
                $indexCurrentSession = $manySessionRealized;
            }else{
                $indexCurrentSession = 0;
            }
            $dataCourse['indexCurrent'] = $indexCurrentSession;

            $dataCourse['finishSyllabus'] = 0;

            if ($indexCurrentSession == ($c + 1)) {
                $dataCourse['finishSyllabus'] = 1;
            }



            //clases realizadas
            $attrib = '';
            $order  = array('session DESC');
            $preDataSyllabusContent = $syllabusContentDb->_getFilter($where, $attrib, $order);
            foreach ($preDataSyllabusContent as $c => $content) {
                $dataCourse['syllabusContentInverse'][$c] = array(  'unit'      => $content['unit'],
                                                                    'week'      => $content['week'],
                                                                    'session'   => $content['session'],
                                                                    'name'      => $content[$nameSession],
                                                                    'dateCheck' => '' );

                //Fecha de realización
                $where = array( 'eid'      => $eid,
                                'oid'      => $oid,
                                'perid'    => $perid,
                                'escid'    => $escid,
                                'subid'    => $subid,
                                'courseid' => $courseid,
                                'curid'    => $curid,
                                'unit'     => $content['unit'],
                                'week'     => $content['week'],
                                'session'  => $content['session'] );
                $attrib = array('datecheck');

                $preDateCheck = $syllabusControlDb->_getFilter($where, $attrib);
                if ($preDateCheck[0]['datecheck']) {
                    $dataCourse['syllabusContentInverse'][$c]['dateCheck'] = date('d-m-Y', strtotime($preDateCheck[0]['datecheck']));
                }

            }
            $dataCourse['indexRealized'] = $c - $indexCurrentSession;
        }

        $this->view->dataCourse = $dataCourse;
    }


    public function savesessionAction()
    {
        $this->_helper->layout()->disableLayout();
        //DataBases
        $syllabusControlDb = new Api_Model_DbTable_ControlActivity();

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        $formData = $this->getRequest()->getPost();

        $idsCourse = base64_decode($formData['idsCourse']);
        $idsCourse = explode('-', $idsCourse);

        $dataSave = array(  'eid'      => $eid,
                            'oid'      => $oid,
                            'perid'    => $idsCourse[0],
                            'escid'    => $idsCourse[1],
                            'subid'    => $idsCourse[2],
                            'courseid' => $idsCourse[3],
                            'curid'    => $idsCourse[4],
                            'turno'    => $idsCourse[5],
                            'unit'     => $formData['unit'],
                            'week'     => $formData['week'],
                            'session'  => $formData['session'],
                            'comment'  => $formData['comentClase'],
                            'state'    => 'D',
                            'created'  => date('Y-m-d h:m:s') );

        // Tipo de envio
        if ($formData['whySend'] == 'N') {
            $dataSave['datecheck'] = date('Y-m-d h:m:s', strtotime($formData['currentDate']));
        }else if ($formData['whySend'] == 'O'){
            $dataSave['datecheck'] = date('Y-m-d h:m:s', strtotime($formData['dateChange']));
        }

        if ($syllabusControlDb->_save($dataSave)) {
            $result = array('success' => 1);
        }else{
            $result = array('success' => 0);
        }
        print (json_encode($result));
    }
}