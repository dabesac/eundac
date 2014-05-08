
<?php

class Acreditacion_TutorshipController extends Zend_Controller_Action {

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

    }

    public function listteachersAction(){
    	$this->_helper->layout()->disableLayout();

    	$server = new Eundac_Connect_openerp();

    	//DataBase
		$teacherDb  = new Api_Model_DbTable_Users();
		$personDb   = new Api_Model_DbTable_Person();
		$semesterDb = new Api_Model_DbTable_Semester();

		$eid   = $this->sesion->eid;
		$oid   = $this->sesion->oid;
		$escid = $this->sesion->escid;
		$subid = $this->sesion->subid;
		$perid = $this->sesion->period->perid;

    	$where = array(	'eid'   => $eid,
						'oid'   => $oid,
						'escid' => $escid,
						'subid' => $subid,
						'state' => 'A',
						'rid'   => 'DC' );
    	$attrib = array('uid', 'pid');

    	$teachers = $teacherDb->_getFilter($where, $attrib);
    	foreach ($teachers as $c => $teacher) {
    		$where = array(	'eid' => $eid,
    						'pid' => $teacher['pid'] );
    		$attrib = array('last_name0', 'last_name1', 'first_name');
    		$name = $personDb->_getFilter($where, $attrib);
			$teachersData[$c]['full_name'] = $name[0]['last_name0'].' '.$name[0]['last_name1'].' '.$name[0]['first_name'];
			$teachersData[$c]['uid']       = $teacher['uid'];
			$teachersData[$c]['pid']       = $teacher['pid'];

			//Consultar si ya esta asignado un docente
			$query = array(
	                      array('column'   => 'identification_id',
	                            'operator' => '=',
	                            'value'    =>  $teacher['pid'],
	                            'type'     => 'string' )
	                    );
	        $idTeacher  = $server->search('hr.employee', $query);
	        $attributes     = array('id');
	        $dataTeacher = $server->read($idTeacher, $attributes, 'hr.employee');

	        $query = array(
	                      array('column'   => 'employee_id',
	                            'operator' => '=',
	                            'value'    =>  $dataTeacher[0]['id'],
	                            'type'     => 'int' ),

	                      array('column'   => 'perid',
	                            'operator' => '=',
	                            'value'    =>  $perid,
	                            'type'     => 'string' )
	                    );
	        $idTeacher = $server->search('tutoring', $query);
	        if ($idTeacher) {
	        	$teachersData[$c]['asigned'] = 'yes';
		        $attributes = array('semid');
		        $dataTeacher = $server->read($idTeacher, $attributes, 'tutoring');
	        	$teachersData[$c]['semid'] = $dataTeacher[0]['semid'];
	        }else{
				$teachersData[$c]['asigned'] = 'no';
				$teachersData[$c]['semid']   = '-';
	        }
    	}
    	$this->view->teachers = $teachersData;

    	$where = array(	'eid'   => $eid,
						'oid'   => $oid,
						'escid' => $escid,
						'perid' => $perid );
    	$semesters = $semesterDb->_getSemesterXPeriodsXEscid($where);
    	$this->view->semesters = $semesters;
    }

    public function asignardocenteAction(){
    	$this->_helper->layout()->disableLayout();

    	$server = new Eundac_Connect_openerp();

    	$escid = $this->sesion->escid;
    	$perid = $this->sesion->period->perid;

    	$formData = $this->getRequest()->getPost();

    	if ($formData['semid'] != '' and is_numeric($formData['cantStudents']) == true) {
	    	$pid = $formData['pid'];

	    	//Id de Empleado
	    	$query = array(
	                          array('column'   => 'identification_id',
	                                'operator' => '=',
	                                'value'    =>  $pid,
	                                'type'     => 'string' )
	                        );
	        $idTeacher  = $server->search('hr.employee', $query);
	        $attributes     = array('id');
	        $dataTeacher = $server->read($idTeacher, $attributes, 'hr.employee');

	        //Id de Departamento
	        $query = array(
	        				array(	'column'   => 'of_id',
									'operator' => '=',
									'value'    => $escid,
									'type'     => 'string')
	        	);

	        $idDepartment = $server->search('hr.department', $query);
	        $dataDepartment = $server->read($idDepartment, $attributes, 'hr.department');

	        $dataTutoring = array(	'employee_id'   => $dataTeacher[0]['id'],
									'number'        => $formData['cantStudents'],
									'department_id' => $dataDepartment[0]['id'],
									'semid'         => $formData['semid'],
									'name'          => $escid.$formData['semid'],
									'perid'         => $perid );

	        $create = $server->create('tutoring',$dataTutoring);
	        if ($create) {
	            echo 2;
	        }else{
	        	echo "Error al crear";
            }
    	}else{
    		echo 1;
    	}

    }

}
