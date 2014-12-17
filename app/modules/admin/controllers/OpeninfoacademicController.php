<?php
	class Admin_OpeninfoacademicController extends Zend_Controller_Action{
		public function init()
		{
			$sesion  = Zend_Auth::getInstance();
 			if(!$sesion->hasIdentity() ){
 				$this->_helper->redirector('index',"index",'default');
 			}
 			$login = $sesion->getStorage()->read();
 			$this->sesion = $login;

		}
		public function indexAction()
		{
			try {
				$eid = $this->sesion->eid;
	 			$oid = $this->sesion->oid;
				$facultyDb = new Api_Model_DbTable_Faculty();
	            $preDataFaculty = $facultyDb->_getAll();
	            $c=0;
	            foreach ($preDataFaculty as $c => $faculty) {
	                if ($faculty['state'] == 'A' and  $faculty['facid'] != "TODO" ) {
	                    $dataFaculty[$c]['facid'] = $faculty['facid'];
	                    $dataFaculty[$c]['name']  = $faculty['name'];
	                }
	            }
	            $this->view->dataFaculty = $dataFaculty; 

			} catch (Exception $e) {
				print "Error: ".$e->getMessage();
			}
		}
		public function listteacherAction()
		{
			try {
				
				$this->_helper->layout()->disableLayout();
	 			$eid = $this->sesion->eid;
	 			$oid = $this->sesion->oid;
	 			$espec = $this->_getParam('especialidad');
	 			$escid = $this->_getParam('escuela');
	 			$anio = $this->_getParam('anio');
	 			$perid = $this->_getParam('periodo');
	 			$escFin;
	 			if ($espec !="TODOESP") {
	 				if ($escid !="TODOESC") {
	 					if ($espec =="") {
	 					$escFin = $escid;
	 					} else {
	 						$escFin = $espec;
	 					}
	 					$anio = substr($anio, 2);
	 					$perid = $anio . $perid;
	 					
			 			$this->view->perid=$perid;
			 			$this->view->escid=$escFin;
			 			$dbInfoAcad=new Api_Model_DbTable_Infoacademic();
			 			$row=$dbInfoAcad->listteacher($escid,$perid);
			 			$this->view->row=$row;
			 			$sms=1;
	 				}else{$sms=2;}
	 			} else {$sms=3;}	
				$this->view->sms=$sms;
			} catch (Exception $e) {
				print "Error: ".$e->getMessage();
			}
			
		}

		public function updatestateAction()
		{
			$this->_helper->layout()->disableLayout();
			$perid=base64_decode($this->_getParam('perid'));
			$escid=base64_decode($this->_getParam('escid'));
			$state=$this->_getParam('state');
			$pid=base64_decode($this->_getParam('pid'));
			if ($state == "B") {
				$state="C";
				$json = array(
					'status'=>true,
					'sms'=>"Se cerro satisfactoriamente.",
					'do'=>"btnChange btn btn-success form-control",
					'estado'=>'C',
					'icon'=>'glyphicon glyphicon-folder-open',
					'text'=>'Abrir'
				);
				//$json = array('do'=>"btnChange btn btn-danger form-control");
				$query=new Api_Model_DbTable_Infoacademic();
				$query->_update($escid,$perid,$state,$pid); 

			} elseif ($state == "C") {
				$state="B";
				$json = array(
					'status'=>true,
					'sms'=>"Se abrio satisfactoriamente",
					'do'=>"btnChange btn btn-danger form-control",
					'estado'=>'B',
					'icon'=>'glyphicon glyphicon-folder-close',
					'text'=>'Cerrar'
					);
				//$json = array('do'=>"btnChange btn btn-success form-control");
				$query=new Api_Model_DbTable_Infoacademic();
				$query->_update($escid,$perid,$state,$pid); 
			}else{
				
				$json = array('status'=>false,'sms'=>"hubo un error");
				//$json = array('do'=>"");
			}
			$this->_response->setHeader('Content-Type', 'application/json');
			$this->view->data = $json;
		}
	}
?>