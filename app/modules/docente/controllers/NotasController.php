<?php
class Docente_NotasController extends Zend_Controller_Action{

	public function init(){
		$sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		
 		$this->sesion = $login;
	}

	public function indexAction(){
		// Periods Now
		$where['uid']=$this->sesion->uid;
		$where['perid']=$this->sesion->period->perid;
		$where['rid']=$this->sesion->rid;
		$where['is_main']='S';
		$this->view->perid= $this->sesion->period->perid;
		$docente = new Api_Model_DbTable_PeriodsCourses();
		$data = $docente->_getCourseTeacher($where);
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
		
		
		// Periods Later
		$where['uid']=$this->sesion->uid;
		$where['perid']="13A";
		$where['rid']=$this->sesion->rid;
		$where['is_main']='S';
		$docente_ = new Api_Model_DbTable_PeriodsCourses();
		$data_ = $docente_->_getCourseTeacher($where);
		$l_=count($data_);
		$faculty_=array();
		
		$a_=0;
		$faculty_[$a_]['facid']=$data_[0]['facid'];
		$faculty_[$a_]['name']=$data_[0]['name'];
		for ($i_=0; $i_ < $l_ ; $i_++) {
			if (($faculty_[$a_]['facid'])!= $data_[$i_]['facid']) {
				$a_++;
				$faculty_[$a_]['facid']=$data_[$i_]['facid'];
				$faculty_[$a_]['name']=$data_[$i_]['name'];
			}
		}
		$this->view->faculty_=$faculty_;
		$this->view->data_=$data_;
	}


}