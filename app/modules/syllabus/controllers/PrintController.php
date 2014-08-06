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

            $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";

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

            $wherefac['eid']=$eid;
            $wherefac['oid']=$oid;
            $wherefac['facid']=$speciality['facid'];
            $fac = new Api_Model_DbTable_Faculty();
            $facu = $fac ->_getOne($wherefac);
            $namef=strtoupper($facu['name']);
            
            $whereperi['eid']=$eid;
            $whereperi['oid']=$oid;
            $whereperi['perid']=$perid;

            //DataBases
            $tb_period = new Api_Model_DbTable_Periods();
            //
            $where_per = array('eid' => $this->sesion->eid,'oid' => $this->sesion->oid,'perid' => $perid);
            $data_period =$tb_period->_getOnePeriod($where_per);

            $date_stard_t = $this->converterdate((string)$data_period['class_start_date']);
            $date_end_t = $this->converterdate((string)$data_period['class_end_date']);

            $this->view->date_stard = $date_stard_t;
            $this->view->date_end =  $date_end_t;           

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

            // $escid=$this->sesion->escid;
            // $where['escid']=$escid;
            
            $pid=$direc[0]['pid'];

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
                'code'=>'silabo'
                );

            $dbimpression->_save($data);            

            $wheri = array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'courseid'=>$courseid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid,'turno'=>$turno,'code'=>'silabo');
            $dataim = $dbimpression->_getFilter($wheri);
            $co=count($dataim);
            $codigo=$co." - ".$uidim;

            $header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
            $header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", $namelogo, $header);
            $header = str_replace("?codigo", $codigo, $header);
            $header = str_replace("h2", "h3", $header);
            $header = str_replace("h3", "h5", $header);
            $header = str_replace("h4", "h6", $header);
            $header = str_replace("11%", "12%", $header);

            $footer = str_replace("h4", "h5", $footer);
            $footer = str_replace("h5", "h6", $footer);
            
            $this->view->header=$header;
            $this->view->footer=$footer;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    function converterdate($date = null){
            $date_literal = split('-', $date);
            if ($date !='') {
                 switch ($date_literal['1']) {
                    case 1: $strm = "Enero";break;
                    case 2: $strm = "Febrero";break;
                    case 3: $strm = "Marzo";break;
                    case 4: $strm = "Abril";break;
                    case 5: $strm = "Mayo";break;
                    case 6: $strm = "Junio";break;
                    case 7: $strm = "Julio";break;
                    case 8: $strm = "Agosto";break;
                    case 9: $strm = "Setiembre";break;
                    case 10: $strm = "Octubre";break;
                    case 11: $strm = "Noviembre";break;
                    case 12: $strm = "Diciembre";break;
                }
                $date = $date_literal[2]." de ".$strm." del ".$date_literal[0];
            } 
        return $date;
    }
}