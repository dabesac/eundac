<?php

class Alumno_IdiomasController extends Zend_Controller_Action {

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

    public function matriculaAction(){
        $this->_helper->layout()->disableLayout();

        //dataBases
        $langRegisterCourseDb = new Api_Model_DbTable_LangRegisterCourse();
        $langPeriodDb         = new Api_Model_DbTable_LangPeriod();
        $langProgramDb        = new Api_Model_DbTable_LangProgram();
        $langCoursesDb        = new Api_Model_DbTable_LangCourse();
        $langTasaDb           = new Api_Model_DbTable_LangTasa();
        $langProgramTasasDb   = new Api_Model_DbTable_LangProgramTasas();
        $langProgramTurnosDb  = new Api_Model_DbTable_LangProgramTurnos();
        $bankReceiptDb        = new Api_Model_DbTable_Bankreceipts();

        $eid = $this->sesion->eid;
        $rid = $this->sesion->rid;
        $pid = $this->sesion->pid;

        //Obtener Periodo Activo de Idiomas
        $where = array( 'eid'   => $eid,
                        'state' => 'A');
        $attrib = array('name', 'perid');
        $dataPeriod = $langPeriodDb->_getFilter($where, $attrib);
        if ($dataPeriod) {
            $periodActive['perid'] = $dataPeriod[0]['perid'];
            $periodActive['name']  = $dataPeriod[0]['name'];
            $this->view->period = $periodActive;

            //Programas Disponibles
            $where = array( 'eid'   => $eid,
                            'perid' => $periodActive['perid'] );

            $preDataPrograms = $langProgramDb->_getFilter($where);

            $validate = 'no';
            foreach ($preDataPrograms as $c => $program) {
                //Verificando si ya esta Pre-Matriculado
                $where = array( 'eid'   => $eid,
                                'cid'   => $program['cid'],
                                'perid' => $program['perid'],
                                'pid'   => $pid );
                $attrib = array('state', 'turno');
                $alreadyRegister = $langRegisterCourseDb->_getFilter($where, $attrib);
                if ($alreadyRegister[0]['state'] == 'I') {
                    $dataPrograms[$c]['alreadyRegister'] = 'yes';
                    $dataPrograms[$c]['turnoRegistrado'] = $alreadyRegister[0]['turno'];
                }elseif($alreadyRegister[0]['state'] == 'M'){
                    $validate = 'yes';
                    break;
                }else{
                    $dataPrograms[$c]['alreadyRegister'] = 'no';
                }

                //Datos de Tasa
                $where = array( 'eid'   => $eid,
                                'cid'   => $program['cid'],
                                'perid' => $program['perid'] );
                $attrib = array('tasaid', 'costo');
                $dataTasaProgram = $langProgramTasasDb->_getFilter($where, $attrib);

                $num_cuenta = '';
                $costo      = '';
                foreach ($dataTasaProgram as $tasaProgram) {
                    $where = array( 'eid'    => $eid,
                                    'tasaid' => $tasaProgram['tasaid']);
                    $attrib = array('code_rol', 'num_cuenta');

                    $rolTasa = $langTasaDb->_getFilter($where, $attrib);
                    if ($rid == $rolTasa[0]['code_rol']) {
                        $num_cuenta = $rolTasa[0]['num_cuenta'];
                        $costo      = $tasaProgram['costo'];
                    }
                }
                if ($num_cuenta) {
                    //Datos de Curso
                    $where = array( 'eid' => $eid,
                                    'cid' => $program['cid'] );
                    $attrib = array('name', 'credits', 'prerequisite');

                    $dataCourse = $langCoursesDb->_getFilter($where, $attrib);

                    $dataPrograms[$c]['courseId']      = $program['cid'];
                    $dataPrograms[$c]['courseName']    = $dataCourse[0]['name'];
                    $dataPrograms[$c]['courseCredits'] = $dataCourse[0]['credits'];
                    $dataPrograms[$c]['coursePre']     = $dataCourse[0]['prerequisite'];
                    $dataPrograms[$c]['costo']         = $costo;
                    $dataPrograms[$c]['studentId']     = $pid;
                    $dataPrograms[$c]['perid']         = $program['perid'];

                    //Turnos por Programa
                    $where = array( 'eid'   => $eid,
                                    'cid'   => $program['cid'],
                                    'perid' => $program['perid'], );
                    $attrib = array('turnoid', 'frecuencia');

                    $dataTurnos = $langProgramTurnosDb->_getFilter($where, $attrib);
                    foreach ($dataTurnos as $cProgramTurnos => $turno) {
                       $dataPrograms[$c]['turnos'][$cProgramTurnos] = array( 'turnoid'    => $turno['turnoid'],
                                                                             'frecuencia' => $turno['frecuencia']);
                    }
                }

            }

            /*$where = array(
                            'code_student' => $pid,
                            'concept'      => '00000048' );
            print_r($where);*/
            
            $this->view->dataPrograms = $dataPrograms;
            $this->view->validate     = $validate;
            $this->view->num_cuenta   = $num_cuenta;
        }

    }

    public function registerstudentAction(){
        $this->_helper->layout()->disableLayout();
        //DataBases
        $langUserDb           = new Api_Model_DbTable_LangUser();
        $langRegisterDb       = new Api_Model_DbTable_LangRegister();
        $langRegisterCourseDb = new Api_Model_DbTable_LangRegisterCourse();

        $eid = $this->sesion->eid;

        $formData = $this->getRequest()->getPost();

        //Verificar Si Existe el Usuario
        $where = array( 'eid' => $eid,
                        'pid' => $formData['pid'] );

        $personData = $langUserDb->_getFilter($where);
        $personExist = '';
        if (!$personData) {
            $dataSaveUser = array(  'eid'   => $eid,
                                    'pid'   => $formData['pid'],
                                    'rid'   => 'AL',
                                    'pwd'   => md5($formData['pid']),
                                    'state' => 'A' );
            if ($langUserDb->_save($dataSaveUser)) {
                $personExist = 'yes';
            }
        }else{
            $personExist = 'yes';
        }
        
        if ($personExist) {
            //Verificar si ya tiene matricula ese periodo
            $where = array( 'eid'   => $eid,
                            'pid'   => $formData['pid'],
                            'perid' => $formData['perid'] );

            $registerData = $langRegisterDb->_getFilter($where);

            if (!$registerData) {
                $dataSaveRegister = array(  'eid'     => $eid,
                                            'perid'   => $formData['perid'],
                                            'pid'     => $formData['pid'],
                                            'created' => date('Y-m-d h:m:s'),
                                            'state'   => 'I' );

                $saveRegister = $langRegisterDb->_save($dataSaveRegister);
            }else{
                $saveRegister = 1;
            }

            if ($saveRegister) {
                $dataSaveRegisterCourses = array(   'eid'     => $eid,
                                                    'perid'   => $formData['perid'],
                                                    'cid'     => $formData['cid'],
                                                    'turno'   => $formData['turno'],
                                                    'pid'     => $formData['pid'],
                                                    'created' => date('Y-m-d h:m:s'),
                                                    'state'   => 'I' );
                if ($langRegisterCourseDb->_save($dataSaveRegisterCourses)) {
                    echo 1;
                }else{
                    echo 0;
                }
            }
        }
    }

    public function matriculaactualAction(){
        $this->_helper->layout()->disableLayout();
        $langRegisterDb       = new Api_Model_DbTable_LangRegister();
        $langRegisterCourseDb = new Api_Model_DbTable_LangRegisterCourse();
        $langPeriodDb         = new Api_Model_DbTable_LangPeriod();
        $langCoursesDb        = new Api_Model_DbTable_LangCourse();
        $langProgramTurnosDb  = new Api_Model_DbTable_LangProgramTurnos();

        $eid = $this->sesion->eid;
        $pid = $this->sesion->pid;

        //Obtener Periodo Activo de Idiomas
        $where = array( 'eid'   => $eid,
                        'state' => 'A');
        $attrib = array('name', 'perid');
        $dataPeriod = $langPeriodDb->_getFilter($where, $attrib);
        if ($dataPeriod) {
            $periodActive['perid'] = $dataPeriod[0]['perid'];
            $periodActive['name']  = $dataPeriod[0]['name'];
            $this->view->period = $periodActive;

            //Obteniendo Cursos Matriculados
            $where = array( 'eid'   => $eid,
                            'pid'   => $pid,
                            'perid' => $periodActive['perid'],
                            'state' => 'M' );

            $attrib = array('cid', 'turno');

            $preDataCourses = $langRegisterCourseDb->_getFilter($where, $attrib);
            foreach ($preDataCourses as $c => $course) {
                //Datos del Curso
                $where = array( 'eid' => $eid,
                                'cid' => $course['cid'] );
                $attrib = array('name' , 'credits');
                $preDataCourse = $langCoursesDb->_getFilter($where, $attrib);
                $dataCourses[$c]['cid']     = $course['cid'];
                $dataCourses[$c]['name']    = $preDataCourse[0]['name'];
                $dataCourses[$c]['turno']   = $course['turno'];
                $dataCourses[$c]['credits'] = $course['credits'];

                //Datos del Turno
                $where = array( 'eid'     => $eid,
                                'cid'     => $course['cid'],
                                'perid'   => $periodActive['perid'],
                                'turnoid' => $course['turno'] );
                $attrib = array('frecuencia');
                $dataProgramTurno = $langProgramTurnosDb->_getFilter($where, $attrib);
                $dataCourses[$c]['frecuencia'] = $dataProgramTurno[0]['frecuencia'];
            }
            $this->view->dataCourses = $dataCourses;
        }
    }

    public function recordregistersAction(){
        $this->_helper->layout()->disableLayout();

        //DataBases
        $langRegisterDb       = new Api_Model_DbTable_LangRegister();
        $langRegisterCourseDb = new Api_Model_DbTable_LangRegisterCourse();
        $langCoursesDb        = new Api_Model_DbTable_LangCourse();
        $langProgramTurnosDb  = new Api_Model_DbTable_LangProgramTurnos();

        $eid = $this->sesion->eid;
        $pid = $this->sesion->pid;

        $where = array( 'eid' => $eid,
                        'pid' => $pid );
        print_r($where);

    }

}