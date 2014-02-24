<?php

class Graduated_ReportgraduatedController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	// if (!$login->modulo=="graduated"){
    	// 	$this->_helper->redirector('index','index','default');
    	// }
    	$this->sesion = $login;
    }
    
    public function indexAction()
    {
        try {
            //DataBases
            $schoolDb = new Api_Model_DbTable_Speciality();
            $facultyDb = new Api_Model_DbTable_Faculty();
            //_____________________
            $haveSpeciality = 'No';
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $rid = $this->sesion->rid;
            $facid = $this->sesion->faculty->facid;
            $escid = $this->sesion->escid;
            $is_director = $this->sesion->infouser['teacher']['is_director'];
            if ($rid=="DC" && $is_director=="S"){
                $where = array('eid'=>$eid, 'oid'=>$oid, 'escid'=>$escid);
                $attrib = array('parent', 'name');
                $school = $schoolDb->_getFilter($where, $attrib);
                if ($school[0]['parent']) {
                    $specialityName = $school[0]['name'];
                }else{
                    $specialityName = '';
                }

               
                $where = array('eid'=>$eid, 'oid'=>$oid, 'state'=>'A');
                $attrib = array('parent', 'name');
                $parents = $schoolDb->_getFilter($where, $attrib);
                foreach ($parents as $parent) {
                    if ($parent['parent']['0'] == $escid['0']) {
                        $haveSpeciality = 'Si';
                        break;
                    }
                }
                $this->view->haveSpeciality = $haveSpeciality;

                $schoolName = $this->sesion->speciality->name;
                $this->view->schoolName = $schoolName;
                $this->view->specialityName = $specialityName;
                
                $rid="DIREC";
                $this->view->escid=$escid;        
            }
            if ($rid=="RF" || $rid=="DIREC") {
                $this->view->facid=$facid;
                $where = array('eid'=>$eid, 'oid'=>$oid, 'facid'=>$facid);
                $attrib = array('name');
                $facultyName = $facultyDb->_getFilter($where, $attrib);
                $this->view->facultyName = $facultyName;

                $where = array('eid'=>$eid, 'oid'=>$oid, 'state'=>'A');
                $attrib = array('parent', 'name');
                $parents = $schoolDb->_getFilter($where, $attrib);
                foreach ($parents as $parent) {
                    if ($parent['parent']['0'] == $escid['1']) {
                        $haveSpeciality = 'Si';
                        break;
                    }
                }
                $this->view->haveSpeciality = $haveSpeciality;
            }

            $where = array('eid' => $eid, 'oid' => $oid, 'state' => 'A');
            $fac= new Api_Model_DbTable_Faculty();
            $facultad=$fac->_getFilter($where,$attrib=null,$orders=null);
            $this->view->facultades=$facultad;
            $anio = date('Y');
            $this->view->anio = $anio;
            $this->view->rid = $rid;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function schoolsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $rid = $this->sesion->rid;
            $escid = $this->sesion->escid;
            $is_director = $this->sesion->infouser['teacher']['is_director'];
            $facid = $this->_getParam('facid');
            if ($rid=="DC" && $is_director=="S"){
                if ($facid=="2") $escid=substr($escid,0,3);
                $this->view->escid=$escid;
            }
            if ($facid=="TODO") $this->view->facid=$facid;
            else{
                $where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid);
                $es = new Api_Model_DbTable_Speciality();
                $escu = $es->_getSchoolXFacultyNOTParent($where);
                $this->view->escuelas=$escu;
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function specialityAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $subid = $this->sesion->subid;
            $escid = $this->_getParam('escid');
            if ($escid=="TODOEC") $this->view->escid=$escid;
            else{
                $where = array('eid' => $eid, 'oid' => $oid, 'parent' => $escid);
                $es = new Api_Model_DbTable_Speciality();
                $especia = $es->_getFilter($where,$attrib=null,$orders=null);
                $this->view->especialidad=$especia;
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function periodsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $perid = $this->sesion->period->perid;
            $this->view->perid = $perid;
            $anio = $this->_getParam("anio");
            if ($eid=="" || $oid==""|| $anio=="") return false;
            $p = substr($anio, 2, 3);
            $p1=$p."A";
            $p2=$p."B";
            $where = array('eid' => $eid, 'oid' => $oid, 'p1' => $p1, 'p2' => $p2);
            $periodos = new Api_Model_DbTable_Periods();
            $this->view->lper = $periodos->_getPeriodsXAyB($where);
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function listgraduatedsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $facid = $this->_getParam('facid');
            $escid = $this->_getParam('escid');
            $espec = $this->_getParam('especialidad');
            $perid = $this->_getParam('perid');
            $anho = $this->_getParam('anho');
            $this->view->facid=$facid;
            $this->view->especialidad=$espec;
            $this->view->escid=$escid;
            $this->view->perid=$perid;
            $this->view->anho=$anho;
            
            $user= new Api_Model_DbTable_Users();
            if($perid!='T'){
                if ($facid!="TODO") {
                    if ($escid=="TODOEC") {
                        $where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid, 'perid' => $perid);
                    }else{
                        if ($espec <> "") {
                            if ($espec=="TODOEP") $left='S';
                            else $escid = $espec;
                        }
                        $where = array(
                            'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 
                            'perid' => $perid, 'left' => $left);
                    }
                }else $where = array('eid' => $eid, 'oid' => $oid, 'perid' => $perid);
                $egre = $user->_getGraduatedXFacultyXSchoolXPeriod($where);
            }else{
                $ano=substr($anho,2,4);
                if ($facid!="TODO") {
                    if ($escid=="TODOEC") {  
                        $where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid, 'anio' => $ano);
                    }else{
                        if ($espec<>"") {
                            if ($espec=="TODOEP") $left='S';
                            else $escid = $espec;
                        }
                        $where = array(
                            'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 
                            'anio' => $ano, 'left' => $left);
                    }
                }else $where = array('eid' => $eid, 'oid' => $oid, 'anio' => $ano);
                $egre = $user->_getGraduatedXFacultyXSchoolXAnio($where);
            }
            $this->view->egresados=$egre; 
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function academicAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $escid = $this->_getParam('escid');
            $uid = $this->_getParam('uid');
            $pid = $this->_getParam('pid');
            $this->view->escid=$escid;
            $this->view->uid=$uid;
            $this->view->pid=$pid;
            $this->view->oid=$oid;
            $this->view->eid=$eid;

            // $where = array('eid' => $eid, 'pid' => $pid, 'escid' => $escid);
            // $per = new Api_Model_DbTable_Academicrecord();
            // $ficha = $per->_getFilter($where,$attrib=null,$orders=null);
            // $this->view->ficha = $ficha;

            $user = new Api_Model_DbTable_Users();
            $datauser = $user->_getUserXUid($whereuser=array('eid' => $eid, 'oid' => $oid, 'uid' => $uid));
            $this->view->user=$datauser[0];

            $wherefac = array('eid' => $eid, 'oid' => $oid, 'facid' => substr($escid,0,1));
            $fac = new Api_Model_DbTable_Faculty();
            $datafac = $fac->_getOne($wherefac);
            $this->view->faculty=$datafac;

            $whereesc = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid);
            $esc = new Api_Model_DbTable_Speciality();
            $dataesc = $esc->_getFilter($whereesc,$attrib=null,$orders=null);
            $this->view->school=$dataesc[0];
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function totalgraduatedsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $this->view->oid=$oid;
            $this->view->eid=$eid;
            $facid = $this->_getParam('facid');
            $escid = $this->_getParam('escid');
            $espec = $this->_getParam('especialidad');
            $perid = $this->_getParam('perid');
            $anho = $this->_getParam('anho');
            $user = new Api_Model_DbTable_Users();
            if($perid!='T'){
                if ($facid!="TODO") {
                    if ($escid=="TODOEC") { 
                        $where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid, 'perid' => $perid);
                    }else{
                        if ($espec<>"") {
                            if ($espec=="TODOEP") $left='S';
                            else $escid = $espec;
                        }
                        $where = array(
                            'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 
                            'perid' => $perid, 'left' => $left);
                    }
                }else $where = array('eid' => $eid, 'oid' => $oid, 'perid' => $perid);
                $egre=$user->_getTotalGraduatedXFacultyXSchoolXPeriod($where);
            }else{
                $ano=substr($anho,2,4);
                if ($facid!="TODO") {
                    if ($escid=="TODOEC") {
                        $where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid, 'anio' => $ano);
                    }else{
                        if ($espec<>"") {
                            if ($espec=="TODOEP") $left='S';
                            else $escid = $espec;
                        }
                        $where = array(
                            'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 
                            'anio' => $ano, 'left' => $left);
                    }
                }else $where = array('eid' => $eid, 'oid' => $oid, 'anio' => $ano);
                $egre=$user->_getTotalGraduatedXFacultyXSchoolXAnho($where);
            }
            $this->view->egresados=$egre;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function printAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $facid = base64_decode($this->_getParam('facid'));
            $escid = base64_decode($this->_getParam('escid'));
            $espec = base64_decode($this->_getParam('especialidad'));
            $perid = base64_decode($this->_getParam('perid'));
            $anho = base64_decode($this->_getParam('anho'));

            $where = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid);
            $esc = new Api_Model_DbTable_Speciality();
            $data_esc = $esc->_getFilter($where, $attrib=null, $orders=null);
            $this->view->school = $data_esc[0];

            $fac = new Api_Model_DbTable_Faculty();
            $data_fac = $fac->_getOne($where=array('eid' => $eid, 'oid' => $oid, 'facid' => $facid));
            $this->view->faculty = $data_fac;
            
            $user= new Api_Model_DbTable_Users();
            if($perid!='T'){
                if ($facid!="TODO") {
                    if ($escid=="TODOEC") {
                        $where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid, 'perid' => $perid);
                    }else{
                        if ($espec<>"") {
                            if ($espec=="TODOEP") $left='S';
                            else $escid = $espec;
                        }
                        $where = array(
                            'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 
                            'perid' => $perid, 'left' => $left);
                    }
                }else $where = array('eid' => $eid, 'oid' => $oid, 'perid' => $perid);
                $egre = $user->_getGraduatedXFacultyXSchoolXPeriod($where);
            }else{
                $ano=substr($anho,2,4);
                if ($facid!="TODO") {
                    if ($escid=="TODOEC") {  
                        $where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid, 'anio' => $ano);
                    }else{
                        if ($espec<>"") {
                            if ($espec=="TODOEP") $left='S';
                            else $escid = $espec;
                        }
                        $where = array(
                            'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 
                            'anio' => $ano, 'left' => $left);
                    }
                }else $where = array('eid' => $eid, 'oid' => $oid, 'anio' => $ano);
                $egre = $user->_getGraduatedXFacultyXSchoolXAnio($where);
            }
            $this->view->egresados=$egre;
            $this->view->facid=$facid;
            $this->view->especialidad=$espec;
            $this->view->escid=$escid;
            $this->view->perid=$perid;
            $this->view->anho=$anho;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}