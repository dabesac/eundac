<?php

class Alumno_ProjectsController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	$this->sesion = $login;
        //$this->sesion->period->perid = '13B';

    }

    public function listprojectsAction(){
    	$serverErp = new Eundac_Connect_openerp();
        //DataBases
        $registerDb = new Api_Model_DbTable_Registration();
        
        $oid   = $this->sesion->oid;
        $eid   = $this->sesion->eid;
        $pid   = $this->sesion->pid;
        $uid   = $this->sesion->uid;
        $perid = $this->sesion->period->perid;

        $where = array( 'oid'   => $oid,
                        'eid'   => $eid,
                        'uid'   => $uid,
                        'pid'   => $pid,
                        'perid' => $perid,
                        'state' => 'M' );
        $attrib = array('regid');
        $register = $registerDb->_getFilter($where, $attrib);
        $regid = $register[0]['regid'];
        $this->view->regid = $regid;

        if ($register) {
            $query = array(
                        array(
                            'column'   => 'state',
                            'operator' => '=',
                            'value'    => 'A',
                            'type'     => 'string'
                            )
                    );
            
            $idsProjectsCronogram = $serverErp->search('inv.pro.project.cronogram', $query);
            $attributes = array('project_id');

            $projectsCronogram = $serverErp->read($idsProjectsCronogram, $attributes, 'inv.pro.project.cronogram');
            //print_r($projectsCronogram);

            if ($projectsCronogram) {
                $proyeccionMatriculado    = array('state' => 'no');
                $investigacionMatriculado = array('state' => 'no');
                foreach ($projectsCronogram as $c => $projectCronogram) {
                    $query = array(
                        array(
                            'column'   => 'id',
                            'operator' => '=',
                            'value'    => $projectCronogram['project_id'][0],
                            'type'     => 'int'
                            )
                    );
                    $idProject = $serverErp->search('inv.pro.project', $query);
                    $attributes = array();
                    $project = $serverErp->read($idProject, $attributes, 'inv.pro.project');

                    //Cantidad de Estudiantes Matriculados
                    $query = array(
                        array(
                            'column'   => 'project_cronogram_id',
                            'operator' => '=',
                            'value'    => $projectCronogram['id'],
                            'type'     => 'int'
                            )
                    );
                    $idsStudents  = $serverErp->search('inv.pro.students', $query);
                    $attributes   = array('code_register', 'project_cronogram_id');
                    $students     = $serverErp->read($idsStudents, $attributes, 'inv.pro.students');
                    $cantStudents = count($students);

                    if ($project[0]['type'] == 'P') {
                        $proyeccionSocial[$c] = $project;
                        $proyeccionSocial[$c]['cantStudents']       = $cantStudents;
                        $proyeccionSocial[$c]['projectCronogramId'] = $projectCronogram['id'];
                        foreach ($students as $student) {
                            if ($student['code_register'] == $uid) {
                                $proyeccionMatriculado['state'] = 'si';
                                $proyeccionMatriculado['project'] = $project;
                                $proyeccionMatriculado['cantStudents'] = $cantStudents;
                            }
                        }

                    }else if ($project[0]['type'] == 'I'){
                        $investigacion[$c] = $project;
                        $investigacion[$c]['cantStudents']       = $cantStudents;
                        $investigacion[$c]['projectCronogramId'] = $projectCronogram['id'];
                        foreach ($students as $student) {
                            if ($student['code_register'] == $uid) {
                                $investigacionMatriculado['state'] = 'si';
                                $investigacionMatriculado['project'] = $project;
                                $investigacionMatriculado['cantStudents'] = $cantStudents;
                            }
                        }
                    }
                }
            }
        }

        $this->view->proyeccionMatriculado    = $proyeccionMatriculado;
        $this->view->proyeccionSocial         = $proyeccionSocial;

        $this->view->investigacionMatriculado = $investigacionMatriculado;
        $this->view->investigacion            = $investigacion;
        
    }

    public function registerstudentAction(){
        $this->_helper->layout()->disableLayout();
        $serverErp = new Eundac_Connect_openerp();

        $projectCronogramId = $this->_getParam('projectcronogramid');
        $regid              = $this->_getParam('regid');

        $uid      = $this->sesion->uid;
        $fullname = $this->sesion->infouser['fullname'];

        $data = array(  'code_register'        => $regid,
                        'state'                => 'I',
                        'project_cronogram_id' => $projectCronogramId,
                        'name'                 => $fullname );

        $create = $serverErp->create('inv.pro.students', $data);
        if ($create) {
            echo 'true';
        }else {
            echo 'false';
        }
    }

}