<?php
class Admin_OpendistributionController extends Zend_Controller_Action{
	public function indexAction(){
		$eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $where=array('eid'=>$eid,'oid'=>$oid);
        $dbfaculty=new Api_Model_DbTable_Faculty();
        $dataf=$dbfaculty->_getAll($where);
        $this->view->dataf=$dataf;

        $perid=$this->sesion->period->perid;
        $this->view->perid=$perid;
	}

}