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

}
