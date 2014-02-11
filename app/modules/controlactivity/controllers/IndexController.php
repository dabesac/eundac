<?php

class Controlactivity_IndexController extends Zend_Controller_Action {

    public function init()
    {
      $sesion  = Zend_Auth::getInstance();
      if(!$sesion->hasIdentity() ){
        $this->_helper->redirector('index',"index",'default');
      }
      $login = $sesion->getStorage()->read();
      if (!$login->rol['module']=="docente"){
        $this->_helper->redirector('index','index','default');
      }
      $this->sesion = $login;   
    }
    
    public function indexAction()
    {
        try{
            $controlsyllabusDb = new Api_Model_DbTable_ControlActivity();
            $contentsyllabusDb = new Api_Model_DbTable_Syllabusunitcontent();

            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $subid = base64_decode($this->getParam('subid'));
            $curid = base64_decode($this->getParam('curid'));
            $courseid = base64_decode($this->getParam('courseid'));
            $turno = base64_decode($this->getParam('turno'));
            $perid = base64_decode($this->getParam('perid'));

            $attrib = array('session');
            $where = array('eid' => $eid, 
                            'oid'=>$oid, 
                            'perid'=>$perid, 
                            'subid'=>$subid, 
                            'courseid'=>$courseid, 
                            'curid'=>$curid,
                            'turno'=>$turno);
              
            $controlsyllabus = $controlsyllabusDb->_getFilter($where, $attrib);

            if ($controlsyllabus) {
                $c = 0;    
                foreach ($controlsyllabus as $session) {
                    $c++;
                }
                print_r($c);
                $index = 0;
                for ($i = $c; $i < $c + 3; $i++) { 
                    $attrib = array('perid', 'subid', 'courseid', 'curid', 'turno', 'unit', 'session', 'week', 'obj_content');
                    $where = array('eid' => $eid, 
                                'oid'=>$oid, 
                                'perid'=>$perid, 
                                'subid'=>$subid, 
                                'courseid'=>$courseid, 
                                'curid'=>$curid,
                                'turno'=>$turno,
                                'session'=>$i);
                    $contentsyllabus[$index] = $contentsyllabusDb->_getFilter($where, $attrib);
                    $index++;
                }
                $this->view->firstTime = "No";
            }else{
                $session = 1;
                $c = 0;
                for ($i=0; $i <= 1 ; $i++) { 
                    $attrib = array('perid', 'subid', 'courseid', 'curid', 'turno', 'unit', 'session', 'week', 'obj_content');
                    $where = array('eid' => $eid, 
                                'oid'=>$oid, 
                                'perid'=>$perid, 
                                'subid'=>$subid, 
                                'courseid'=>$courseid, 
                                'curid'=>$curid,
                                'turno'=>$turno,
                                'session'=>$i + 1);
                    $contentsyllabus[$i] = $contentsyllabusDb->_getFilter($where, $attrib);
                    
                }

                $this->view->firstTime = "Si";
            }
            $this->view->contentsyllabus = $contentsyllabus;
        }
            catch (Exception $e) {           
        }
    }

    public function saveAction()
    {
        try {
            $controlsyllabusDb = new Api_Model_DbTable_ControlActivity();

            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $subid = base64_decode($this->getParam('subid'));
            $curid = base64_decode($this->getParam('curid'));
            $courseid = base64_decode($this->getParam('courseid'));
            $turno = base64_decode($this->getParam('turno'));
            $unit = base64_decode($this->getParam('unit'));
            $session = base64_decode($this->getParam('session'));
            $week = base64_decode($this->getParam('week'));
            $perid = base64_decode($this->getParam('perid'));

            $where = array('eid' => $eid, 
                            'oid'=>$oid, 
                            'perid'=>$perid, 
                            'subid'=>$subid, 
                            'courseid'=>$courseid, 
                            'curid'=>$curid,
                            'turno'=>$turno,
                            'unit'=>$unit,
                            'week'=>$week,
                            'session'=>$session);

            $where['datecheck'] = date('Y-m-d');
            $where['state'] = 'D';

            $save = $controlsyllabusDb->_save($where);
            if ($save) {
                $this->_redirect('/controlactivity/index/index/courseid/'.base64_encode($courseid).'/curid/'.base64_encode($curid).'/turno/'.base64_encode($turno).'/perid/'.base64_encode($perid).'/subid/'.base64_encode($subid));
            }

        } catch (Exception $e) {
            print 'Error'.$e->getMessage();
        }
    }
}