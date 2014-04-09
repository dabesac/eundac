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

			$bdcourses=new Api_Model_DbTable_Course();
			$bdteacher=new Api_Model_DbTable_PeriodsCourses();
			$bdcontrol=new Api_Model_DbTable_ControlActivity();

			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid);
			$where1=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'perid'=>$perid);
			$data=$bdteacher->_getTeacherXPeridXEscid1($where1);
			if ($data) {			
				foreach ($data as $key => $teachers) {
					$where1['courseid']=$teachers['courseid'];
					$where1['state']="D";
					$datacontrol=$bdcontrol->_getUltimateclass($where1);
					$where['courseid']=$teachers['courseid'];
					$data[$key]['ultimatesession']=$datacontrol[0];
					$datacourse=$bdcourses->_getFilter($where);
					$data[$key]['namecourse']=$datacourse[0]['name'];
				}
				$len=count($data);
				$c=1;
				$s=0;
				$datafinal[$s]['name']=strtoupper($data[0]['last_name0'])." ".strtoupper($data[0]['last_name1']).", ".$data[0]['first_name'];
				$datafinal[$s]['uid']=$data[0]['uid'];
				$datafinal[$s]['pid']=$data[0]['pid'];
				$datafinal[$s]['courses'][0]=$data[0];
				// print_r($datafinal);exit();
				$r=1;
				for ($i=0; $i < $len-1; $i++) {
					if ($data[$i]['uid']==$data[$c]['uid']) {
						$datafinal[$s]['courses'][$r]=$data[$c];
						$r++;
					}
					else{
						$s++;
						$r=1;
						$datafinal[$s]['name']=strtoupper($data[$c]['last_name0'])." ".strtoupper($data[$c]['last_name1']).", ".$data[$c]['first_name'];
						$datafinal[$s]['uid']=$data[$c]['uid'];
						$datafinal[$s]['pid']=$data[$c]['pid'];
						$datafinal[$s]['courses'][$r]=$data[$c];				
						$r++;
					}
					$c++;
				}
			}
			$this->view->data=$datafinal;
		} catch (Exception $e) {
			
		}
	}
	public function printadvanceAction()
    {
        try{
        	$this->_helper->layout()->disableLayout();
            $controlsyllabusDb = new Api_Model_DbTable_ControlActivity();
            $contentsyllabusDb = new Api_Model_DbTable_Syllabusunitcontent();
            $coursesDb = new Api_Model_DbTable_Course();

            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $escid = base64_decode($this->getParam('escid'));
            $subid = base64_decode($this->getParam('subid'));
            $curid = base64_decode($this->getParam('curid'));
            $courseid = base64_decode($this->getParam('courseid'));
            $turno = base64_decode($this->getParam('turno'));
            $perid = base64_decode($this->getParam('perid'));
            $nameteach = base64_decode($this->getParam('nameteach'));
            $uid=base64_decode($this->getParam('uid'));
            $pid=base64_decode($this->getParam('pid'));

            $where = array('eid'=>$eid, 'oid'=>$oid, 'courseid'=>$courseid, 'curid'=>$curid);
            $attrib = array('name');
            $coursename = $coursesDb->_getFilter($where, $attrib);
            $this->view->coursename = $coursename;
            $this->view->turno=$turno;
            $this->view->nameteach=$nameteach;

            $attrib = array('session');
            $where = array('eid' => $eid, 
                            'oid'=>$oid, 
                            'perid'=>$perid,
                            'escid'=>$escid, 
                            'subid'=>$subid, 
                            'courseid'=>$courseid, 
                            'curid'=>$curid,
                            'turno'=>$turno);
            $controlsyllabus = $controlsyllabusDb->_getFilter($where, $attrib);
            $contentsyllabus = $contentsyllabusDb->_getFilter($where, $attrib);
  
            $c = 0;    
            $index = 0;
            $firstTime = "Si";
            if ($contentsyllabus) {
                if ($controlsyllabus) {
                    foreach ($controlsyllabus as $session) {
                        $c++;
                    }
                    $this->view->realizedSession = $c;

                    for ($i = 0; $i < $c; $i++) { 
                        $attrib = array('perid', 'subid', 'courseid', 'curid', 'turno', 'unit', 'session', 'week', 'obj_content');
                        $where = array('eid' => $eid, 
                                    'oid'=>$oid, 
                                    'perid'=>$perid, 
                                    'subid'=>$subid, 
                                    'courseid'=>$courseid, 
                                    'curid'=>$curid,
                                    'turno'=>$turno,
                                    'session'=>$controlsyllabus[$i]['session']);
                        $contentsyllabus[$index] = $contentsyllabusDb->_getFilter($where, $attrib);
                        if ($contentsyllabus[$index][0]['obj_content']=="") {
                        	$attrib = array('perid', 'subid', 'courseid', 'curid', 'turno', 'unit', 'session', 'week', 'com_conceptual');
	                        $contentsyllabus[$index] = $contentsyllabusDb->_getFilter($where, $attrib);
                        }

                        $attrib = array('datecheck');
                        $where = array('eid' => $eid, 
                                    'oid'=>$oid, 
                                    'perid'=>$perid, 
                                    'subid'=>$subid, 
                                    'courseid'=>$courseid, 
                                    'curid'=>$curid,
                                    'turno'=>$turno,
                                    'session'=>$controlsyllabus[$i]['session']);
                        $controlsyllabus[$index] = $controlsyllabusDb->_getFilter($where, $attrib);

                        $index++;
                    }
                    $firstTime = "No";
                }

                $attrib = array('perid', 'escid', 'subid', 'courseid', 'curid', 'turno', 'unit', 'session', 'week', 'obj_content');
                $where = array('eid' => $eid, 
                            'oid'=>$oid, 
                            'perid'=>$perid,
                            'escid'=>$escid, 
                            'subid'=>$subid, 
                            'courseid'=>$courseid, 
                            'curid'=>$curid,
                            'turno'=>$turno,
                            'session'=>$contentsyllabus[$index]['session']);
                $contentsyllabus[$index] = $contentsyllabusDb->_getFilter($where, $attrib);
                if ($contentsyllabus[$index][0]['obj_content']=="") {
                	$attrib = array('perid', 'escid', 'subid', 'courseid', 'curid', 'turno', 'unit', 'session', 'week', 'com_conceptual');
	                $contentsyllabus[$index] = $contentsyllabusDb->_getFilter($where, $attrib);
                }


                if ($contentsyllabus[$index] == null) {
                    $completeSyllabus = 'Si';
                }else{
                    $completeSyllabus = 'No';
                }
                $index++;

                $c = 0;    
                foreach ($contentsyllabus as $session) {
                    $c++;
                }
                $this->view->restSession = $c;

                $finalSession = 0;
                for ($i = $index; $i < $c ; $i++) { 
                    //echo $i.' ';
                    $attrib = array('perid', 'subid', 'courseid', 'curid', 'turno', 'unit', 'session', 'week', 'obj_content');
                    $where = array('eid' => $eid, 
                                'oid'=>$oid, 
                                'perid'=>$perid, 
                                'subid'=>$subid, 
                                'courseid'=>$courseid, 
                                'curid'=>$curid,
                                'turno'=>$turno,
                                'session'=>$contentsyllabus[$i]['session']);
                    $contentsyllabus[$index] = $contentsyllabusDb->_getFilter($where, $attrib);
                    if ($contentsyllabus[$index][0]['obj_content']=="") {
                    	$attrib = array('perid', 'subid', 'courseid', 'curid', 'turno', 'unit', 'session', 'week', 'com_conceptual');
                    	$contentsyllabus[$index] = $contentsyllabusDb->_getFilter($where, $attrib);
                    	
                    }
                    $index++;
                    $finalSession = 1;
                }

                $this->view->finalSession = $finalSession;

                $this->view->firstTime = $firstTime;
                $this->view->completeSyllabus = $completeSyllabus;
             
                $this->view->contentsyllabus = $contentsyllabus;
                $this->view->controlsyllabus = $controlsyllabus;

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
	                
	            $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";

	            $dbimpression = new Api_Model_DbTable_Impresscourse();
	            $uidim=$this->sesion->pid;

	            $data = array(
	                'eid'=>$eid,
	                'oid'=>$oid,
	                'perid'=>$perid,
	                'courseid'=>$courseid,
	                'escid'=>$escid,
	                'subid'=>$subid,
	                'curid'=>$curid,
	                'turno'=>$turno,
	                'register'=>$uidim,
	                'created'=>date('Y-m-d H:i:s'),
	                'code'=>'infosillabus_acre'
	                );
	            // print_r($data);exit();
	            $dbimpression->_save($data);            

	            $wheri = array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'courseid'=>$courseid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid,'turno'=>$turno,'code'=>'infosillabus_acre');
	            $dataim = $dbimpression->_getFilter($wheri);
	            $co=count($dataim);
	            $codigo=$co." - ".$uidim;
                $header=$this->sesion->org['header_print'];
	            $footer=$this->sesion->org['footer_print'];
	            $header = str_replace("?facultad",$namef,$header);
	            $header = str_replace("?escuela",$namefinal,$header);
	            $header = str_replace("?logo", $namelogo, $header);
	            $header = str_replace("?codigo", $codigo, $header);
	            
	            $this->view->uid=$uid;  
	            $this->view->person=$person;
	            $this->view->header=$header;
	            $this->view->footer=$footer;
            }
        }
            catch (Exception $e) {           
        }
    }
}