<?php
class Acreditacion_InfosillabusController extends Zend_Controller_Action{

	public function init(){
		$sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
	}
	public function indexAction(){
		try {
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$escid=$this->sesion->escid;
			$subid=$this->sesion->subid;
			$perid=$this->sesion->period->perid;
			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'perid'=>$perid);

			$bdcourses=new Api_Model_DbTable_Course();
			$bdteacher=new Api_Model_DbTable_PeriodsCourses();
			$bdcontrol=new Api_Model_DbTable_ControlActivity();

			$data=$bdteacher->_getTeacherXPeridXEscid1($where);
			// unset($where['perid']);
			foreach ($data as $key => $teachers) {
				$where['courseid']=$teachers['courseid'];
				// $datacourse=$bdcourses->_getFilter($where);
				// $data[$key]['namecourse']=$datacourse[0]['name'];
				$datacontrol=$bdcontrol->_getFilter($where);
				print_r($datacontrol);
				exit();
			}
			// $len=count($data);
			// $j=0;
			// for ($i=0; $i < $len; $i++) { 
			// 	if ($data[$i]['uid']==$data[$i+1]['uid']) {
			// 		$datafinal[$j]=$data[$i];
			// 		$j++;
			// 	}
			// }
			$this->view->data=$data;
		} catch (Exception $e) {
			
		}
	}
}