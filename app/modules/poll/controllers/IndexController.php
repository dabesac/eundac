<?php

class Poll_IndexController extends Zend_Controller_Action {

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
    
    public function indexAction()
    {
        try {
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $poll = new Api_Model_DbTable_Polll();
            $all_data = $poll->_getAll($where=array('eid' => $eid, 'oid' => $oid), $order='', $start=0, $limit=0);
            $this->view->poll = $all_data;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function newAction()
    {
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $form = new Poll_Form_Poll();
            if ($this->getRequest()->isPost()) {
                $formData = array();
                $formData = $this->getRequest()->getPost();
                $formData['state'] = 'A';
                if ($form->isValid($formData)){
                    $formData['eid'] = $eid;
                    $formData['oid'] = $oid;
                    $formData['register'] = $this->sesion->uid;
                    $poll = new Api_Model_DbTable_Polll();
                    $poll->_save($formData);
                    $this->view->success=1;
                }
            }
            $this->view->form=$form;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function updateAction()
    {
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $pollid = $this->_getParam('pollid');
            $this->view->pollid = $pollid;
            $poll = new Api_Model_DbTable_Polll();
            $data_poll = $poll->_getOne($data=array('eid' => $eid, 'oid' => $oid, 'pollid' => $pollid));
            $form = new Poll_Form_Poll();
            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)){
                    $pk = array('eid' => $eid, 'oid' => $oid, 'pollid' => $formData['pollid']);
                    $poll->_update($formData,$pk);
                    $this->view->success=1;
                }else{
                    $form->populate($formData);
                    $this->view->form=$form;
                }
            }else{
                $form->populate($data_poll);
                $this->view->form=$form;
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function questionsAction(){
        try {
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $pollid = base64_decode($this->_getParam('pollid'));
            $this->view->pollid = $pollid;
            $poll = new Api_Model_DbTable_Polll();
            $data_poll = $poll->_getOne($data=array('eid' => $eid, 'oid' => $oid, 'pollid' => $pollid));
            $this->view->data_poll = $data_poll;

            $ques = new Api_Model_DbTable_PollQuestion();
            $data_ques = $ques->_getFilter($data,$attrib=null,$orders=array('position'));
            $this->view->data_ques = $data_ques;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function newquestionAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $pollid = $this->_getParam('pollid');
            $this->view->pollid = $pollid;
            $form = new Poll_Form_Question();
            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                $formData['state'] = 'A';
                if ($form->isValid($formData)){
                    $formData['eid'] = $eid;
                    $formData['oid'] = $oid;
                    $where = array('eid' => $eid, 'oid' => $oid, 'pollid' => $formData['pollid']);
                    $ques = new Api_Model_DbTable_PollQuestion();
                    $cant_ques = count($ques->_getPreguntasXencuesta($where));
                    $formData['position'] = ++$cant_ques;
                    
                    $alternatives = array();
                    for ($i=1; $i <= 5; $i++) { 
                        $alternatives[$i] = $formData['alternative'.$i];
                        unset($formData['alternative'.$i]);
                    }
                    $ques->_save($formData);
                    $whereques = array('eid' => $eid, 'oid' => $oid, 'position' => $formData['position']);
                    $question = $ques->_getFilter($whereques,$attrib=null,$orders=null);

                    if ($question) {
                        $alt = new Api_Model_DbTable_PollAlternatives();
                        $wherealt = array('eid' => $eid, 'oid' => $oid, 'qid' => $question[0]['qid']);
                        for ($i=1; $i <= 5; $i++) { 
                            if ($alternatives[$i] != "") {
                                $cant_alt = count($alt->_getAll($wherealt));
                                $data = array(
                                    'eid' => $eid, 'oid' => $oid, 'qid' => $question[0]['qid'],
                                    'alternative' => $alternatives[$i], 'position' => $cant_alt+1);
                                $alt->_save($data);
                            }
                            else $i=6;
                        }
                    }
                    $this->view->success=1;
                }
            }
            $this->view->form = $form;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function updatequestionAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $qid = $this->_getParam('qid');
            $this->view->qid = $qid;
            $where = array('eid' => $eid, 'oid' => $oid, 'qid' => $qid);
            $ques = new Api_Model_DbTable_PollQuestion();
            $data_ques = $ques->_getOne($where);

            $alt = new Api_Model_DbTable_PollAlternatives();
            $data_alt = $alt->_getFilter($where,$attrib=null,$orders=array('position'));
            $this->view->alternatives = $data_alt;
            $form = new Poll_Form_Question();
            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)){
                    $pk = array('eid' => $eid, 'oid' => $oid, 'qid' => $formData['qid']);
                    $data = array('question' => $formData['question'], 'type' => $formData['type'], 'state' => $formData['state']);
                    $ques->_update($data, $pk);
                    unset($formData['question']);
                    unset($formData['type']);
                    unset($formData['state']);
                    $qid = $formData['qid']; 
                    unset($formData['qid']);

                    foreach ($formData as $key => $value) {
                        if (substr($key, 0, 11) != "alternative") {
                            $pk = array('eid' => $eid, 'oid' => $oid, 'atlid' => $key, 'qid' => $qid);
                            $data = array('alternative' => $formData[$key]);
                            $alt->_update($data,$pk);
                        }else{
                            if ($formData[$key] != "") {
                                $wherealt = array('eid' => $eid, 'oid' => $oid, 'qid' => $qid);
                                $cant_alt = $alt->_getFilter($wherealt,$attrib=null,$orders=array('position'));
                                $number = $cant_alt[count($cant_alt)-1]['position']+1;
                                $data = array(
                                    'eid' => $eid, 'oid' => $oid, 'qid' => $qid,
                                    'alternative' => $formData[$key], 'position' => $number);
                                $alt->_save($data);
                            }
                        }
                    }
                    $this->view->success=1;
                }else{
                    $form->populate($data_ques);
                    $this->view->form = $form;
                }
            }else{
                $form->populate($data_ques);
                $this->view->form = $form;
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function deletealternativeAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $atlid = $this->_getParam('atlid');
            $qid = $this->_getParam('qid');
            $alt = new Api_Model_DbTable_PollAlternatives();
            $alt->_delete($data=array('eid' => $eid, 'oid' => $oid, 'atlid' => $atlid, 'qid' => $qid));
            $this->view->success=1;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}
