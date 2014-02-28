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
                $escid=$this->sesion->escid;
                $this->view->escid=$escid;
                $facid=$this->sesion->faculty->facid;
                $this->view->facid=$facid;
            }
            if ($rid=="RF"){
                $facid=$this->sesion->faculty->facid;
                $this->view->facid=$facid;
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

      public function lschoolsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $rid=$this->sesion->rid;
            $esdirector=$this->sesion->esdirector;
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

            $curriculas=$cur->_getFilter($where);
            $this->view->curriculas=$curriculas;
            
            
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
    public function seecurriculaAction(){
        try {
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $curid=base64_decode($this->_getParam('curid'));
            $escid=base64_decode($this->_getParam('escid'));
            $subid=base64_decode($this->_getParam('subid'));
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
            $cursocur=$cursos->_getFilter($data);
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
            $state=base64_decode($this->_getParam('state'));
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

            $data = array(
                'eid'=>$eid,
                'oid'=>$oid,
                'uid'=>$uid,
                'escid'=>$escid,
                'subid'=>$subid,
                'pid'=>$pid,
                'type_impression'=>'curricula_'.$curid,
                'date_impression'=>date('Y-m-d H:i:s'),
                'pid_print'=>$uidim
                );
            // print_r($data);exit();
            $dbimpression->_save($data);            

            $wheri = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,
                'subid'=>$subid,'type_impression'=>'curricula_'.$curid);
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
        }
        catch (Exception $ex)
        {
            print "Error: Cargar Curriculas".$ex->getMessage();
        }
    }      
}