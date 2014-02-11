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
            $uid=base64_decode($this->_getParam('uid'));
            $escid=base64_decode($this->_getParam('escid'));
            $eid=base64_decode($this->_getParam('eid'));
            $oid=base64_decode($this->_getParam('oid'));
            $subid=base64_decode($this->_getParam('subid'));
            $pid=base64_decode($this->_getParam('pid'));
            $record = new Api_Model_DbTable_Registrationxcourse();
            // $data = $record->_getRecordNotasAlumno($escid,$uid,$eid,$oid,$subid,$pid);
            $data = $record->_getRecordNotasAlumno_H($escid,$uid,$eid,$oid,$subid,$pid);
            
            $this->view->data=$data;
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['subid']=$subid;
              
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
            $names=strtoupper($spe['esc']);
            $namep=strtoupper($spe['parent']);
            $namefinal=$names." <br> ".$namep;
            
            $whered['eid']=$eid;
            $whered['oid']=$oid;
            $whered['facid']= $speciality['facid'];
            $dbfaculty = new Api_Model_DbTable_Faculty();
            $faculty = $dbfaculty ->_getOne($whered);
            $namef = strtoupper($faculty['name']);
  
            $wheres=array('eid'=>$eid,'pid'=>$pid);
            $dbperson = new Api_Model_DbTable_Person();
            $person= $dbperson ->_getOne($wheres);

            if ($speciality['header']) {
                $namelogo = $speciality['header'];
            }
            else{
                $namelogo = 'blanco';
            }

            $dbimpression = new Api_Model_DbTable_Countimpressionall();
            date_default_timezone_set("America/Lima");
            $uidim=$this->sesion->pid;

            $data = array(
                'eid'=>$eid,
                'oid'=>$oid,
                'uid'=>$uid,
                'escid'=>$escid,
                'subid'=>$subid,
                'pid'=>$pid,
                'type_impression'=>'recordnotas',
                'date_impression'=>date('Y-m-d H:i:s'),
                'pid_print'=>$uidim
                );
            $dbimpression->_save($data);            

            $wheri = array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'recordnotas');
            $dataim = $dbimpression->_getFilter($wheri);
            
            $co=0;
            $len=count($dataim);
            for ($i=0; $i < $len ; $i++) { 
                if($dataim[$i]['type_impression']=='recordnotas'){
                    $co=$co+1;
                }
            }
            $codigo=$co." - ".$uidim;

            $header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
            $header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", $namelogo, $header);
            $header = str_replace("?codigo", $codigo, $header);
            
            $this->view->spe=$spe;
            $this->view->uid=$uid;  
            $this->view->person=$person;
            $this->view->header=$header;
            $this->view->footer=$footer;

        } catch (Exception $e) {
            print "Error: Print Notas: ".$e->getMessage();
        }

    }
}