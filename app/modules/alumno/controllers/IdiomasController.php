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
        $langPeriodDb  = new Api_Model_DbTable_LangPeriod();
        $langProgramDb = new Api_Model_DbTable_LangProgram();
        $langCoursesDb = new Api_Model_DbTable_LangCourse();

        $eid = $this->sesion->eid;

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

            $dataPrograms = $langProgramDb->_getFilter($where);
            $sendProgram = '';
            $anotherTurn = 'no';
            foreach ($dataPrograms as $c => $program) {

                foreach ($sendProgram as $send) {
                    if ($program['cid'] == $send['courseId']) {
                        $anotherTurn = 'yes';
                    }
                }
                $where = array( 'eid' => $eid,
                                'cid' => $program['cid'] );
                $attrib = array('name', 'credits', 'prerequisite');

                $dataCourse = $langCoursesDb->_getFilter($where, $attrib);
                $sendProgram[$c]['courseId']      = $program['cid'];
                $sendProgram[$c]['courseName']    = $dataCourse[0]['name'];
                $sendProgram[$c]['courseCredits'] = $dataCourse[0]['credits'];
                $sendProgram[$c]['coursePre']     = $dataCourse[0]['prerequisite'];
                $sendProgram[$c]['price']         = $program['amount'];
                $sendProgram[$c]['perid']         = $periodActive['perid'];
            }

            $this->view->programs = $sendProgram;
        }

    }

    public function recordregistersAction(){
        $this->_helper->layout()->disableLayout();
    }

    public function matriculaactualAction(){
        $this->_helper->layout()->disableLayout();
    }

}