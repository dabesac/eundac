<?php

class Rest_PreregisterController extends Zend_Rest_Controller {
	public function init() {
		$this->_helper->layout()->disableLayout();
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;

    }

    public function headAction() {}

    public function indexAction() {

        //dataBases
        $registerDb       = new Api_Model_DbTable_Registration();
        $registerCourseDb = new Api_Model_DbTable_Registrationxcourse();
        $courseDb         = new Api_Model_DbTable_Course();
        $courseTeacherDb  = new Api_Model_DbTable_Coursexteacher();
        $bankreceiptsDb   = new Api_Model_DbTable_Bankreceipts();
        $conditionDb      = new Api_Model_DbTable_Studentcondition();
        $personDb         = new Api_Model_DbTable_Person();

        //PREFIJOS FANCYS
        $semesters_roman = array( 1  => 'I', 2  => 'II', 3  => 'III', 4  => 'IV', 5  => 'V', 6  => 'VI', 7  => 'VII',
                                    8  => 'VIII', 9  => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII' );
        $attempts = array( 0 => '1era', 1 => '2da', 2  => '3ra', 3  => '4ta', 4  => '5ta', 5  => '6ta', 6  => '7ma', 7  => '8va', 8  => '9na' );
        $payment_conditionals = array( 2  => 11, 3  => 22, 4  => 33, 5  => 44, 6  => 55, 7  => 66, 8  => 77 );

        $eid   = $this->sesion->eid;
        $oid   = $this->sesion->oid;
        $uid   = $this->sesion->uid;
        $pid   = $this->sesion->pid;
        $escid = $this->sesion->escid;
        $subid = $this->sesion->subid;
        $curid = $this->sesion->curid;
        $perid = $this->sesion->period->perid;
        

        //verificar si ya esta prematriculado
        $courses_data = null;
        $where = array(
                        'eid'   => $eid,
                        'oid'   => $oid,
                        'uid'   => $uid,
                        'pid'   => $pid,
                        'escid' => $escid,
                        'subid' => $subid,
                        'regid' => $uid.$perid,
                        'perid' => $perid );
        $register_pd = $registerDb->_getOne($where);
        if ($register_pd['state'] == 'I' or $register_pd['state'] == 'M') {
            $semester_assign       = $register_pd['semid'];
            $semester_roman_assign = $semesters_roman[$register_pd['semid']];

            $order = array('courseid ASC');
            $courses_pd = $registerCourseDb->_getFilter($where, null, $order);
            if ($courses_pd) {
                foreach ($courses_pd as $c => $course) {
                    // datos del curso
                    $where = array(
                                    'eid'      => $eid,
                                    'oid'      => $oid,
                                    'escid'    => $escid,
                                    'subid'    => $subid,
                                    'courseid' => $course['courseid'],
                                    'curid'    => $course['curid'] );
                    $course_pd = $courseDb->_getOne($where);

                    $courses_data[$c] = array(
                                                'idget'                 => base64_encode($course['courseid']),
                                                'code'                  => $course['courseid'],
                                                'code_cur'              => $course['curid'],
                                                'name'                  => $course_pd['name'],
                                                'credits'               => $course_pd['credits'],
                                                'turn'                  => $course['turno'],
                                                'teacher'               => null,
                                                'semester_roman'        => $semesters_roman[$course_pd['semid']],
                                                'semester'              => $course_pd['semid'],
                                                'semester_assign'       => $semester_assign,
                                                'semester_roman_assign' => $semester_roman_assign,
                                                'attempt'               => 'No se',
                                                'type'                  => 'No se',
                                                'state'                 => 'I',
                                                'payment_did'           => true,
                                                'carry'                 => false );
                    
                    // course teacher
                    $where = array(
                                    'eid'      => $eid,
                                    'oid'      => $oid,
                                    'escid'    => $escid,
                                    'subid'    => $subid,
                                    'courseid' => $course['courseid'],
                                    'curid'    => $course['curid'],
                                    'turno'    => $course['turno'],
                                    'perid'    => $perid,
                                    'is_main'  => 'S' );
                    $attrib = array('pid');
                    $teacher_pd = $courseTeacherDb->_getFilter($where, $attrib);
                    //name teacher
                    $where = array(
                                    'eid' => $eid,
                                    'pid' => $teacher_pd[0]['pid'] );
                    $person_pd = $personDb->_getOne($where);
                    $courses_data[$c]['teacher'] = $person_pd['last_name0'].' '.
                                                    $person_pd['last_name1'].' '.
                                                    $person_pd['first_name'];

                    // Attempts
                    $where = array(
                                    'eid'      => $eid,
                                    'oid'      => $oid,
                                    'escid'    => $escid,
                                    'subid'    => $subid,
                                    'courseid' => $course['courseid'],
                                    'curid'    => $course['curid'],
                                    'uid'      => $uid,
                                    'pid'      => $pid,
                                    'state'    => 'M' );
                    $attrib = array('perid', 'notafinal', 'courseid');
                    $courses_attempt_pd = $registerCourseDb->_getFilter($where, $attrib);
                    $attempt = 0;
                    if ($courses_attempt_pd) {
                        foreach ($courses_attempt_pd as $c_c => $course) {
                            if ($perid != $course['perid']) {
                                $letter = substr($course['perid'], 2);
                                if ($letter == 'A' or $letter == 'B' or $letter == 'N') {
                                    if ($course['notafinal'] != '-3') {
                                        $attempt++;
                                    }
                                }
                            }
                        }
                    }

                    $courses_data[$c]['attempt'] = $attempts[$attempt];
                    $courses_data[$c]['condition'] = false;
                    if ($attempt >= 2) {
                        $courses_data[$c]['condition'] = true;
                    }
                    
                    // Si el curso es extraordinario
                    if (substr($perid, 2) == 'A') {
                        if (($course_pd['semid']%2) == 0) {
                            $courses_data[$c]['type'] = 'X';
                        }
                    } else if (substr($perid, 2) == 'B') {
                        if (($course_pd['semid']%2) != 0) {
                            $courses_data[$c]['type'] = 'X';
                        }
                    }
                }
            }
        } else {
            // recibos condicionales
            $where = array(
                            'code_student' => $uid,
                            'concept'      => '00000045',
                            'perid'        => $perid );
            $receipts_conditional = $bankreceiptsDb->_getFilter($where);
            $total_payment_condition = 0;
            if ($receipts_conditional) {
                foreach ($receipts_conditional as $receipt) {
                    $total_payment_condition = $total_payment_condition + $receipt['amount'];
                }
            }

            // condiciones del estudiante
            $where = array(
                            'eid'   => $eid,
                            'oid'   => $oid,
                            'uid'   => $uid,
                            'pid'   => $pid,
                            'escid' => $escid,
                            'subid' => $subid,
                            'perid' => $perid );
            $conditions_pd = $conditionDb->_getAllStudent($where);

            // datos para pedir los cursos
            $where = array(
                            'eid'      => base64_encode($eid),
                            'oid'      => base64_encode($oid),
                            'uid'      => base64_encode($uid),
                            'pid'      => base64_encode($pid),
                            'escid'    => base64_encode($escid),
                            'subid'    => base64_encode($subid),
                            'perid'    => base64_encode($perid),
                            'curid'    => base64_encode($curid),
                            'semestre' => base64_encode(4));
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass('Zend_Rest_Client');
            $base_url = 'http://api.undac.edu.pe:8080/';
            $route = '/'.base64_encode('s3lf.040c0c030$0$0').'/'.base64_encode('__999c0n$um3r999__').'/pendig_absolute';
            $client = new Zend_Rest_Client($base_url);
            $httpClient = $client->getHttpClient();
            $httpClient->setConfig(array("timeout" => 680));
            $response = $client->restget($route,$where);
            $list = $response->getBody();
            $courses_pd = Zend_Json::decode($list);

            // Empaquetar cursos
            if ($courses_pd and $courses_pd['curricula'] != 0) {
                $course_condition = 0;
                $c_semesters = 0;
                $current_semester = 0;
                $c_courses = 0;
                foreach ($courses_pd as $c => $course) {
                    //Dejar curso
                    $render_course = true;
                    if ($conditions_pd) {
                        foreach ($conditions_pd as $condition) {
                            if ($course['courseid'] == $condition['courseid'] and $condition['type'] == 'LE') {
                                $render_course = false;
                            }
                        }
                    }

                    if ($render_course) {
                        $courses_data[$c_courses] = array(
                                                'idget'          => base64_encode($course['courseid'].$course['turno']),
                                                'code'           => $course['courseid'],
                                                'code_cur'       => $course['curid'],
                                                'name'           => $course['name'],
                                                'credits'        => $course['credits'],
                                                'turn'           => $course['turno'],
                                                'teacher'        => $course['docente'],
                                                'semester_roman' => $semesters_roman[$course['semid']],
                                                'semester'       => $course['semid'],
                                                'attempt'        => $attempts[$course['veces']],
                                                'type'           => $course['type'],
                                                'state'          => 'I',
                                                'payment_did'    => true,
                                                'carry'          => false );
                    

                        // permitir curso
                        $courses_data[$c_courses]['condition'] = false;
                        $courses_data[$c_courses]['condition_render'] = true;

                        // condicion por 3ra, 4ta , 5ta etc vez
                        if ($course['veces'] >= 2) {
                            $courses_data[$c_courses]['condition'] = true;
                            $condition_allow = false;
                            // verificar condicion
                            if ($conditions_pd) {
                                foreach ($conditions_pd as $condition) {
                                    if ($course['courseid'] == $condition['courseid'] and $condition['type'] == 'CO') {
                                        $condition_allow = true;
                                    }
                                }
                            }

                            if ($condition_allow) {
                                $condition_allow = false;

                                //pago condicional del curso
                                $courses_data[$c_courses]['payment'] = $course['credits'] * $payment_conditionals[$course['veces']];
                                $courses_data[$c_courses]['payment_did']  = false;
                                if ($total_payment_condition >= $courses_data[$c_courses]['payment'] and
                                    $course['courseid'] != $course_condition) {
                                    $courses_data[$c_courses]['payment_did']  = true;
                                    $course_condition = $course['courseid'];
                                    $total_payment_condition = $total_payment_condition - $courses_data[$c_courses]['payment'];
                                    $condition_allow = true;
                                } else if ($course['courseid'] == $course_condition){
                                    $courses_data[$c_courses]['payment_did']  = true;
                                    $condition_allow = true;
                                }
                            }
                            
                            $courses_data[$c_courses]['condition_allow'] = $condition_allow;
                        }
                        // Si el curso es extraordinario
                        if (substr($perid, 2) == 'A') {
                            if (($course['semid']%2) == 0) {
                                $courses_data[$c_courses]['type'] = 'X';
                            }
                        } else if (substr($perid, 2) == 'B') {
                            if (($course['semid']%2) != 0) {
                                $courses_data[$c_courses]['type'] = 'X';
                            }
                        }
                        if ($course['semid'] != 1) {
                            $courses_data[$c_courses]['semester_close'] = $course['semid'] - 1;
                        } else {
                            $courses_data[$c_courses]['semester_close'] = $course['semid'] + 1;
                        }

                        // Si el curso es de equivalencia

                        $c_courses++;
                    }
                }
            }
        }
        //print_r($courses_data);
        return $this->_helper->json->sendJson($courses_data);
    }

    public function getAction() {
        $result['success'] = 'get';
        return $this->_helper->json->sendJson($result);
    }


    public function postAction() {
        $result['success'] = 'post';
        return $this->_helper->json->sendJson($result);
    }

    public function putAction() {
        //dataBases
        $registerDb       = new Api_Model_DbTable_Registration();
        $registerCourseDb = new Api_Model_DbTable_Registrationxcourse();

        $result['success'] = 0;
        $courses_data = $this->getRequest()->getRawBody();
        if ($courses_data) {
            $courses_data = Zend_Json::decode($courses_data);

            //Verificar si ya existe prematricula y si no guardarla
            $eid   = $this->sesion->eid;
            $oid   = $this->sesion->oid;
            $uid   = $this->sesion->uid;
            $pid   = $this->sesion->pid;
            $escid = $this->sesion->escid;
            $subid = $this->sesion->subid;
            $perid = $this->sesion->period->perid;
            

            $where = array(
                            'eid' => $eid,
                            'oid' => $oid,
                            'uid' => $uid,
                            'pid' => $pid,
                            'escid' => $escid,
                            'subid' => $subid,
                            'perid' => $perid,
                            'regid' => $uid.$perid );
            $register_pd = $registerDb->_getOne($where);
            if ($register_pd['state'] == 'B') {
                $register_pk = array(
                                        'eid'           => $eid,
                                        'oid'           => $oid,
                                        'uid'           => $uid,
                                        'pid'           => $pid,
                                        'escid'         => $escid,
                                        'subid'         => $subid,
                                        'regid'         => $uid.$perid,
                                        'perid'         => $perid );

                $register_updated_data = array(
                                        'modified' => $uid,
                                        'updated'  => date('Y-m-d h:i:s'),
                                        'state'    => 'I' );
                $registerDb->_update($register_updated_data, $register_pk);
            }

            //Salvar Curso
            $course_register_save = array(
                                            'eid'           => $eid,
                                            'oid'           => $oid,
                                            'uid'           => $uid,
                                            'pid'           => $pid,
                                            'escid'         => $escid,
                                            'subid'         => $subid,
                                            'regid'         => $uid.$perid,
                                            'perid'         => $perid,
                                            'courseid'      => $courses_data['code'],
                                            'curid'         => $courses_data['code_cur'],
                                            'turno'         => $courses_data['turn'],
                                            'register'      => $uid,
                                            'created'       => date('Y-m-d h:i:s'),
                                            'state'         => 'I',
                                            'approved'      => $uid,
                                            'approved_date' => date('Y-m-d h:i:s'));

            if ($registerCourseDb->_save($course_register_save)) {
                $result['success'] = 1;
            }   
        }
        return $this->_helper->json->sendJson($result);
    }

    public function deleteAction() {
        // dataBase
        $registerDb = new Api_Model_DbTable_Registration();

        $eid   = $this->sesion->eid;
        $oid   = $this->sesion->oid;
        $uid   = $this->sesion->uid;
        $pid   = $this->sesion->pid;
        $escid = $this->sesion->escid;
        $subid = $this->sesion->subid;
        $perid = $this->sesion->period->perid;
        

        $register_pk = array(
                        'eid'   => $eid,
                        'oid'   => $oid,
                        'uid'   => $uid,
                        'pid'   => $pid,
                        'escid' => $escid,
                        'subid' => $subid,
                        'perid' => $perid,
                        'regid' => $uid.$perid );

        $registerDb->_delete($register_pk);
        $result['success'] = true;
        return $this->_helper->json->sendJson($result);
    }
}