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
        //  $this->_helper->redirector('index','index','default');
        // }
        $this->sesion = $login;
    }
    
    public function indexAction()
    {
        try {
        $facultyDb = new Api_Model_DbTable_Faculty();
        $schooldDb = new Api_Model_DbTable_Speciality();

        $period = $this->sesion->period->perid;
        $rid    = $this->sesion->rid;
        $eid    = $this->sesion->eid;
        $oid    = $this->sesion->oid;
        $facid  = $this->sesion->faculty->facid;
        $subid  = $this->sesion->subid;
        $escid  = $this->sesion->escid;

        $dataVista['period']    = $period;
        $dataVista['rid']       = $rid;
        $dataVista['escid']     = $escid;
        $dataVista['subid']     = $subid;
        $dataVista['facid']     = $facid;
        $dataVista['dataEscid'] = base64_encode($escid.'|'.$subid);
       

        if ($rid == 'RF') {
            $c=0;
            $dataVista['dataEscid']  = '';

            $where = array( 'eid'   => $eid,
                            'oid'   => $oid,
                            'facid' => $facid,
                            'subid' => $subid,
                            'state' => 'A',
                            'parent' => '' );
            $attrib = array('name', 'escid', 'subid', 'parent');
            $preDataSchool = $schooldDb->_getFilter($where, $attrib);
            foreach ($preDataSchool as $c => $school) {
                $dataSchool[$c]['escid'] = $school['escid'];
                $dataSchool[$c]['subid'] = $school['subid'];
                $dataSchool[$c]['name']  = $school['name'];
            }
            $this->view->dataSchool = $dataSchool;
        }elseif ($rid == 'RC'){
            $dataVista['dataEscid']  = '';
            $preDataFaculty = $facultyDb->_getAll();
            foreach ($preDataFaculty as $c => $faculty) {
                if ($faculty['state'] == 'A' and  $faculty['facid'] != "TODO" ) {
                    $dataFaculty[$c]['facid'] = $faculty['facid'];
                    $dataFaculty[$c]['name']  = $faculty['name'];
                }
            }
            $this->view->dataFaculty = $dataFaculty; 
        }
        $this->view->dataVista = $dataVista;
           
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
            
            if ($facid!="TODOFAC"){
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
            if ($escid!="TODOESC"){
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
            $eid   = $this->sesion->eid;
            $oid   = $this->sesion->oid;
            $perid = $this->sesion->period->perid;
            $anio  = $this->_getParam("anio");
            $this->view->perid = $perid;
            if ($eid=="" || $oid==""|| $anio=="") return false;
            $p = substr($anio, 2, 3);
            $p1=$p."A";
            $p2=$p."B";
            $where = array('eid' => $eid, 'oid' => $oid, 'p1' => $p1, 'p2' => $p2);
            $periodos = new Api_Model_DbTable_Periods();
            $this->view->lper = $periodos->_getPeriodsXAyB($where);
            $this->view->an = $p;
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
            // base de datos
            $msj = "";
            if ($facid!='' and $escid!='' and $perid!='') {
                $temp=substr($perid, 0, -1);//año
                $temp1=substr($perid,-1);//peri
                $left='';

                $user= new Api_Model_DbTable_Users();

                if($temp1!='T'){
                    if ($facid!="TODOFAC") {
                        if ($escid=="TODOESC") {
                            $where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid, 'perid' => $perid);
                        }else{
                            if ($espec <> "") {
                                if ($espec=="TODOESP") $left='S';
                                else $escid = $espec;
                            }
                            $where = array(
                                'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'facid' => $facid,
                                'perid' => $perid, 'left' => $left);
                        }
                    }else $where = array('eid' => $eid, 'oid' => $oid, 'perid' => $perid);

                    $egre = $user->_getGraduatedXFacultyXSchoolXPeriod($where);
                }else{
                    if ($facid!="TODOFAC") {
                        if ($escid=="TODOESC") {  
                            $where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid, 'anio' => $temp);
                        }else{
                            if ($espec<>"") {
                                if ($espec=="TODOESP") $left='S';
                                else $escid = $espec;
                            }
                            $where = array(
                                'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 
                                'anio' => $temp, 'left' => $left);
                        }
                    }else $where = array('eid' => $eid, 'oid' => $oid, 'anio' => $temp);
                    $egre = $user->_getGraduatedXFacultyXSchoolXAnio($where);
                }
                $this->view->egresados=$egre;
            } else{
                $msj = "Lo sentimos, tenemos un Error, Comunicarse a la oficina de INFORMATICA";
            }

/*
            $this->view->facid=$facid;
            $this->view->especialidad=$espec;
            $this->view->escid=$escid;
            $this->view->perid=$perid;
            $this->view->anho=$anho;

            $left='';

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
                            'eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'facid' => $facid,
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
            $this->view->egresados=$egre; */
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

            // $where = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid);
            // $esc = new Api_Model_DbTable_Speciality();
            // $data_esc = $esc->_getFilter($where, $attrib=null, $orders=null);
            // $this->view->school = $data_esc[0];

            // $fac = new Api_Model_DbTable_Faculty();
            // $data_fac = $fac->_getOne($where=array('eid' => $eid, 'oid' => $oid, 'facid' => $facid));
            // $this->view->faculty = $data_fac;
            
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

            $whered['eid']=$eid;
            $whered['oid']=$oid;
            $whered['facid']= $facid;
            $dbfaculty = new Api_Model_DbTable_Faculty();
            $faculty = $dbfaculty ->_getOne($whered);
            $namef = strtoupper($faculty['name']);

            $wheres['eid']=$eid;
            $wheres['oid']=$oid;
            $wheres['escid']=$escid;
            $wheres['facid']=$facid;
              
            $spe=array();
            $dbspeciality = new Api_Model_DbTable_Speciality();
            $speciality = $dbspeciality ->_getFilter($wheres);                  
            $parent=$speciality[0]['parent'];
            $subid=$speciality[0]['subid'];
    
            $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
            $parentesc= $dbspeciality->_getOne($wher);
            if ($parentesc) {
                $pala='ESPECIALIDAD DE ';
                $spe['esc']=$parentesc['name'];
                $spe['parent']=$pala.$speciality[0]['name'];
            }
            else{
                $spe['esc']=$speciality[0]['name'];
                $spe['parent']='';      
            }
            $names=strtoupper($spe['esc']);
            $namep=strtoupper($spe['parent']);
            $namefinal=$names." <br> ".$namep;            
                  
            $namelogo = (!empty($speciality[0]['header']))?$speciality[0]['header']:"blanco";

            $dbimpression = new Api_Model_DbTable_Countimpressionall();

            $uid=$this->sesion->uid;
            $uidim=$this->sesion->pid;
            $pid=$uidim;

            $data = array(
                'eid'=>$eid,
                'oid'=>$oid,
                'uid'=>$uid,
                'escid'=>$escid,
                'subid'=>$subid,
                'pid'=>$pid,
                'type_impression'=>'impresion_egresados_'.$perid,
                'date_impression'=>date('Y-m-d H:i:s'),
                'pid_print'=>$uidim
                );
            $dbimpression->_save($data);            

            $wheri = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'impresion_egresados_'.$perid);
            $dataim = $dbimpression->_getFilter($wheri);
            
            $co=count($dataim);
            $codigo=$co." - ".$uidim;

            $header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
            $header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", $namelogo, $header);
            $header = str_replace("?codigo", $codigo, $header);
            
            $this->view->header=$header;
            $this->view->footer=$footer;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}