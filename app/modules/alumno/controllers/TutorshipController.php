<?php

class Alumno_TutorshipController extends Zend_Controller_Action {

    public function init() {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index', "index", 'default');
    	}
    	$login = $sesion->getStorage()->read();
    	$this->sesion = $login;

    }

    public function listtutorsAction(){
        //Conexion ERP
        $server = new Eundac_Connect_openerp();

        //DataBases eUndac
        $registerDb = new Api_Model_DbTable_Registration();

        $eid   = $this->sesion->eid;
        $oid   = $this->sesion->oid;
        $uid   = $this->sesion->uid;
        $pid   = $this->sesion->pid;
        $escid = $this->sesion->escid;
        $perid = $this->sesion->period->perid;

        //Consultando Semestre
        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'uid'   => $uid,
                        'pid'   => $pid,
                        'escid' => $escid,
                        'perid' => '13B',
                        'state' => 'M' );

        $register = $registerDb->_getFilter($where);

        if ($register) {
            $semid    = $register[0]['semid'];
            $regid    = $register[0]['regid'];

            $dataStudent = array( 'regid' => $regid );
            $this->view->dataStudent = $dataStudent;

            //Consulta ERP
            //Escuela
            $query = array(
                          array('column'   => 'of_id',
                                'operator' => '=',
                                'value'    =>  $escid,
                                'type'     => 'string' )
                        );
            $idsDepartment  = $server->search('hr.department', $query);
            $attributes     = array('id');
            $dataSpeciality = $server->read($idsDepartment, $attributes, 'hr.department');
            $departmentId   = $dataSpeciality[0]['id'];

            //Consulta de Existencia
            $query = array(
                        array(  'column' => 'registered_code',
                                'operator' => '=',
                                'value' => $regid,
                                'type' => 'string')
                        );
            $idStudent = $server->search('tutoring.students', $query);
            if (!$idStudent) {
            $stateStudent = 'NoExiste';
                //Tutores de Acuerdo a Escuela y Semestre
                $query = array(
                            array(  'column'   => 'department_id',
                                    'operator' => '=',
                                    'value'    => $departmentId,
                                    'type'     => 'int' ),

                            array(  'column'   => 'semid',
                                    'operator' => '=',
                                    'value'    => $semid,
                                    'type'     => 'int' )
                            );

                $idsTutoring = $server->search('tutoring', $query);
                $attributes  = array();
                $tutors      = $server->read($idsTutoring, $attributes, 'tutoring');

                $this->view->tutors = $tutors;

                $c = 0;
                foreach ($tutors as $tutor) {
                    $totalStudents[$c] = 0;
                    $query = array(
                            array(  'column'   => 'tutoring_id',
                                    'operator' => '=',
                                    'value'    => $tutor['id'],
                                    'type'     => 'int' )
                        );
                    $idsStudents = $server->search('tutoring.students', $query);
                    $totalStudents[$c] = count($idsStudents);
                    if ($totalStudents[$c] == $tutor['number']) {
                        $stateTutor[$c]['state']     = 'C';
                        $stateTutor[$c]['nameState'] = 'Cerrado';
                    }else{
                        $stateTutor[$c]['state']     = 'A';
                        $stateTutor[$c]['nameState'] = 'Abierto';
                    }
                    $c++;
                }
                $this->view->totalStudents = $totalStudents;
                $this->view->stateTutor    = $stateTutor;
                # code...
            }else {
                $stateStudent = 'Existe';
                $attributes  = array();
                $student     = $server->read($idStudent, $attributes, 'tutoring.students');
                $tutorId = $student[0]['tutoring_id'][0];
                $query = array(
                            array(  'column'   => 'id',
                                    'operator' => '=',
                                    'value'    => $tutorId,
                                    'type'     => 'int' )
                        );
                $idTutor = $server->search('tutoring', $query);
                $attributes = array('employee_id');
                $tutorName = $server->read($idTutor, $attributes, 'tutoring');
                $tutor['name'] = $tutorName[0]['employee_id'][1];

                $personId = $tutorName[0]['employee_id'][0];
                $query = array(
                            array(  'column'   => 'id',
                                    'operator' => '=',
                                    'value'    => $personId,
                                    'type'     => 'int' )
                        );

                $idEmploye = $server->search('hr.employee', $query);
                $attributes = array('email_personal');
                $tutorEmail = $server->read($idEmploye, $attributes, 'hr.employee');
                $tutor['email'] = $tutorEmail[0]['email_personal'];

                $this->view->tutor = $tutor;
            }
        }else{
            $stateStudent = 'faltaMatricula';
        }
        $this->view->stateStudent = $stateStudent;
    }

    public function registerstudentAction(){
        $this->_helper->layout()->disableLayout();
        $server = new Eundac_Connect_openerp();

        //DataBases
        $registerDb = new Api_Model_DbTable_Registration();
        //_________________________________

        $tutoringId = $this->_getParam('tutoringid');
        $regid      = $this->_getParam('regid');
        $name       = $this->sesion->infouser['fullname'];
        $uid        = $this->sesion->uid;
        $pid        = $this->sesion->pid;

        $data = array(  'create_uid'      => $uid,
                        'create_date'     => date('Y-m-d'),
                        'state'           => 'I',
                        'registered_code' => $regid,
                        'name'            => $name,
                        'tutoring_id'     => $tutoringId );
        $create = $server->create('tutoring.students',$data);
        if ($create) {
            echo "true";
        }else{
            echo "false";
        }
        /*$ids    = array('2');
        $create =  $connect->write('sede',$data,$ids);

        if ($create) {
            print_r($create);
        }*/
    }

}