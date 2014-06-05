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
                            'perid' => $periodActive['perid'],
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
                    $yaNoEntrar = 1;
                }else{
                    $alreadyRegister = 'no';
                }
                if ($yaNoEntrar == 0) {
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
                            if ($programasPagados == 'yes') {
                                $costoProgramasPagados = $costoProgramasPagados + $costo;
                            }
                        }
                    }
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

                            $dataPrograms[$c]['alreadyRegister'] = $alreadyRegister;
                            $dataPrograms[$c]['turnoRegistrado'] = $turnoRegistrado;

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
            $where = array( 'code_student'                        => $pid,
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
                                            'subid'   => $subid,
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
                                                    'subid'   => $subid,
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
        $langPeriodDb         = new Api_Model_DbTable_LangPeriod();
        $langRegisterDb       = new Api_Model_DbTable_LangRegister();
        $langRegisterCourseDb = new Api_Model_DbTable_LangRegisterCourse();
        $langCoursesDb        = new Api_Model_DbTable_LangCourse();
        $langProgramTurnosDb  = new Api_Model_DbTable_LangProgramTurnos();

        $eid = $this->sesion->eid;
        $pid = $this->sesion->pid;

        $where = array( 'eid' => $eid,
                        'pid' => $pid );

        $preDataRegisters = $langRegisterDb->_getFilter($where);

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
                $attrib = array('cid', 'notefin');

                $preDataCourses = $langRegisterCourseDb->_getFilter($where, $attrib);

                foreach ($preDataCourses as $cCourses => $course) {
                    $where = array(
                                    'eid' => $eid,
                                    'cid' => $course['cid'] );

                    $attrib = array('name', 'credits', 'cid');
                    $dataCourse = $langCoursesDb->_getFilter($where, $attrib);
                    $dataRegisters[$c]['courses'][$cCourses] = array(   'cid'        => $dataCourse[0]['cid'],
                                                                        'courseName' => $dataCourse[0]['name'],
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