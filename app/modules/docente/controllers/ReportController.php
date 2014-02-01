<?php

class Docente_ReportController extends Zend_Controller_Action {

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

    public function indexAction(){
        try {
            $this->_helper->layout()->disableLayout();
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function historyAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $uid = $this->sesion->uid;
            $pid = $this->sesion->pid;
            $anio = $this->_getParam('anio');
            $per = new Api_Model_DbTable_Periods();
            $data_per = $per->_getPeriodsxYears($data = array('eid' => $eid, 'oid' => $oid, 'year' => substr($anio,2,3)));
            if ($data_per) {
                $tam = count($data_per);
                $doc = new Api_Model_DbTable_Coursexteacher();
                for ($i=0; $i < $tam; $i++) { 
                    $aca = 0;
                    $lab = 0;

                    $where = array(
                        'eid' => $eid, 'oid' => $oid, 'pid' => $pid,
                        'perid' => $data_per[$i]['perid']);
                    $data_doc = $doc->_getFilter($where,$attrib=null,$orders=array('courseid','turno'));
                    if ($data_doc) $data_per[$i]['courses'] = $data_doc;
                    else $aca = 1;

                    $adm = new Distribution_Model_DbTable_DistributionAdmin();
                    $data_adm = $adm->_getFilter($where,$atrib=array());
                    if ($data_adm) $data_per[$i]['admin'] = $data_adm;
                    else $lab = 1;

                    if ($lab == 1 && $aca == 1) $data_per[$i]['data'] = 0;
                    else $data_per[$i]['data'] = 1;
                }
            }
            $this->view->periods = $data_per;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}