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
	}


}