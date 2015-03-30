<?php 
class Register_RegisterealizedController extends Zend_Controller_Action {

	public function init(){
		$sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		if (!$login->rol['module']=="register"){
 			$this->_helper->redirector('index','index','default');
 		}
 		$this->sesion = $login;
	}

	public function indexAction(){
		try {
			$this->_helper->layout()->disableLayout();
           	$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;

			$rid=$this->_getParam('rid');
			$perid=$this->_getParam('perid');
			$escid=$this->_getParam('escid');
			$subid=$this->_getParam('subid');
			$uid=$this->_getParam('uid');
			$pid=$this->_getParam('pid');
		
			$this->view->pid=$pid;
			$this->view->rid=$rid;
			$this->view->escid=$escid;
			$this->view->subid=$subid;
			$this->view->uid=$uid;

			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'uid'=>$uid,'state'=>'M');
			$attrib=array('escid','perid','courseid','turno','notafinal','state');
			$orders=array('perid','courseid');	
			$dbgc= new Api_Model_DbTable_Registrationxcourse();
			$data=$dbgc->_getFilter($where,$attrib,$orders);
			$len=count($data);
			$dbperiod= new Api_Model_DbTable_Periods();
			$dbcourse=new Api_Model_DbTable_Course();
			
			$newperiod=array();
			$c=0;
			$aux=$data[0]['perid'];
			$newperiod[0]=$aux;
			
			foreach ($data as $periods) {
				$perid=$periods['perid'];
				if ($perid!=$aux) {
					$c++;	
					$newperiod[$c]=$perid;
					$aux=$perid;
				}
			}
			$l=count($newperiod);
			for ($f=0; $f < $l ; $f++) { 
				$whered=array('eid'=>$eid,'oid'=>$oid,'perid'=>$newperiod[$f]);
				$attrib=array('perid','name');
				$dasd=$dbperiod->_getFilter($whered,$attrib);
				$newperiod[$f]=$dasd[0];
			}
			
			for ($i=0; $i < $len; $i++) {
				$courseid=$data[$i]['courseid'];
				$whered=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'courseid'=>$courseid);
				$attrib=array('courseid','name','semid','credits');
				$datac=$dbcourse->_getFilter($whered,$attrib);
				$datacourse[$i]=$datac[0];
			}
			$this->view->newperiod=$newperiod;
			$this->view->datacourse=$datacourse;
			$this->view->data=$data;
			
		} catch (Exception $e) {
			print "Error: get Registers".$e->getMessage();
		}

	}

	public function registerprintAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
            
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$escid=base64_decode($this->_getParam('escid'));
			$subid=base64_decode($this->_getParam('subid'));
			$uid=base64_decode($this->_getParam('uid'));
			$this->view->uid=$uid;

			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'uid'=>$uid);
			$attrib=array('escid','perid','courseid','turno','notafinal','state');
			$orders=array('perid','courseid');	
			$dbgc= new Api_Model_DbTable_Registrationxcourse();
			$data=$dbgc->_getFilter($where,$attrib,$orders);
			$len=count($data);
			$dbperiod= new Api_Model_DbTable_Periods();
			$dbcourse=new Api_Model_DbTable_Course();
			
			$newperiod=array();
			$c=0;
			$aux=$data[0]['perid'];
			$newperiod[0]=$aux;
			foreach ($data as $periods) {
				$perid=$periods['perid'];				 
				if ($perid!=$aux) {
					$c++;	
					$newperiod[$c]=$perid;
					$aux=$perid;
				}
			}
			$l=count($newperiod);
			for ($f=0; $f < $l ; $f++) { 
				$whered=array('eid'=>$eid,'oid'=>$oid,'perid'=>$newperiod[$f]);
				$attrib=array('perid','name');
				$dasd=$dbperiod->_getFilter($whered,$attrib);
				$newperiod[$f]=$dasd[0];
			}
			// print_r($newperiod);s
			for ($i=0; $i < $len; $i++) {
				$courseid=$data[$i]['courseid'];
				$whered=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'courseid'=>$courseid);
				$attrib=array('courseid','name','semid','credits');
				$datac=$dbcourse->_getFilter($whered,$attrib);
				$datacourse[$i]=$datac[0];
			}
			
			$this->view->newperiod=$newperiod;
			$this->view->datacourse=$datacourse;
			$this->view->data=$data;

			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
			$spe=array();
		    $dbspeciality = new Api_Model_DbTable_Speciality();
		    $speciality = $dbspeciality ->_getOne($where);
		    $parent=$speciality['parent'];
		    $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
		    $parentesc= $dbspeciality->_getOne($wher);
		    if ($parentesc) {
		        $pala='ESPECIALIDAD DE ';
		        $spe['esc']=$parentesc['name'];
		        $spe['parent']=$pala.$speciality['name'];
		    }
		    else{
		        $spe['esc']=$speciality['name'];
		        $spe['parent']='';  
		    }

		    $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";

            $names=strtoupper($spe['esc']);
            $namep=strtoupper($spe['parent']);
            $namefinal=$names."<br>".$namep;
		    $whered['eid']=$eid;
		    $whered['oid']=$oid;
		    $whered['facid']= $speciality['facid'];
		    $dbfaculty = new Api_Model_DbTable_Faculty();
		    $faculty = $dbfaculty ->_getOne($whered);
		    $namef = strtoupper($faculty['name']);

		    $wheres=array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid);
		    $dbperson = new Api_Model_DbTable_Users();
		    $person= $dbperson -> _getUserXUid($wheres);
		   	$this->view->person=$person;
		   	$pid=$person[0]['pid'];

		   	$dbimpression = new Api_Model_DbTable_Countimpressionall();
            
            $uidim=$this->sesion->pid;

            $wheri = array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'matriculasrealizadas');
            $dataim = $dbimpression->_getFilter($wheri);

            if ($dataim) {
            	$pk = array('eid'=>$eid,'oid'=>$oid,'countid'=>$dataim[0]['countid'],'escid'=>$escid,'subid'=>$subid);
                $data_u = array('count_impression'=>$dataim[0]['count_impression']+1);

                $dbimpression->_update($data_u,$pk);
                $co=$data_u['count_impression'];
            }
            else{
	            $data = array(
	                'eid'=>$eid,
	                'oid'=>$oid,
	                'uid'=>$uid,
	                'escid'=>$escid,
	                'subid'=>$subid,
	                'pid'=>$pid,
	                'type_impression'=>'matriculasrealizadas',
	                'date_impression'=>date('Y-m-d H:i:s'),
	                'pid_print'=>$uidim,
	                'count_impression'=>1	
	                );
	            $dbimpression->_save($data);
            	$co=1;
            }
            
            
            $codigo=$co." - ".$uidim;

		   	$header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", $namelogo, $header);
            $header = str_replace("?codigo", $codigo, $header);

            $this->view->header=$header;
            $this->view->footer=$footer;

					
		} catch (Exception $e) {
			print "Error: Print".$e->getMessage();
		}

	}

	public function printperiodAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$escid=base64_decode($this->_getParam('escid'));
			$subid=base64_decode($this->_getParam('subid'));
			$uid=base64_decode($this->_getParam('uid'));
			$this->view->uid=$uid;
			$perid=base64_decode($this->_getParam('perid'));
			$this->view->perid=$perid;

			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'uid'=>$uid,'perid'=>$perid);
			$attrib=array('escid','perid','courseid','turno','notafinal','state');
			$orders=array('perid','courseid');	
			$dbgc= new Api_Model_DbTable_Registrationxcourse();
			$data=$dbgc->_getFilter($where,$attrib,$orders);

			$len=count($data);
			$wher=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid);
			$attrib=array('perid','name');
			$dbperiod=new Api_Model_DbTable_Periods();
			$dataperiod=$dbperiod->_getFilter($wher,$attrib);
			$dbcourse=new Api_Model_DbTable_Course();
			
			for ($i=0; $i < $len; $i++) {
				$courseid=$data[$i]['courseid'];
				$whered=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'courseid'=>$courseid);
				$attrib=array('courseid','name','semid','credits');
				$datac=$dbcourse->_getFilter($whered,$attrib);
				$datacourse[$i]=$datac[0];
			}
			
			$this->view->dataperiod=$dataperiod;
			$this->view->datap=$datap;
			$this->view->datacourse=$datacourse;
			$this->view->data=$data;

			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
			$spe=array();
		    $dbspeciality = new Api_Model_DbTable_Speciality();
		    $speciality = $dbspeciality ->_getOne($where);
		    $parent=$speciality['parent'];
		    $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
		    $parentesc= $dbspeciality->_getOne($wher);
		    
		    if ($parentesc) {
		        $pala='ESPECIALIDAD DE ';
		        $spe['esc']=$parentesc['name'];
		        $spe['parent']=$pala.$speciality['name'];
		        $this->view->spe=$spe;
		    }
		    else{
		        $spe['esc']=$speciality['name'];
		        $spe['parent']='';  
		        $this->view->spe=$spe;
		    }

		    if ($speciality['header']) {
                $namelogo = $speciality['header'];
            }
            else{
                $namelogo = 'blanco';
            }

            $names=strtoupper($spe['esc']);
            $namep=strtoupper($spe['parent']);
            $namefinal=$names."<br>".$namep;
		    $whered['eid']=$eid;
		    $whered['oid']=$oid;
		    $whered['facid']= $speciality['facid'];
		    $dbfaculty = new Api_Model_DbTable_Faculty();
		    $faculty = $dbfaculty ->_getOne($whered);
		    $namef = strtoupper($faculty['name']);
		  
		    $wheres=array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid);
		    $dbperson = new Api_Model_DbTable_Users();
		    $person= $dbperson -> _getUserXUid($wheres);
		   	$this->view->person=$person;
		   	$pid=$person[0]['pid'];

		   	$dbimpression = new Api_Model_DbTable_Countimpressionall();
            
            $uid=$this->sesion->uid;
            $uidim=$this->sesion->pid;
            $pid=$uidim;
            
			$wheri = array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'matriculasrealizadasxperiodo','perid'=>$perid);
            $dataim = $dbimpression->_getFilter($wheri);

            if ($dataim) {
            	$pk = array('eid'=>$eid,'oid'=>$oid,'countid'=>$dataim[0]['countid'],'escid'=>$escid,'subid'=>$subid);
                $data_u = array('count_impression'=>$dataim[0]['count_impression']+1);

                $dbimpression->_update($data_u,$pk);
                $co=$data_u['count_impression'];
            }
            else{
	            $data = array(
	                'eid'=>$eid,
	                'oid'=>$oid,
	                'uid'=>$uid,
	                'escid'=>$escid,
	                'subid'=>$subid,
	                'pid'=>$pid,
	                'type_impression'=>'matriculasrealizadasxperiodo',
	                'date_impression'=>date('Y-m-d H:i:s'),
	                'pid_print'=>$uidim,
	                'perid'=>$perid,
	                'count_impression'=>1
	            );            	
            	$dbimpression->_save($data);
            	$co=1;
            }
         
            
            $codigo=$co." - ".$uidim;

		   	$header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", $namelogo, $header);
            $header = str_replace("?codigo", $codigo, $header);

            $this->view->header=$header;
            $this->view->footer=$footer;

		} catch (Exception $e) {
			print "Error: Print".$e->getMessage();
		}

	}
}