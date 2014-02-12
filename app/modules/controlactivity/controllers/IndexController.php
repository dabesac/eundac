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
            $coursesDb = new Api_Model_DbTable_Course();

            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $subid = base64_decode($this->getParam('subid'));
            $curid = base64_decode($this->getParam('curid'));
            $courseid = base64_decode($this->getParam('courseid'));
            $turno = base64_decode($this->getParam('turno'));
            $perid = base64_decode($this->getParam('perid'));

            $where = array('eid'=>$eid, 'oid'=>$oid, 'courseid'=>$courseid, 'curid'=>$curid);
            $attrib = array('name');
            $coursename = $coursesDb->_getFilter($where, $attrib);
            $this->view->coursename = $coursename;

            $attrib = array('session');
            $where = array('eid' => $eid, 
                            'oid'=>$oid, 
                            'perid'=>$perid, 
                            'subid'=>$subid, 
                            'courseid'=>$courseid, 
                            'curid'=>$curid,
                            'turno'=>$turno);
              
            $controlsyllabus = $controlsyllabusDb->_getFilter($where, $attrib);
            $contentsyllabus = $contentsyllabusDb->_getFilter($where, $attrib);

            $c = 0;    
            $index = 0;
            $firstTime = "Si";
            if ($controlsyllabus) {
                foreach ($controlsyllabus as $session) {
                    $c++;
                }
                $this->view->realizedSession = $c;

                for ($i = 0; $i < $c; $i++) { 
                    $attrib = array('perid', 'subid', 'courseid', 'curid', 'turno', 'unit', 'session', 'week', 'obj_content');
                    $where = array('eid' => $eid, 
                                'oid'=>$oid, 
                                'perid'=>$perid, 
                                'subid'=>$subid, 
                                'courseid'=>$courseid, 
                                'curid'=>$curid,
                                'turno'=>$turno,
                                'session'=>$controlsyllabus[$i]['session']);
                    $contentsyllabus[$index] = $contentsyllabusDb->_getFilter($where, $attrib);

                    $attrib = array('datecheck');
                    $where = array('eid' => $eid, 
                                'oid'=>$oid, 
                                'perid'=>$perid, 
                                'subid'=>$subid, 
                                'courseid'=>$courseid, 
                                'curid'=>$curid,
                                'turno'=>$turno,
                                'session'=>$controlsyllabus[$i]['session']);
                    $controlsyllabus[$index] = $controlsyllabusDb->_getFilter($where, $attrib);

                    $index++;
                }
                $firstTime = "No";
            }

            $attrib = array('perid', 'subid', 'courseid', 'curid', 'turno', 'unit', 'session', 'week', 'obj_content');
            $where = array('eid' => $eid, 
                        'oid'=>$oid, 
                        'perid'=>$perid, 
                        'subid'=>$subid, 
                        'courseid'=>$courseid, 
                        'curid'=>$curid,
                        'turno'=>$turno,
                        'session'=>$contentsyllabus[$index]['session']);
            $contentsyllabus[$index] = $contentsyllabusDb->_getFilter($where, $attrib);


            if ($contentsyllabus[$index] == null) {
                $completeSyllabus = 'Si';
            }else{
                $completeSyllabus = 'No';
            }
            $index++;

            $c = 0;    
            foreach ($contentsyllabus as $session) {
                $c++;
            }
            $this->view->restSession = $c;

            $finalSession = 0;
            for ($i = $index; $i < $c ; $i++) { 
                //echo $i.' ';
                $attrib = array('perid', 'subid', 'courseid', 'curid', 'turno', 'unit', 'session', 'week', 'obj_content');
                $where = array('eid' => $eid, 
                            'oid'=>$oid, 
                            'perid'=>$perid, 
                            'subid'=>$subid, 
                            'courseid'=>$courseid, 
                            'curid'=>$curid,
                            'turno'=>$turno,
                            'session'=>$contentsyllabus[$i]['session']);
                $contentsyllabus[$index] = $contentsyllabusDb->_getFilter($where, $attrib);
                $index++;
                $finalSession = 1;
            }

            $this->view->finalSession = $finalSession;

            $this->view->firstTime = $firstTime;
            print_r($completeSyllabus);
            $this->view->completeSyllabus = $completeSyllabus;
         
            $this->view->contentsyllabus = $contentsyllabus;
            $this->view->controlsyllabus = $controlsyllabus;
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