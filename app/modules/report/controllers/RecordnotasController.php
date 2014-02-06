<?php
 class Report_RecordnotasController extends Zend_Controller_Action{

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
 			$fm=new Report_Form_Buscar();
			$this->view->fm=$fm; 			
 		} catch (Exception $e) {
 			print ('Error: get datos'. $e->getMessage());
 			
 		}

 	}

 	public function getstudentuidAction(){
 		try{
       		$this->_helper->layout()->disableLayout();
          $facid=$this->sesion->faculty->facid;
          $this->view->facid=$facid;
          // print ($facid);
          $uid= $this->_getParam('uid');
       		if($uid){
       			$where['uid'] = $uid;
        		$where['eid'] = $this->sesion->eid;
        		$where['oid'] = $this->sesion->oid;
        		$bdu = new Api_Model_DbTable_Users();
        		$data = $bdu->_getUserXUid($where);
        		$this->view->data=$data;
       		}
       		$nom = $this->_getParam('last_name0');
       		if($nom){
        		$where['eid'] = $this->sesion->eid;
        		$where['oid'] = $this->sesion->oid;
        		$where['rid'] = 'AL';
        		$where['nom'] = trim(strtoupper($nom));
        		$where['nom'] = mb_strtoupper($where['nom'],'UTF-8');
        		$bdu = new Api_Model_DbTable_Users();
        		$da = $bdu->_getUsuarioXNombre($where);
            $where['rid'] = 'EG';            
            $dat = $bdu->_getUsuarioXNombre($where);
            $data = array_merge($da,$dat);
            $this->view->data=$data;
            // print_r($data);  
        	}
     }catch(Exception $ex ){
        print ("Error Controller get Datos: ".$ex->getMessage());
    	} 
 	}

 	public function printAction(){
 		try {
 			    $this->_helper->layout()->disableLayout();
          // $footer=$this->sesion->org['footer_print'];
      		// $this->view->footer=$footer;
 			    $uid=base64_decode($this->_getParam('uid'));
      		$this->view->uid=$uid;	
      		$escid=base64_decode($this->_getParam('escid'));
      		$eid=base64_decode($this->_getParam('eid'));
      		$oid=base64_decode($this->_getParam('oid'));
      		$subid=base64_decode($this->_getParam('subid'));
      		$pid=base64_decode($this->_getParam('pid'));
      		$record = new Api_Model_DbTable_Registrationxcourse();
     			// $data = $record->_getRecordNotasAlumno($escid,$uid,$eid,$oid,$subid,$pid);
     			$data = $record->_getRecordNotasAlumno_H($escid,$uid,$eid,$oid,$subid,$pid);
          // $len=count($data);
          // print ($len);
          // exit();
          $this->view->data=$data;
          $where['eid']=$eid;
          $where['oid']=$oid;
          $where['escid']=$escid;
          $where['subid']=$subid;
		      
  			  $spe=array();
  		    $dbspeciality = new Api_Model_DbTable_Speciality();
  		    $speciality = $dbspeciality ->_getOne($where);

          if ($speciality['header']) {
            $namelogo = $speciality['header'];
          }
          else{
            $namelogo = 'blanco';
          }
          $this->view->namelogo=$namelogo;
      
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
            // print_r($spe);
            // exit();
        		$whered['eid']=$eid;
        		$whered['oid']=$oid;
        		$whered['facid']= $speciality['facid'];
        		$dbfaculty = new Api_Model_DbTable_Faculty();
        		$faculty = $dbfaculty ->_getOne($whered);
        		$this->view->faculty=$faculty;      
        		$wheres['eid']=$eid;
        		$wheres['pid']=$pid;
        		$dbperson = new Api_Model_DbTable_Person();
        		$person= $dbperson ->_getOne($wheres);
        		$this->view->person=$person;
 		} catch (Exception $e) {
 			print "Error: Print Notas: ".$e->getMessage();
 		}

 	}
 }