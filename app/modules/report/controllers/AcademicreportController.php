<?php
class Report_AcademicreportController extends Zend_Controller_Action{
	public function init(){
		$sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		$this->sesion = $login;
	}

	public function indexAction(){
		try{
			$eid = $this->sesion->eid;
			$oid = $this->sesion->oid;
			$where['eid'] = $eid;
			$where['oid'] = $oid;
			$anio =date('Y');
			$confaculty = new Api_Model_DbTable_Faculty();
			$datafac = $confaculty ->_getfilter($where);

			$this->view->faculty = $datafac;
			$this->view->anio = $anio;	
		} catch(Exception $e){
			print "Error: ".$e->getMessage();
		}
	}
	 public function lperiodoAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $anio=$this->_getParam('anio');
            //$anio = $this->getParam('anio');
            $anio = substr($anio,-2);
            $periodosDb = new Api_Model_DbTable_Periods();
            $where = array('eid'=>$eid, 'oid'=>$oid, 'year'=>$anio);
            //print_r($where);
            $periods = $periodosDb->_getPeriodsxYears($where);
            $this->view->periods = $periods;

            } catch (Exception $e) {
                print 'Error '.$e->getMessage();
            }
        }
	public function schoolsAction(){
		try{
			$this->_helper->layout()->disableLayout();
			$facid=base64_decode($this->getParam('facid'));
			$eid = $this->sesion->eid;
			$oid = $this->sesion->oid;
			$consulschool = new Api_Model_DbTable_Speciality();
			$where = array('eid'=>$eid,'oid'=>$oid,'facid'=>$facid);
			$dataschool = $consulschool->_getSchoolXFacultyNOTParent($where);
			$this->view->schools=$dataschool;
		}catch(Exception $e){
			print 'Error; '.$e->getMessage();
		}
	}
	public function specialityAction(){
		try{
			$this->_helper->layout()->disableLayout();
			$escid=base64_decode($this->getParam('escid'));
			$subid=base64_decode($this->getParam('subid'));
			$eid = $this->sesion->eid;
			$oid = $this->sesion->oid;
			$where = array('eid' => $eid, 'oid' => $oid, 'parent' => $escid);
            $es = new Api_Model_DbTable_Speciality();
            $especia = $es->_getFilter($where,$attrib=null,$orders=null);
            $this->view->especialidad=$especia;

		}catch(Exception $e){
			print "Error: ".$e->getMessage();
		}
	}

	public function listAction(){
		try{
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
			$data = $this->getRequest()->getPost();
			$where = array('escid'=>$data['escid'],'perid'=>$data['perid']);
			$consul = new Api_Model_DbTable_Registrationxcourse();
			$conspaciality = new Api_Model_DbTable_Speciality();

			if($data['opcion']==1){
				$resul= $consul->_firstlegends($where);
			}elseif($data['opcion']==2){
				$resul= $consul->_firstlegendsirregulares($where);
			}elseif($data['opcion']==3){
				$resul= $consul->_firstlegendsretirados($where);
			}
			
			$wher = array('eid'=>$eid,'oid'=>$oid);
			$speciality = $conspaciality->_getFilter($wher);
			$this->view->resp=$resul;
			$this->view->data=$speciality;
			$this->view->opcion=$data['opcion'];
			$this->view->periodo=$data['perid'];
			$this->view->escid=$data['escid'];

		}catch(Excepiton $e){
			print "Error: ".$e->getMessage();
		}
	}
	 public function printlistAction(){
	 	try{
	 		$this->_helper->layout()->desableLayout();
	 		$eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $data['opcion']=$this->getParam('opcion');
            $data['escid'] =$this->getParam('escid');
            $data['perid'] =$this->getParam('perid');
            $where = array('escid'=>$data['escid'],'perid'=>$data['perid']);
            $consult = new Api_Model_DbTable_Registrationxcourse();
            $conspaciality = new Api_Model_DbTable_Speciality();
            /*if($data['opcion']==1){
				$resul= $consul->_firstlegends($where);
			}elseif($data['opcion']==2){
				$resul= $consul->_firstlegendsirregulares($where);
			}elseif($data['opcion']==3){
				$resul= $consul->_firstlegendsretirados($where);
			}

			$wher = array('eid'=>$eid,'oid'=>$oid);
			$speciality = $conspaciality->_getFilter($wher);
			$this->view->resp=$resul;
			$this->view->data=$speciality;*/
			$this->view->opcion=$data['opcion'];
			$this->view->periodo=$data['perid'];
			$this->view->escid=$data['escid'];
	 	}catch(Exception $e){
	 		print "Error: ".$e->getMessage();
	 	}
	 }
}