<?php

class GlobalController extends Zend_Controller_Action {

    public function init()
    {
        $sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		$this->sesion = $login;
    }

    public function listschoolsAction(){
    	$this->_helper->layout()->disableLayout();

    	//DataBases
    	$schoolDb = new Api_Model_DbTable_Speciality();
    	
    	$facid = base64_decode($this->_getParam('id'));

    	$eid = $this->sesion->eid;
    	$oid = $this->sesion->oid;
    	$where = array(	'eid'    => $eid,
						'oid'    => $oid,
						'facid'  => $facid,
						'state'  => 'A',
						'parent' => '' );
    	$attrib = array('escid', 'name', 'subid');
    	$preDataSchool = $schoolDb->_getFilter($where, $attrib);
    	if ($preDataSchool) {
	    	foreach ($preDataSchool as $c => $school) {
	    		$dataSchool[$c]['escid'] = $school['escid'];
	    		$dataSchool[$c]['subid'] = $school['subid'];
	    		$dataSchool[$c]['name']  = $school['name'];
	    	}
	    	$this->view->dataSchool = $dataSchool;
    	}
    }

    public function searchAction(){
        //DataBase
        $personDb     = new Api_Model_DbTable_Person();
        $userDb       = new Api_Model_DbTable_Users();
        $specialityDb = new Api_Model_DbTable_Speciality();
        $facultyDb    = new Api_Model_DbTable_Faculty();

        $eid   = $this->sesion->eid;
        $oid   = $this->sesion->oid;
        $rid   = $this->sesion->rid;
        $facid = $this->sesion->faculty->facid;
        $escid = $this->sesion->escid;
        $subid = $this->sesion->subid;

        $data_search = $this->_getParam('data');

        $users_data = array();
        $where      = array();
        $users_pre  = array();
        if (is_numeric($data_search)) {
            $data_lentgh = strlen($data_search);
            if ($data_lentgh == 10) {
                //codigo 
                $uid = $data_search;
                $where = array(
                                'eid' => $eid,
                                'oid' => $oid,
                                'uid' => $uid );
            } elseif ($data_lentgh == 8) {
                //dni
                $dni = $data_search;
                $where = array(
                                'eid' => $eid,
                                'oid' => $oid,
                                'pid' => $dni );
            }

            if ($rid == 'DF') {
                $lf = strlen($facid);
                $where['left (escid, '.$lf.')'] = $facid;
            } elseif ($rid == 'RF') {
                $where['subid'] = $subid;

                $lf = strlen($facid);
                $where['left (escid, '.$lf.')'] = $facid;
            } elseif ($rid == 'DR') {
                $where['escid'] = $escid;
                $where['subid'] = $subid;
            }

            if ($where) {
                $users_pre_filter = $userDb->_getFilter($where);
                //ordernar datos del filter
                if ($users_pre_filter) {
                    foreach ($users_pre_filter as $c => $user) {
                        //nombres
                        $where = array(
                                        'eid' => $eid,
                                        'pid' => $user['pid'] );
                        $person_pre = $personDb->_getOne($where);

                        $users_pre[$c] = array(
                                            'pid'       => $user['pid'],
                                            'uid'       => $user['uid'],
                                            'full_name' => $person_pre['last_name0'].' '.
                                                            $person_pre['last_name1'].' '.
                                                            $person_pre['first_name'],
                                            'escid' => $user['escid'],
                                            'subid' => $user['subid'],
                                            'rid'   => $user['rid'],
                                            'state' => $user['state'] );
                    }
                }
            }
        } else {
            $name_search = trim(strtoupper($data_search));
            $name_search = mb_strtoupper($name_search,'UTF-8');
            $where = array(
                            'eid'  => $eid,
                            'name' => $name_search );

            if ($rid == 'RC' or $rid == 'AD' or $rid == 'VA' or $rid == 'SP' or $rid == 'RE' or $rid == 'PD' or $rid == 'ES' or $rid == 'CU') {
                $users_pre = $userDb->_getUsuarioXNombreProAll($where);
            } else if ($rid == 'DF'){
                $where['facid'] = $facid;
                $users_pre = $userDb->_getUsuarioXNombreProFaculty($where);
            } elseif ($rid == 'RF') {
                $where['facid'] = $facid;
                $where['subid'] = $subid;
                $users_pre = $userDb->_getUsuarioXNombreProFacultySubid($where);
            } elseif($rid == 'DR') {
                $where['escid'] = $escid;
                $where['subid'] = $subid;
                $users_pre = $userDb->_getUsuarioXNombreProSchool($where);
            }
        }

        //Empaquetar los datos :'v
        $view_data['role']        = $rid;
        $view_data['simple_mode'] = true;
        $view_data['many_result'] = false;
        $view_data['tabs_mode']   = false;
        $view_data['cant_result'] = 0;
        $view_data['tabs']        = array();
        $users_data = array('students' => array(),
                            'graduates' => array(),
                            'teachers' => array() );
        $roles_finded = array(  'students'  => null,
                                'graduated' => null,
                                'teachers'  => null );
        if ($users_pre) {
            $c_al   = 0;
            $c_al_p = true;

            $c_eg   = 0;
            $c_eg_p = true;

            $c_dc = 0;
            $c_dc_p = true;
            foreach ($users_pre as $c => $user) {
                //Escuela
                $where = array( 'eid'   => $eid,
                                'oid'   => $oid,
                                'escid' => $user['escid'],
                                'subid' => $user['subid'] );
                $speciality_pre = $specialityDb->_getOne($where);

                //Facultad
                $where = array( 'eid'   => $eid,
                                'oid'   => $oid,
                                'facid' => $speciality_pre['facid'] );
                $faculty_pre = $facultyDb->_getOne($where);

                if ($user['rid'] == 'AL' and $c_al_p) {
                    $roles_finded['students'] = true;
                    $users_data['students'][$c_al] = array(
                                                        'eid'       => $eid,
                                                        'oid'       => $oid,
                                                        'pid'       => $user['pid'],
                                                        'uid'       => $user['uid'],
                                                        'escid'     => $user['escid'],
                                                        'subid'     => $user['subid'],
                                                        'full_name' => $user['full_name'],
                                                        'role'      => 'Alumno',
                                                        'school'    => $speciality_pre['name'],
                                                        'faculty'   => $faculty_pre['name'],
                                                        'state'     => $user['state'] );
                    $c_al++;
                    if ($c_al > 100) {
                        $c_al_p = false;
                        $view_data['many_result'] = true;
                    }
                } elseif ($user['rid'] == 'EG' and $c_eg_p) {
                    $roles_finded['graduates'] = true;
                    $users_data['graduates'][$c_eg] = array(
                                                        'eid'       => $eid,
                                                        'oid'       => $oid,
                                                        'pid'       => $user['pid'],
                                                        'uid'       => $user['uid'],
                                                        'escid'     => $user['escid'],
                                                        'subid'     => $user['subid'],
                                                        'full_name' => $user['full_name'],
                                                        'role'      => 'Egresado',
                                                        'school'    => $speciality_pre['name'],
                                                        'faculty'   => $faculty_pre['name'],
                                                        'state'     => $user['state'] );
                    $c_eg++;
                    if ($c_eg > 100) {
                        $c_eg_p = false;
                        $view_data['many_result'] = true;
                    }
                } elseif ($user['rid'] == 'DC' and $c_dc_p) {
                    if ($rid == 'RC' or $rid == 'RF' or $rid == 'DR' or $rid == 'VA' or $rid == 'SP' or $rid = 'RE' or $rid = 'CU') {
                        if ($user['state'] == 'A') {
                            $roles_finded['teachers'] = true;
                            $users_data['teachers'][$c_dc] = array(
                                                                'eid'       => $eid,
                                                                'oid'       => $oid,
                                                                'pid'       => $user['pid'],
                                                                'uid'       => $user['uid'],
                                                                'escid'     => $user['escid'],
                                                                'subid'     => $user['subid'],
                                                                'full_name' => $user['full_name'],
                                                                'role'      => 'Docente',
                                                                'school'    => $speciality_pre['name'],
                                                                'faculty'   => $faculty_pre['name'],
                                                                'state'     => $user['state'] );
                            $c_dc++;
                            if ($c_dc > 100) {
                                $c_dc_p = false;
                                $view_data['many_result'] = true;
                            }
                        }
                    } elseif ($rid == 'AD') {
                        $roles_finded['teachers'] = true;
                        $users_data['teachers'][$c_dc] = array(
                                                            'eid'       => $eid,
                                                            'oid'       => $oid,
                                                            'pid'       => $user['pid'],
                                                            'uid'       => $user['uid'],
                                                            'escid'     => $user['escid'],
                                                            'subid'     => $user['subid'],
                                                            'full_name' => $user['full_name'],
                                                            'role'      => 'Docente',
                                                            'school'    => $speciality_pre['name'],
                                                            'faculty'   => $faculty_pre['name'],
                                                            'state'     => $user['state'] );
                        $c_dc++;
                        if ($c_dc > 100) {
                            $c_dc_p = false;
                            $view_data['many_result'] = true;
                        }
                    }
                }
            }
            $view_data['cant_result'] = $c_al + $c_eg + $c_dc;
            
            $counters[0] = array(   'count' => $c_al,
                                    'name'  => 'Estudiante',
                                    'code'  => 'students');
            $counters[1] = array(   'count' => $c_eg,
                                    'name'  => 'Egresado',
                                    'code'  => 'graduates');
            $counters[2] = array(   'count' => $c_dc,
                                    'name'  => 'Docente',
                                    'code'  => 'teachers');

            $how_many = 0;
            foreach ($counters as $counter) {
                if ($counter['count'] > 3) {
                    $view_data['simple_mode'] = false;
                }
                if ($counter['count'] > 0) {
                    $view_data['tabs'][$how_many]['code'] = $counter['code'];
                    $view_data['tabs'][$how_many]['name'] = $counter['name'];
                    if ($counter['count'] > 1) {
                        $view_data['tabs'][$how_many]['name'] = $counter['name'].'s';
                    }
                    $how_many++;
                }
            }
            if ($how_many > 1) {
                $view_data['tabs_mode'] = true;
            }
        }

        $view_data['what_search']  = $data_search;
        $view_data['users_data']   = $users_data;
        $view_data['roles_finded'] = $roles_finded;

        $this->view->view_data = $view_data;
    }

    // public function 

}