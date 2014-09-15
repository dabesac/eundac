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
        $personDb             = new Api_Model_DbTable_Person();

        $eid   = $this->sesion->eid;
        $rid   = $this->sesion->rid;
        $pid   = $this->sesion->pid;
        $subid = $this->sesion->subid;

        //Obtener Periodo Activo de Idiomas
        $where = array( 'eid'   => $eid,
                        'state' => 'A');
        $attrib = array('name', 'perid');
        $dataPeriod = $langPeriodDb->_getFilter($where, $attrib);
        $dataPrograms = array();
        if ($dataPeriod) {
            $periodActive['perid'] = $dataPeriod[0]['perid'];
            $periodActive['name']  = $dataPeriod[0]['name'];
            $this->view->period = $periodActive;

            //Programas Disponibles
            $where = array( 'eid'   => $eid,
                            'perid' => $periodActive['perid'],
                            'subid' => $subid,
                            'state' => 'A' );

            $preDataPrograms = $langProgramDb->_getFilter($where);
            $validate              = 0;
            $costoProgramasPagados = 0;
            foreach ($preDataPrograms as $c => $program) {
                $programasPagados = 'no';

                //Verificando si ya esta Pre-Matriculado
                $where = array( 'eid'   => $eid,
                                'cid'   => $program['cid'],
                                'perid' => $program['perid'],
                                'subid' => $program['subid'],
                                'tipo'  => $program['tipo'],
                                'pid'   => $pid );
                $attrib = array('state', 'turno');
                $dataAlreadyRegister = $langRegisterCourseDb->_getFilter($where, $attrib);

                $yaNoEntrar = 0;
                if ($dataAlreadyRegister[0]['state'] == 'I') {
                    $alreadyRegister = 'yes';
                    $turnoRegistrado = $dataAlreadyRegister[0]['turno'];
                    $programasPagados = 'yes';
                }elseif($dataAlreadyRegister[0]['state'] == 'M'){
                    $validate++;
                    $programasPagados = 'yes';
                    $yaNoEntrar = 1;
                }else{
                    $alreadyRegister = 'no';
                }
                    //Datos de Tasa
                    $where = array( 'eid'   => $eid,
                                    'cid'   => $program['cid'],
                                    'subid' => $program['subid'],
                                    'tipo'  => $program['tipo'],
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
                            if ($programasPagados == 'yes') {
                                $costoProgramasPagados = $costoProgramasPagados + $costo;
                            }
                        }
                    }
                if ($yaNoEntrar == 0) {
                    if ($num_cuenta) {
                        //Datos de Curso
                        $where = array( 'eid' => $eid,
                                        'cid' => $program['cid'] );
                        $attrib = array('cid', 'name', 'credits', 'prerequisite');

                        $dataCourse = $langCoursesDb->_getFilter($where, $attrib);

                        //Verificar si ya aprobo este curso
                        $where = array( 'eid'   => $eid,
                                        'cid'   => $program['cid'],
                                        'pid'   => $pid,
                                        'state' => 'M' );
                        $attrib = array('notefin');
                        $notaCourse = $langRegisterCourseDb->_getFilter($where, $attrib);
                        $vuelveAEntrar = 'yes';
                        if ($notaCourse) {
                            if ($notaCourse[0]['notefin'] >= 11) {
                                $vuelveAEntrar = 'no';
                            }
                        }
                        if ($dataCourse[0]['prerequisite'] != 0) {
                            $where = array( 'eid'   => $eid,
                                            'cid'   => $dataCourse[0]['prerequisite'],
                                            'pid'   => $pid,
                                            'state' => 'M' );
                            $attrib = array('notefin');
                            $notaCourse = $langRegisterCourseDb->_getFilter($where, $attrib);
                            if ($notaCourse) {
                                if ($notaCourse[0]['notefin'] < 11) {
                                    $vuelveAEntrar = 'no';
                                }
                            }else{
                                $vuelveAEntrar = 'no';
                            }
                        }

                        if ($vuelveAEntrar == 'yes') {
                            $dataPrograms[$c]['courseId']      = $program['cid'];
                            $dataPrograms[$c]['courseName']    = $dataCourse[0]['name'];
                            $dataPrograms[$c]['courseCredits'] = $dataCourse[0]['credits'];
                            $dataPrograms[$c]['coursePre']     = $dataCourse[0]['prerequisite'];
                            $dataPrograms[$c]['costo']         = $costo;
                            $dataPrograms[$c]['studentId']     = $pid;
                            $dataPrograms[$c]['perid']         = $program['perid'];
                            $dataPrograms[$c]['subid']         = $program['subid'];
                            $dataPrograms[$c]['tipo']          = $program['tipo'];

                            $dataPrograms[$c]['alreadyRegister'] = $alreadyRegister;
                            $dataPrograms[$c]['turnoRegistrado'] = $turnoRegistrado;

                             //Datos del Tipo de Curso
                            if ($program['tipo'] == 'N') {
                                $dataPrograms[$c]['tipoName'] = '';
                            }elseif ($program['tipo'] == 'I'){
                                $dataPrograms[$c]['tipoName'] = '(Intensivo)';
                            }elseif ($program['tipo'] == 'S'){
                                $dataPrograms[$c]['tipoName'] = '(Super-Intensivo)';
                            }

                            //Turnos por Programa
                            $where = array( 'eid'   => $eid,
                                            'cid'   => $program['cid'],
                                            'subid' => $program['subid'],
                                            'tipo'  => $program['tipo'],
                                            'perid' => $program['perid'], );
                            $attrib = array('turnoid', 'frecuencia', 'docente_pid');

                            $dataTurnos = $langProgramTurnosDb->_getFilter($where, $attrib);
                            foreach ($dataTurnos as $cProgramTurnos => $turno) {
                                //Datos del Docente
                                $where = array( 'eid' => $eid,
                                                'pid' => $turno['docente_pid'] );
                                $attrib = array('last_name0', 'last_name1', 'first_name');
                                $preDataPerson = $personDb->_getFilter($where, $attrib);

                                $dataPrograms[$c]['turnos'][$cProgramTurnos] = array(   'turnoid'    => $turno['turnoid'],
                                                                                        'frecuencia' => $turno['frecuencia'],
                                                                                        'docente'    => $preDataPerson[0]['last_name0'].' '.
                                                                                                        $preDataPerson[0]['last_name1'].' '.
                                                                                                        $preDataPerson[0]['first_name'] );
                            }
                        }

                    }
                }
            }

            if ($validate == $c + 1 ) {
                $validate = 'yes';
            }else{
                $validate = 'no';
            }

            //Pagos
            $cantCaracteres = strlen($num_cuenta);
            $perid = $periodActive['perid'];
            $where = array( //'code_student'                        => $pid,
                            'perid'                               => (string)$periodActive['perid'],
                            'right(concept, '.$cantCaracteres.')' => $num_cuenta );


            $preDataPayments = $bankReceiptDb->_getFilter($where);
            if ($preDataPayments) {
                $totalPagados = 0;
                foreach ($preDataPayments as $c => $payment) {
                    $totalPagados = $totalPagados + $payment['amount'];
                    $dataPayments['detailPayment'][$c] = array( 'numOperacion' => $payment['operation'],
                                                                'diaPago'      => date('d-m-Y', strtotime($payment['payment_date'])),
                                                                'monto'        => $payment['amount'] );
                }
                $dataPayments['totalPagado']  = $totalPagados;
                $dataPayments['totalGastado'] = $costoProgramasPagados;

                $this->view->dataPayments = $dataPayments;
            }

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

        $eid   = $this->sesion->eid;
        $subid = $this->sesion->subid;

        $formData = $this->getRequest()->getPost();

        //Verificar Si Existe el Usuario
        $where = array( 'eid'   => $eid,
                        'pid'   => $formData['pid'],
                        'subid' => $subid );

        $personData = $langUserDb->_getFilter($where);
        $personExist = '';
        if (!$personData) {
            $dataSaveUser = array(  'eid'   => $eid,
                                    'pid'   => $formData['pid'],
                                    'rid'   => 'AL',
                                    'pwd'   => md5($formData['pid']),
                                    'state' => 'A',
                                    'subid' => $subid );
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
                            'perid' => $formData['perid'],
                            'subid' => $formData['subid'] );

            $registerData = $langRegisterDb->_getFilter($where);

            if (!$registerData) {
                $dataSaveRegister = array(  'eid'     => $eid,
                                            'perid'   => $formData['perid'],
                                            'pid'     => $formData['pid'],
                                            'subid'   => $formData['subid'],
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
                                                    'subid'   => $formData['subid'],
                                                    'tipo'    => $formData['tipo'],
                                                    'created' => date('Y-m-d h:m:s'),
                                                    'state'   => 'I' );
                if ($langRegisterCourseDb->_save($dataSaveRegisterCourses)) {
                    $result = array('success' => 1);
                }else{
                    $result = array('success' => 0);
                }
                print json_encode($result);
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

        $dataCourses = array();
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

            $attrib = array('cid', 'turno', 'tipo');

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
                $dataCourses[$c]['credits'] = $preDataCourse[0]['credits'];
                $dataCourses[$c]['tipo']    = $course['tipo'];

                //Datos del Tipo de Curso
                if ($course['tipo'] == 'N') {
                    $dataCourses[$c]['tipoName'] = 'Normal';
                }elseif ($course['tipo'] == 'I'){
                    $dataCourses[$c]['tipoName'] = 'Intensivo';
                }elseif ($course['tipo'] == 'S'){
                    $dataCourses[$c]['tipoName'] = 'Super-Intensivo';
                }
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
        $langPeriodDb         = new Api_Model_DbTable_LangPeriod();
        $langRegisterDb       = new Api_Model_DbTable_LangRegister();
        $langRegisterCourseDb = new Api_Model_DbTable_LangRegisterCourse();
        $langCoursesDb        = new Api_Model_DbTable_LangCourse();
        $langProgramTurnosDb  = new Api_Model_DbTable_LangProgramTurnos();

        $eid = $this->sesion->eid;
        $pid = $this->sesion->pid;

        $where = array( 'eid'   => $eid,
                        'pid'   => $pid,
                        'state' => 'M' );

        $attrib = '';
        $order = array('perid DESC,');

        $preDataRegisters = $langRegisterDb->_getFilter($where, $attrib, $order);

        if ($preDataRegisters) {
            foreach ($preDataRegisters as $c => $register) {
                //Nombre Periodo
                $where = array( 'eid'   => $eid,
                                'perid' => $register['perid'] );
                $attrib = array('perid', 'name', 'anio');

                $dataPeriod = $langPeriodDb->_getFilter($where, $attrib);

                $dataRegisters[$c]['perid']      = $dataPeriod[0]['perid'];
                $dataRegisters[$c]['periodName'] = $dataPeriod[0]['name'];
                $dataRegisters[$c]['anio']       = $dataPeriod[0]['anio'];

                //Cursos
                $where = array( 'eid'   => $eid,
                                'pid'   => $pid,
                                'perid' => $register['perid'],
                                'state' => 'M' );
                $attrib = array('cid', 'notefin', 'tipo');

                $preDataCourses = $langRegisterCourseDb->_getFilter($where, $attrib);

                foreach ($preDataCourses as $cCourses => $course) {
                    //Datos del Tipo de Curso
                    if ($course['tipo'] == 'N') {
                        $tipoName= 'Normal';
                    }elseif ($course['tipo'] == 'I'){
                        $tipoName = 'Intensivo';
                    }elseif ($course['tipo'] == 'S'){
                        $tipoName = 'Super-Intensivo';
                    }

                    $where = array(
                                    'eid' => $eid,
                                    'cid' => $course['cid'] );

                    $attrib = array('name', 'credits', 'cid');
                    $dataCourse = $langCoursesDb->_getFilter($where, $attrib);
                    $dataRegisters[$c]['courses'][$cCourses] = array(   'cid'        => $dataCourse[0]['cid'],
                                                                        'courseName' => $dataCourse[0]['name'],
                                                                        'tipo'       => $course['tipo'],
                                                                        'tipoName'   => $tipoName,
                                                                        'credits'    => $dataCourse[0]['credits'],
                                                                        'nota'       => $course['notefin'] );
                }

                $dataRegisters[$c]['cantCourses'] = $cCourses + 1;
                if ($dataRegisters[$c]['cantCourses'] == 1) {
                    $dataRegisters[$c]['cantCourses'] = $dataRegisters[$c]['cantCourses'].' Curso';
                }else{
                    $dataRegisters[$c]['cantCourses'] = $dataRegisters[$c]['cantCourses'].' Cursos';
                }
            }

            $this->view->dataRegisters = $dataRegisters;
        }


    }

}