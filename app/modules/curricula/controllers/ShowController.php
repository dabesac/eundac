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

            //  if ($rid=="DC" && $esdirector=="S"){
            //     $escid=$this->sesion->escid;
            //     $this->view->escid=$escid;
            // }
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
            //$rid=$this->sesion->rid;
            $esdirector=$this->sesion->esdirector;
            $facid=$this->_getParam('facid');
            $esc=new Api_Model_DbTable_Speciality();
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['facid']=$facid;

            $escuelas=$esc->_getFilter($where);
            $this->view->escuelas=$escuelas;

            // if ($rid=="DC" && $esdirector=="S"){
            //     $escid=$this->sesion->escid;
            //     $this->view->escid=$escid;
            // }
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

    public function printAction() 
    {
        try
        {
            //$this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $uid_update = $this->sesion->uid;
            $f_update = date("Y-m-d");

            $escid=base64_decode($this->_getParam('escid'));
            $subid=base64_decode($this->_getParam('subid'));
            $curid=base64_decode($this->_getParam('curid'));
            $state=base64_decode($this->_getParam('state'));

            $this->view->eid=$eid;
            $this->view->oid=$oid;
            $this->view->escid=$escid;
            $this->view->subid=$subid;
            $this->view->curid=$curid;
            $this->view->state=$state;
            $bdcurricula = new Api_Model_DbTable_Curricula();
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['subid']=$subid;
            $where['curid']=$curid;
            $lcurricula=$bdcurricula->_getOne($where);
            $this->view->nombre_curricula=$lcurricula["name"];

            $semestre=$bdcurricula->_getSemesterXCurricula($curid,$subid,$escid,$oid,$eid);
           $this->view->semestre=$semestre;  
        }
        catch (Exception $ex)
        {
            print "Error: Cargar Curriculas".$ex->getMessage();
        }
    }


	
      
}