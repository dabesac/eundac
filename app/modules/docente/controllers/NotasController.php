<?php
class Docente_NotasController extends Zend_Controller_Action{

	public function init(){
		$this->eid='20154605046';
		$this->oid='1';
		$this->perid='13A';
		$this->uid='04000119DC';
		$this->rid='DC';
		$this->is_main='S';

	}

	public function indexAction(){
		$where['uid']=$this->uid;
		$where['perid']=$this->perid;
		$where['rid']=$this->rid;
		$where['is_main']=$this->is_main;
		$docente = new Api_Model_DbTable_PeriodsCourses();
		$data = $docente->_getCourseTeacher($where);
		print_r($data);
		$l=count($data);
		$faculty=array();
		$a=0;
		$faculty[$a]['facid']=$data[0]['facid'];
		$faculty[$a]['name']=$data[0]['name'];
		for ($i=0; $i < $l ; $i++) { 
			if (($faculty[$a]['facid'])!= $data[$i]['facid']) {
				$a++;
				$faculty[$a]['facid']=$data[$i]['facid']; 
				$faculty[$a]['name']=$data[$i]['name']; 
			}
		}
		$this->view->faculty=$faculty;
		$this->view->data=$data;
	}


}