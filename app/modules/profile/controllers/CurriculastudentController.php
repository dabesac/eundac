<?php

class Profile_CurriculastudentController extends Zend_Controller_Action {

    public function init(){
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;

    }

    public function indexAction()
    {

        echo "ffdfsf";
    }



    public function coursecurriculaAction() 
    {
        //$this->_helper->layout()->disableLayout();
        //$escid =$this->_getParam('escid');
        //$sedid =$this->_getParam('sedid');

        // $uid =$this->_getParam('uid');
        // $pid =$this->_getParam('pid');
        // $eid =$this->_getParam('eid');
        // $oid =$this->_getParam('oid');
        
       // print_r($this->sesion);break;

        $escid=$this->sesion->escid;
        $subid=$this->sesion->subid;
        $perid=$this->sesion->period->perid;  
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $pid=$this->sesion->pid;
        $uid=$this->sesion->uid;

        $this->view->escid = $escid;
        $this->view->perid = $perid;      
        $this->view->subid = $subid;
        $this->view->pid = $pid;
        $this->view->uid = $uid;
        $this->view->oid = $oid;
        $this->view->eid = $eid;

        // $dbcuract=new Api_Model_DbTable_Registrationxcourse();
        // $where=array("eid"=>$eid, "oid"=>$oid, "pid"=>$pid, "uid"=>$uid, "perid"=>$perid);     

        // $curact=$dbcuract->_getFilter($where, $attrib);        
        // echo "fsdgsgs";
        // print_r($where);//break;
    
        // $dbcursos=new Admin_Model_DbTable_Cursos();
        // $datcursos=$dbcursos->_getTodosCursosXCurriculaXEscuelayNotas($eid,$oid,$curid,$escid,$uid);
        // //print_r($datcursos);
        // $this->view->data=$datcursos;

        // $bdrecord = new Admin_Model_DbTable_Matriculacurso();
        // $datarecord=$bdrecord->_getRecordNotasAlumno($escid,$uid);
        // //print_r($datarecord);
        // $this->view->cursosmatricula=$datarecord;  
    
    }








}
