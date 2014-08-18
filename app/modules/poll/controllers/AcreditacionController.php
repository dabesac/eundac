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
        
        $uid      = $this->sesion->uid;
        $escid    = $this->sesion->escid;
        $perid    = $this->sesion->period->perid;
        $fullName = $this->sesion->infouser['fullname'];

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
        $attributes  = array('id', 'name', 'fuente_id', 'objective');
        $preDataEncuesta = $server->read($idEncuesta, $attributes, 'poll.undac');

        $dataEncuesta = array();

        if ($preDataEncuesta) {
            $dataEncuesta['id']          = $preDataEncuesta[0]['id'];
            $dataEncuesta['name']        = utf8_encode($preDataEncuesta[0]['name']);
            $dataEncuesta['objetive']    = utf8_encode($preDataEncuesta[0]['objective']);
            $dataEncuesta['source']      = utf8_encode($preDataEncuesta[0]['fuente_id'][1]);
            $dataEncuesta['codeStudent'] = $uid;
            $dataEncuesta['fullName']    = $fullName;
            $dataEncuesta['escid']       = $escid;

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

            foreach ($preDataQuestions as $c => $question) {
                $positionQuestion[$c] = $question['position'];
            }
            array_multisort($positionQuestion, SORT_ASC, $preDataQuestions);

            foreach ($preDataQuestions as $c => $question) {
                $dataEncuesta['questions'][$c] = array( 'id'   => $question['id'],
                                                        'name' => utf8_encode($question['name']) );

                //alternativas por preguntas
                $query = array(
                                array(  'column'   => 'questions_id',
                                        'operator' => '=',
                                        'value'    =>  $question['id'],
                                        'type'     => 'int' ),

                                array(  'column'   => 'state',
                                        'operator' => '=',
                                        'value'    =>  'A',
                                        'type'     => 'string' ),
                            );
                $idsAlternatives = $server->search('poll.alternatives', $query);
                $attributes = array('id', 'position', 'name');
                $preDataAlternatives = $server->read($idsAlternatives, $attributes, 'poll.alternatives');
                foreach ($preDataAlternatives as $cAlternatives => $alternative) {
                    $positionAlternatives[$cAlternatives] = $alternative['position'];
                }
                array_multisort($positionAlternatives, SORT_ASC, $preDataAlternatives);
                $indiceAlternativas = 'a';
                //$alphas = range('A', 'Z');
                foreach ($preDataAlternatives as $cAlternatives => $alternative) {
                    $dataEncuesta['questions'][$c]['alternatives'][$cAlternatives] = array( 'id'       => $alternative['id'],
                                                                                            'indice'   => $indiceAlternativas,
                                                                                            'name'     => utf8_encode($alternative['name']),
                                                                                            'position' => $alternative['position'] );
                    $indiceAlternativas++;
                }
            }
            $dataEncuesta['cantQuestions'] = $c;
        }
        $this->view->dataEncuesta = $dataEncuesta;
    }

    public function sendpollAction(){
        $this->_helper->layout->disableLayout();

        $formData = $this->getRequest()->getPost();

        if ($formData) {
            $amountQuestions = $formData['cantPreguntas'];
            for ($i=0; $i < $amountQuestions; $i++) { 
                $dataSend[$i] = array(  'code'                => $formData['code'],
                                        'name'                => trim($formData['name']),
                                        'escid'               => $formData['escid'],
                                        'pollid'              => $formData['pollid'],
                                        'question_id'         => $formData['question'.$i],
                                        ['alternative_id'][0] => array($formData['alternative'.$i]) );
            }
            $dataSend['dataPoll'] = json_encode($dataSend);
            print_r($dataSend);
        }
    }

}