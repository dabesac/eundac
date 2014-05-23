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
        //Databases

    }

    public function matriculaAction(){
        $this->_helper->layout()->disableLayout();

        //dataBases
        $langPeriodDb        = new Api_Model_DbTable_LangPeriod();
        $langProgramDb       = new Api_Model_DbTable_LangProgram();
        $langCoursesDb       = new Api_Model_DbTable_LangCourse();
        $langTasaDb          = new Api_Model_DbTable_LangTasa();
        $langProgramTasasDb  = new Api_Model_DbTable_LangProgramTasas();
        $langProgramTurnosDb = new Api_Model_DbTable_LangProgramTurnos();

        $eid = $this->sesion->eid;
        $rid = $this->sesion->rid;

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
            foreach ($preDataPrograms as $c => $program) {
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
                    $dataPrograms[$c]['num_cuenta']    = $num_cuenta;
                    $dataPrograms[$c]['costo']         = $costo;

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
            $this->view->dataPrograms = $dataPrograms;
        }

    }

    public function recordregistersAction(){
        $this->_helper->layout()->disableLayout();
    }

    public function matriculaactualAction(){
        $this->_helper->layout()->disableLayout();
    }

}