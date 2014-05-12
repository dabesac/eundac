<?php

class Controlactivit_CalendarController extends Zend_Controller_Action {

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

        }
            catch (Exception $e) {           
        }
    }
    public function datajsonAction(){

        $tb_horary = new Api_Model_DbTable_Horary();
        $where = array(
            'eid'=>$this->sesion->eid,
            'oid'=>$this->sesion->oid,
            'uid'=>$this->sesion->uid,
            'pid'=>$this->sesion->pid,
            'perid'=>$this->sesion->period->perid
            ); 
        $horary_syllabus = $tb_horary->_get_horary_x_syllabus($where);
        $where_1 = array(   
            'eid'=>$this->sesion->eid,
            'oid'=>$this->sesion->oid,
            'perid'=>$this->sesion->period->perid,
            );
        $tb_periods = new Api_Model_DbTable_Periods();
        $data_period = $tb_periods->_getOnePeriod($where_1);
        $data = array(
            'content'=>$horary_syllabus,
            'period'=>$data_period
            );
        $this->_helper->layout()->disableLayout();
        $this->_response->setHeader('Content-Type', 'application/json'); 
        $this->view->data = Zend_Json::encode($data);
    }
    public function saveAction()
    {
        try {
            $controlsyllabusDb = new Api_Model_DbTable_ControlActivity();

            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $subid    = base64_decode($this->getParam('subid'));
            $escid    = base64_decode($this->getParam('escid'));
            $curid    = base64_decode($this->getParam('curid'));
            $courseid = base64_decode($this->getParam('courseid'));
            $turno    = base64_decode($this->getParam('turno'));
            $unit     = base64_decode($this->getParam('unit'));
            $session  = base64_decode($this->getParam('session'));
            $week     = base64_decode($this->getParam('week'));
            $perid    = base64_decode($this->getParam('perid'));

            $where = array( 'eid'      => $eid, 
                            'oid'      => $oid, 
                            'perid'    => $perid, 
                            'escid'    => $escid, 
                            'subid'    => $subid, 
                            'courseid' => $courseid, 
                            'curid'    => $curid,
                            'turno'    => $turno,
                            'unit'     => $unit,
                            'week'     => $week,
                            'session'  => $session);

            $where['datecheck'] = date('Y-m-d');
            $where['state'] = 'D';

            $save = $controlsyllabusDb->_save($where);
            if ($save) {
                $this->_redirect('/controlactivity/index/index/courseid/'.base64_encode($courseid).'/curid/'.base64_encode($curid).'/turno/'.base64_encode($turno).'/perid/'.base64_encode($perid).'/subid/'.base64_encode($subid).'/escid/'.base64_encode($escid));
            }

        } catch (Exception $e) {
            print 'Error'.$e->getMessage();
        }
    }


}