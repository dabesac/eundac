<?php
class Syllabus_PrintController extends Zend_Controller_Action {

    public function init(){
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
         $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        // if (!$login->modulo=="syllabus"){
        //  $this->_helper->redirector('index','index','default');
        // }
        $this->sesion = $login;
    }

    public function indexAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $courseid = base64_decode($this->_getParam("courseid"));
            $turno = base64_decode($this->_getParam("turno"));
            $curid = base64_decode($this->_getParam("curid"));
            $escid = base64_decode($this->_getParam("escid"));
            $subid = base64_decode($this->_getParam("subid"));
            $perid = base64_decode($this->_getParam("perid"));
            $this->view->subid=$subid;
            $this->view->perid=$perid;
            $this->view->escid=$escid;
            $this->view->curid=$curid;
            $this->view->courseid=$courseid;
            $this->view->turno=$turno;

            $wheres=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
            $dbspeciality = new Api_Model_DbTable_Speciality();
            $speciality = $dbspeciality ->_getOne($wheres); 

            if ($speciality['header']) {
                $namelogo = $speciality['header'];
            }
            else{
                $namelogo = 'blanco';
            }
            $this->view->namelogo=$namelogo;    

            $wherecur['eid']=$eid;
            $wherecur['oid']=$oid;
            $wherecur['escid']=$escid;
            $wherecur['perid']=$perid;
            $wherecur['courseid']=$courseid;
            $wherecur['turno']=$turno;
            $wherecur['curid']=$curid;
            $percurso = new Api_Model_DbTable_PeriodsCourses();
            $datcurso = $percurso->_getInfocourseXescidXperidXcourseXturno($wherecur);
            $this->view->curso = $datcurso;
            
            $wheresc['eid']=$eid;
            $wheresc['oid']=$oid;
            $wheresc['escid']=$escid;
            $wheresc['subid']=$subid;
            $esc = new Api_Model_DbTable_Speciality();
            $escuela = $esc ->_getOne($wheresc);
            $this->view->escuela=$escuela;

            $wherefac['eid']=$eid;
            $wherefac['oid']=$oid;
            $wherefac['facid']=$escuela['facid'];
            $fac = new Api_Model_DbTable_Faculty();
            $facu = $fac ->_getOne($wherefac);
            $this->view->facu=$facu;
            
            $whereperi['eid']=$eid;
            $whereperi['oid']=$oid;
            $whereperi['perid']=$perid;
            $bdperiodo = new Api_Model_DbTable_Periods();
            $periods = $bdperiodo->_getOne($whereperi);
            $this->view->periods=$periods; 
            
            $wheresyl['eid']=$eid;
            $wheresyl['oid']=$oid;
            $wheresyl['subid']=$subid;
            $wheresyl['perid']=$perid;
            $wheresyl['escid']=$escid;
            $wheresyl['curid']=$curid;
            $wheresyl['courseid']=$courseid;
            $wheresyl['turno']=$turno;
            $dbsilabos = new Api_Model_DbTable_Syllabus();
            $silabo = $dbsilabos->_getOne($wheresyl);
            $this->view->silabo=$silabo;

            $whereper['eid']=$eid;
            $whereper['pid']=$silabo['teach_pid'];
            $per= new Api_Model_DbTable_Person();
            $persona=$per->_getOne($whereper);
            $this->view->infouser=$persona;

            $syluni = new Api_Model_DbTable_Syllabusunits();
            $datsyluni=$syluni->_getAllXSyllabus($wheresyl);
            $this->view->datunidades=$datsyluni;

            $wheredic=array('eid' => $eid, 'oid' => $oid, 'escid' => $escid , 'is_director' => 'S');
            $dic = new Api_Model_DbTable_UserInfoTeacher();
            $direc = $dic->_getFilter($wheredic,$attrib=null,$orders=null);
            $whereper['pid']=$direc[0]['pid'];
            $director = $per->_getOne($whereper);
            $this->view->director = $director;

            $uid=$direc[0]['uid'];
            $pid=$direc[0]['pid'];
            $dbimpression = new Api_Model_DbTable_Countimpressionall();
            $data = array(
                'eid'=>$eid,
                'oid'=>$oid,
                'uid'=>$uid,
                'escid'=>$escid,
                'subid'=>$subid,
                'pid'=>$pid,
                'type_impression'=>'silabo',
                'date_impression'=>date('Y-m-d h:m:s')
                );
            $dbimpression->_save($data);

            $wheri = array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'silabo');
            $dataim = $dbimpression->_getFilter($wheri);
            $co=0;
            $len=count($dataim);
            for ($i=0; $i < $len ; $i++) { 
                if($dataim[$i]['type_impression']=='silabo'){
                    $co=$co+1;
                }
            }
            $uidim=$this->sesion->pid;
            $codigo=$co.$uidim;
            $this->view->codigo=$codigo;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}