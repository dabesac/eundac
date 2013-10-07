<?php

class Alumno_IndexController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	$this->sesion = $login;
    }
    public function indexAction()
    {

    	try {
            $pid=$this->sesion->pid;
            $this->view->pid=$pid;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }

    }

    public function graphicsperformanceAction()
    {
        try
        {
            $this->_helper->layout()->disableLayout();         
            $where['uid']=$this->sesion->uid;
            $where['eid']=$this->sesion->eid;
            $where['oid']=$this->sesion->oid;
            $where['pid']=$this->sesion->pid;
            $where['escid']=$this->sesion->escid;
            $where['subid']=$this->sesion->subid;
            $this->view->escid = $where['escid'];

            $this->view->uid = $where['uid'];
            $this->view->oid = $where['oid'];
            $this->view->eid = $where['eid'];
            $dbcurricula=new Api_Model_DbTable_Studentxcurricula();
            $datcur=$dbcurricula->_getOne($where);
            $where['curid']=$datcur['curid'];
            $this->view->curid = $where['curid'];
            $dbcursos=new Api_Model_DbTable_Course();
            $datcursos=$dbcursos->_getCountCoursesxSemester($where);
            // print_r($datcursos);
            $this->view->data=$datcursos;
            $cur=$dbcursos->_getCountCoursesxApproved($where);
            // print_r($cur);
            $this->view->cursos=$cur;
        }
        catch(Exception $ex)
        {
              print $ex->getMessage();
        }                  
                
    }


    public function assistanceAction()
    {
        try {
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $perid=$this->sesion->period->perid;
            $uid=$this->sesion->uid;
            $pid=$this->sesion->pid;
            $regid = $uid.$perid;

            $where = null;
            $where = array(
                'eid'=>$eid,'oid'=>$oid,
                'escid'=>$escid,'subid'=>$subid,
                'perid'=>$perid,'uid'=>$uid,
                'pid'=>$pid,'regid'=>$regid,
                );
            $base_assistance_student = new Api_Model_DbTable_StudentAssistance();
            $assistance_student = $base_assistance_student->_getFilter($where);
            
            if ($assistance_student) {
                $base_course = new Api_Model_DbTable_Course();
                foreach ($assistance_student as $key => $value) {
                    $where['courseid']=$value['coursoid'];
                    $where['curid']=$value['curid'];
                    $name = $base_course->_getOne($where);
                    $assistance_student[$key]['name']=$name['name'];
                }
            }
            
            $this->view->assitance=$assistance_student;

        } catch (Exception $e) {
            print "Error Asistencia Alumnno".$e->gegtMessage();
        }
    }

        public function graphicsassistanceAction()
    {
        try
        {
            $this->_helper->layout()->disableLayout();         
            $wheres['uid']=$this->sesion->uid;
            $wheres['eid']=$this->sesion->eid;
            $wheres['oid']=$this->sesion->oid;
            $wheres['pid']=$this->sesion->pid;
            $wheres['escid']=$this->sesion->escid;
            $wheres['subid']=$this->sesion->subid;
            $wheres['perid']=$this->sesion->period->perid;

            $this->view->escid = $wheres['escid'];
            $this->view->uid = $wheres['uid'];
            $this->view->oid = $wheres['oid'];
            $this->view->eid = $wheres['eid'];
            $lcursos = new Api_Model_DbTable_StudentAssistance();
            $listacurso =$lcursos->_assistence($wheres);
            $j=0;
            foreach ($listacurso as $cursomas){
                $where[$j]['eid']=$wheres["eid"];
                $where[$j]['oid']=$wheres["oid"];
                $where[$j]['escid']=$wheres["escid"];
                $where[$j]['subid']=$wheres["subid"];
                $where[$j]['perid']=$wheres["perid"];
                $where[$j]['courseid']=$cursomas["coursoid"];
                $where[$j]['name']=$cursomas["name"];
                $where[$j]['curid']=$cursomas["curid"];
                $where[$j]['turno']=$cursomas["turno"];
                $periods = new Api_Model_DbTable_PeriodsCourses();
                $state =$periods->_getOne($where);
                $var=$state['state'];
                if($var=='A'){
                    $a=1;
                }
                if($var=='P'){
                     $a=18;
                }      
                $x=0;
                $x0=0;
                $x1=0;
                for ($i=$a; $i < 35 ; $i++) { 
                        $assis=$cursomas["a_sesion_".$i];
                    if ($assis=='A' ) {
                        $x++;
                    }
                    if ( $assis=='T') {
                        $x0++;
                    }
                     if ($assis=='F') {
                        $x1++;
                    }
                }
                $where[$j]['asistio']=$x;
                $where[$j]['tarde']=$x0;
                $where[$j]['falto']=$x1;

                if ($x1>=6) {
                $where[$j]['coment']='R';
                    }
                else{
                $where[$j]['coment']='N';

                }


                   $j++;
        } 
        $this->view->assistence=$where; 

        }
        catch(Exception $ex)
        {
              print $ex->getMessage();
        }                  
                
    }
}
