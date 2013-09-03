<?php

class Horary_NhoraryController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		//$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (!$login->rol['module']=="horary"){
    		//$this->_helper->redirector('index','index','default');
    	}
    	$this->sesion = $login;
    
    }
    public function indexAction()
    {    
     $this->_helper->redirector("listteacher");   

    }
    public function listteacherAction()
    {
        try {
            
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid=$this->sesion->period->perid;
            $escid=$this->sesion->escid;
             $d=new Api_Model_DbTable_PeriodsCourses();
             $where['eid']=$eid;
             $where['oid']=$oid;
             $where['perid']=$perid;
             $where['escid']=$escid;

            $cur=$d->_getTeacherXPeridXEscid($eid,$oid,$escid,$perid);
            //$cur=$d->_getTeacherXPeridXEscid1($where);
            //print_r($cur);
            $this->view->docente=$cur;
            

            $this->view->cursos=$datcur;
        } catch (Exception $ex) {
            print $ex->getMessage();
        }
    }

    public function fillhoraryAction()
    {
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $perid=$this->sesion->period->perid;
        $escid=$this->sesion->escid;
        $subid=$this->sesion->subid;
        $pid=base64_decode($this->_getParam('pid'));
        $uid=base64_decode($this->_getParam('uid'));
        $where['eid']=$eid;
        $where['oid']=$oid;
        $where['perid']=$perid;
        $where['escid']=$escid;
        $where['subid']=$subid;
        $where['pid']=$pid;
        $curso=new Api_Model_DbTable_Coursexteacher();
        $datcur=$curso->_getFilter($where);
        $this->view->cursos=$datcur;
        $usu=new Api_Model_DbTable_Users();
        $data['eid']=$eid;
        $data['oid']=$oid;
        $data['uid']=$uid;
        $dusu=$usu->_getUserXUid($data);
        $this->view->usuario=$dusu;
        $hora=new Api_Model_DbTable_Horary();
        $wher['eid']=$eid;
        $wher['oid']=$oid;
        $wher['perid']=$perid;
        $wher['subid']=$subid;
        $wher['teach_pid']=$pid;
        $wher['teach_uid']=$uid;

        $dathora=$hora->_getFilter($wher);
        if ($dathora) $this->view->dathora=1;
    }
    public function validatetimeAction()
    {    
        $this->_helper->layout()->disableLayout();
        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $perid=$this->sesion->period->perid;
        $escid=$this->sesion->escid;
        $subid=$this->sesion->subid;
        $courseid=$this->_getParam('courseid');
        $curid=$this->_getParam('curid');
        $turno=$this->_getParam('turno');
        $semid=$this->_getParam('semid');
        $uid=$this->_getParam('uid');
        $pid=$this->_getParam('pid');
        $hora_ini=$this->_getParam('hora_ini');  
        $hora_acad=$this->_getParam('hora_acad');
        $dia=$this->_getParam('dia');
        $tipo_clase=$this->_getParam('tipoclase');

        //Obtenemos un array con todas las horas academicas establecidas.
        $hora=new Api_Model_DbTable_Horary();
        $valhoras[0]='06:20:00';
        for ($k=0; $k < 20; $k++) { 
            $dho=$hora->_getsumminutes($valhoras[$k],'50');
            $valhoras[$k+1]=$dho[0]['hora'];
        }

        // valida que la hora de inicio sea multiplo de 50 min.
        for ($zz=0; $zz < 20; $zz++) { 
            if ($hora_ini==$valhoras[$zz]) {
                $valini=1;
            }
        }
        if ($valini=="1") {
            //Sacamos de hora de finalizacion deacuerdo a la cantidad de horas academicas.
            $hora_fin[0]['hora']=$hora_ini;
            for ($x=0; $x < $hora_acad; $x++) { 
                $hora_fin=$hora->_getsumminutes($hora_fin[0]['hora'],'50');
            }
            $hora_fin=$hora_fin[0]['hora'];
        }


     

    }

    public function horaryteacherAction()
    {    
       

    }
    
   
}