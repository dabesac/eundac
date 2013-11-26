<?php

class Graduated_IndexController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (!$login->rol['module']=="graduated"){
    		$this->_helper->redirector('index','index','default');
    	}
    	$this->sesion = $login;

        if ($this->_verifyprofile() == "1"){
            $this->_redirect('/profile/public/student');
        }
    }
    
    public function indexAction()
    {       

    }

    public function _verifyprofile(){
        try {
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $pid = $this->sesion->pid;
            $uid = $this->sesion->uid;
            $escid = $this->sesion->escid;
            $subid = $this->sesion->subid;

            $where_stadis = array(
                'eid' => $eid, 'oid' => $oid, 'pid' => $pid, 'uid' => $uid, 'escid' => $escid, 'subid' => $subid);
            $stadis= new Api_Model_DbTable_Statistics();
            $data_stadis = $stadis->_getOne($where_stadis);

            $where_rel = array('eid' => $eid, 'pid' => $pid);
            $rel = new Api_Model_DbTable_Relationship();
            $data_rel = $rel->_getFilter($where_rel, $attrib=null, $orders=null);

            $acad = new Api_Model_DbTable_Academicrecord();
            $data_acad = $acad->_getFilter($where_rel, $attrib=null, $orders=null);

            $jobs = new Api_Model_DbTable_Jobs();
            $data_jobs = $jobs->_getFilter($where_rel, $attrib=null, $orders=null);

            $inter = new Api_Model_DbTable_Interes();
            $data_inter = $inter->_getFilter($where_rel, $attrib=null, $orders=null);

            if (!$data_acad || !$data_stadis || !$data_rel || !$data_inter || !$data_jobs) {
                return "1";
            }else{
                return "0";
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}
