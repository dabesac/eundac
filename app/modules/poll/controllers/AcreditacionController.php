<?php

class Poll_AcreditacionController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	// if (!$login->modulo=="poll"){
    		// $this->_helper->redirector('index','index','default');
    	// }
    	$this->sesion = $login;
    }

    public function indexAction(){
        $server = new Eundac_Connect_openerp();

        $this->_helper->layout()->disableLayout();

        $perid = $this->sesion->period->perid;

        $query = array(
                    array(  'column'   => 'perid',
                            'operator' => '=',
                            'value'    =>  $perid,
                            'type'     => 'string' ),

                    array(  'column'   => 'state',
                            'operator' => '=',
                            'value'    =>  'P',
                            'type'     => 'string' ),
                );
        $idEncuesta   = $server->search('poll.undac', $query);
        $attributes  = array('id', 'name', 'fuente_id', 'objetive');
        $preDataEncuesta = $server->read($idEncuesta, $attributes, 'poll.undac');

        if ($preDataEncuesta) {
            $dataEncuesta['id']     = $preDataEncuesta[0]['id'];
            $dataEncuesta['name']   = utf8_encode($preDataEncuesta[0]['name']);
            $dataEncuesta['fuente'] = utf8_encode($preDataEncuesta[0]['fuente_id'][1]);

            $query = array(
                            array(  'column'   => 'poll_id',
                                    'operator' => '=',
                                    'value'    =>  $dataEncuesta['id'],
                                    'type'     => 'int' ),

                            array(  'column'   => 'state',
                                    'operator' => '=',
                                    'value'    =>  'A',
                                    'type'     => 'string' ),
                        );
            $idsQuestion = $server->search('poll.questions', $query);
            $attributes = array('id', 'position', 'name');
            $preDataQuestions = $server->read($idsQuestion, $attributes, 'poll.questions');

            print_r($preDataQuestions);

            print_r("<br><br><br>");

            $menorPosition = $preDataQuestions[0]['position'];
            $dataEncuesta['questions'][0] = array( 'id'       => $preDataQuestions[0]['id'],
                                                    'name'     => $preDataQuestions[0]['name'],
                                                    'position' => $preDataQuestions[0]['position'] );
            $cQuestionsFix = 0;
            foreach ($preDataQuestions as $c => $question) {
                if ($question['position'] < $menorPosition) {
                    $menorPosition = $question['position'];

                    $interruptorMenor = 0;
                    foreach ($dataEncuesta['questions'] as $cQuestions => $questionFix) {
                        if ($menorPosition < $questionFix['position'] and $interruptorMenor != 1) {
                            $idQuestion       = $questionFix['id'];
                            $nameQuestion     = $questionFix['name'];
                            $positionQuestion = $questionFix['position'];
                            $dataEncuesta['questions'][$cQuestions] = array('id'       => $question['id'],
                                                                            'name'     => $question['name'],
                                                                            'position' => $question['position'] );
                            $interruptorMenor = 1;
                            $cQuestionsFix = $cQuestions + 1;
                        }
                    }
                    print_r($menorPosition);
                    $dataEncuesta['questions'][$cQuestions + 1] = array('id'       => $idQuestion,
                                                                        'name'     => $nameQuestion,
                                                                        'position' => $positionQuestion );
                }elseif ($menorPosition != $question['position']){  
                    $dataEncuesta['questions'][$cQuestionsFix] = array( 'id'       => $question['id'],
                                                                        'name'     => $question['name'],
                                                                        'position' => $question['position'] );
                    $cQuestionsFix++;
                }
            }
            
            print_r($dataEncuesta['questions']);
        }
    }

}