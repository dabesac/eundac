<?php
class Curricula_ShowController extends Zend_Controller_Action
{
    public function init(){
           $sesion  = Zend_Auth::getInstance();
            if(!$sesion->hasIdentity() ){
                  //$this->_helper->redirector('index',"index",'default');
            }
            $login = $sesion->getStorage()->read();
            // if (!$login->rol['module']=="docente"){
            //       $this->_helper->redirector('index','index','default');
            // }
            $this->sesion = $login;
    }
    public function indexAction(){
        try {
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $rid=$this->sesion->rid;
            $escid=$this->sesion->escid;
            $facu=new Api_Model_DbTable_Faculty();
            $where['eid']=$eid;
            $where['oid']=$oid;
            $facultad=$facu->_getAll($where);
            $this->view->facultad=$facultad;
            if ($rid=="DR" && $this->sesion->infouser['teacher']['is_director']=="S"){
                $this->_helper->redirector('viewmanager','show','curricula');
            }
            if ($rid=="RF"){
                $facid=$this->sesion->faculty->facid;
                $this->view->facid=$facid;
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
    public function viewmanagerAction(){
        try {
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $facid=$this->sesion->faculty->facid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;

            $escDb=new Api_Model_DbTable_Speciality();
            //$allSpeciality =$escDb->_getFilter($where,$attrib);
            $where1 = array('eid'=>$eid,'oid'=>$oid,'facid'=>$facid,'subid'=>$subid,'parent'=>$escid);
            $attrib = array('escid','name','state');
            $order = array('name');
            $especialidades = $escDb->_getFilter($where1,$attrib,$order);
            $this->view->especialidades = $especialidades;
            
            $datos["eid"] = $this->sesion->eid;
            $datos["oid"] = $this->sesion->oid;           
            $datos["facName"] = $this->sesion->faculty->name;
            $datos["facid"] = $this->sesion->faculty->facid;
            $datos["escName"] = $this->sesion->speciality->name;
            $datos["escid"] = $this->sesion->escid;
            
            $this->view->datos = $datos;
            //print_r($this->sesion);
        } catch (Exception $e) {
            print "Error".$e ->getMessage();
        }
    }
      public function lschoolsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $rid=$this->sesion->rid;
            $facid=$this->_getParam('facid');
            $esc=new Api_Model_DbTable_Speciality();
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['facid']=$facid;

            $escuelas=$esc->_getFilter($where);
            $this->view->escuelas=$escuelas;

            if ($rid=="DR" && $this->sesion->infouser['teacher']['is_director']=="S"){
                $escid=$this->sesion->escid;
                $this->view->escid=$escid;
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
     public function curriculasAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $rid=$this->sesion->rid;
            $this->view->rid=$rid;
            $escid=$this->_getParam('escid');
            $cur=new Api_Model_DbTable_Curricula();
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $curriculas2=$cur->_getFilter($where);
            if ($rid=="DR" && $this->sesion->infouser['teacher']['is_director']=="S"){
                $where['state'] = "T";
                $curriculas0=$cur->_getFilter($where);
                $where['state'] = "A";
                $curriculas1 =$cur->_getFilter($where);

                if($curriculas0 xor $curriculas1){
                    if ($curriculas0) {
                        $this->view->curriculas=$curriculas0;
                    }elseif ($curriculas1){
                        $this->view->curriculas=$curriculas1;
                    }
                }elseif ($curriculas0 and $curriculas1){
                    $this->view->curriculas = array_merge($curriculas0,$curriculas1);
            }}else{
            $this->view->curriculas=$curriculas2;
            }
            
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
    public function seecurriculaAction(){
        try {
            $rid=$this->sesion->rid;
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $curid=base64_decode($this->_getParam('curid'));
            $escid=base64_decode($this->_getParam('escid'));
            $subid=base64_decode($this->_getParam('subid'));
            $facid=substr($escid,0,1);

            $this->view->facid=$facid;
            $this->view->eid=$eid;
            $this->view->oid=$oid;
            $this->view->escid=$escid;
            $this->view->subid=$subid;
            $this->view->curid=$curid;
            
            $rid=$this->sesion->rid;
            $this->view->rid=$rid;
            $cur= new Api_Model_DbTable_Curricula();
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['subid']=$subid;
            $where['curid']=$curid;

            $curricula=$cur->_getOne($where);
            
            $this->view->curricula=$curricula;
            $curidant=$cur->_getCurriculaAnterior($curid,$escid);
            $this->view->curidant=$curidant[0]['curricula_ant'];
            $semestre=$cur->_getSemesterXCurricula($curid,$subid,$escid,$oid,$eid);
            $this->view->semestre=$semestre;
            $cursos= new Api_Model_DbTable_Course();
            $data['eid']=$eid;
            $data['oid']=$oid;
            $data['escid']=$escid;
            $data['curid']=$curid;
            $order=array('semid','courseid');
            $cursocur=$cursos->_getFilter($data,$attrib=null,$order);
            // print_r($cursocur);exit();
            $this->view->cursos=$cursocur;
            $esc = new Api_Model_DbTable_Speciality();
            $dat['eid']=$eid;
            $dat['oid']=$oid;
            $dat['escid']=$escid;
            $datesc=$esc->_getFilter($dat);
            $this->view->escuela=$datesc;

        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function printAction(){
        try
        {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            // $uid_update = $this->sesion->uid;
            // $f_update = date("Y-m-d");
            $escid=base64_decode($this->_getParam('escid'));
            $subid=base64_decode($this->_getParam('subid'));
            $curid=base64_decode($this->_getParam('curid'));
            // $state=base64_decode($this->_getParam('state'));
            $this->view->curid=$curid;

            // $this->view->eid=$eid;
            // $this->view->oid=$oid;
            // $this->view->escid=$escid;
            // $this->view->subid=$subid;
            // $this->view->state=$state;
            $bdcurricula = new Api_Model_DbTable_Curricula();
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['subid']=$subid;
            $where['curid']=$curid;
            $lcurricula=$bdcurricula->_getOne($where);
            $this->view->nombre_curricula=$lcurricula["name"];
            $semestre=$bdcurricula->_getSemesterXCurricula($curid,$subid,$escid,$oid,$eid);

            $bdcursos = new Api_Model_DbTable_Course();
            $where1= array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid);
            $attrib=array('semid','courseid','name','type','credits','hours_theoretical','hours_practical',
                        'req_1','req_2','req_3','course_equivalence','course_equivalence_2');
            $order=array('courseid');
            $i=0;
            foreach ($semestre as $semestre) {
                $where1['semid']=$semestre['semid'];
                $semestres[$i]['semid']=$semestre['semid'];
                $semestres[$i]['name']=$semestre['name'];
                $cursos=$bdcursos->_getFilter($where1,$attrib,$order);
                $semestres[$i]['cursos']=$cursos;
                $i++;
            }
            
            $this->view->datasemestre=$semestres;

            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
            $base_speciality =  new Api_Model_DbTable_Speciality();        
            $speciality = $base_speciality ->_getOne($where);
            $parent=$speciality['parent'];
            $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
            $parentesc= $base_speciality->_getOne($wher);

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
            $namev=$names." ".$namep;
            $this->view->namev=$namev;
            $namefinal=$names." <br> ".$namep;

            $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";
            
            $fac = array('eid'=>$eid,'oid'=>$oid,'facid'=>$speciality['facid']);
            $base_fac =  new Api_Model_DbTable_Faculty();        
            $datafa= $base_fac->_getOne($fac);
            $namef = strtoupper($datafa['name']);  

            $dbimpression = new Api_Model_DbTable_Countimpressionall();
            
            $uid=$this->sesion->uid;
            $uidim=$this->sesion->pid;
            $pid=$uidim;

            $wheri = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,
                'subid'=>$subid,'type_impression'=>'curricula_'.$curid);
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
                    'type_impression'=>'curricula_'.$curid,
                    'date_impression'=>date('Y-m-d H:i:s'),
                    'pid_print'=>$uidim,
                    'count_impression'=>1
                    );
                $dbimpression->_save($data);               
                $co=1;
            }

            $codigo=$co." - ".$uidim;

            $header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
            $header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", $namelogo, $header);
            $header = str_replace("?codigo", $codigo, $header);
          
            $this->view->header=$header;

            $this->view->footer=$footer;
        }
        catch (Exception $ex)
        {
            print "Error: Cargar Curriculas".$ex->getMessage();
        }
    }      
    public function seecurriculamanagerAction(){
        try {
            $datos["eid"]=$this->sesion->eid;
            $datos["oid"]=$this->sesion->oid;
            $datos["escName"]=$this->sesion->speciality->name;
            $datos["curid"]=base64_decode($this->_getParam('curid'));
            $datos["escid"]=base64_decode($this->_getParam('escid'));
            $datos["subid"]=base64_decode($this->_getParam('subid'));
            $datos["facid"]=substr($escid,0,1);
            $rid=$this->sesion->rid;
            $this->view->rid=$rid;
            $cur= new Api_Model_DbTable_Curricula();
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['subid']=$subid;
            $where['curid']=$curid;
            $coursesDb = new Api_Model_DbTable_Course();
            $this->view->semestre=$semestre;
            $cursos = $coursesDb->_getCoursesXCurriculaXShool($datos["eid"],$datos["oid"],$datos["curid"],$datos["escid"]);
            $this->view->escuela=$datos["escName"];
            $this->view->curid=$datos["curid"];
            $this->view->escid=$datos["escid"];


            $this->view->cursos=$cursos;

        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
    public function saveAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $server = new Eundac_Connect_openerp();
            $datos["eid"]=$this->sesion->eid;
            $datos["oid"]=$this->sesion->oid;

            $formData = $this->getRequest()->getPost();

            $formData['escid'] = base64_decode($formData['escid']);
            $formData['curid'] = base64_decode($formData['curid']);
            //enviando datos al serv

            $eid   = $this->sesion->eid;
            $oid   = $this->sesion->oid;

            $where = array( 'eid'   => $eid,
                            'oid'   => $oid,
                            'escid' => $formData['escid'],
                            'subid' => $subid,
                            'curid'   => $formData['curid'] 
                            );
            
            $curriculaDb = new Api_Model_DbTable_Curricula();
            $curricula = $curriculaDb->_getOne($where);

            if ($curricula) {
                print_r("hay");
                print_r($curricula);
            }else{
                print_r("no hay");
            }

        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}