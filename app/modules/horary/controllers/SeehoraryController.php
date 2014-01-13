<?php

class Horary_SeehoraryController extends Zend_Controller_Action {

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
        // print_r($this->sesion);
    
    }
    public function indexAction()
    {
        try {
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid=$this->sesion->period->perid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $pid=$this->sesion->pid;
            $uid=$this->sesion->uid;

            $wheres=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
            $bd_hours= new Api_Model_DbTable_HoursBeginClasses();
            $datahours=$bd_hours->_getFilter($wheres);   
            
            if ($datahours) {
                $hora=new Api_Model_DbTable_Horary();
                $valhoras[0]=$datahours[0]['hours_begin'];
                for ($k=0; $k < 20; $k++) { 
                    $dho=$hora->_getsumminutes($valhoras[$k],'50');
                    $valhoras[$k+1]=$dho[0]['hora'];
                }
                $this->view->valhoras=$valhoras;

                $where['eid']=$eid;
                $where['oid']=$oid;
                $where['perid']=$perid;
                $where['subid']=$subid;
                $where['teach_uid']=$uid;
                $where['teach_pid']=$pid;
                $order=array('courseid');
                // print_r($where);
                $dathora=$hora->_getFilter($where,'',$order);
                // print_r($dathora);
                $this->view->horarios=$dathora;
                $curso=new Api_Model_DbTable_Coursexteacher();
                $where1['eid']=$eid;
                $where1['oid']=$oid;
                $where1['escid']=$escid;
                $where1['perid']=$perid;
                $where1['subid']=$subid;
                $where1['pid']=$pid;
                $datcur=$curso->_getFilter($where1);
                $this->view->cursos=$datcur;
            }

        } catch (Exception $ex) {
            print "Error: See Horary".$ex->getMessage();
        }
    }

    public function printAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid=$this->sesion->period->perid;
            $faculty=$this->sesion->faculty->name;
            $this->view->faculty=$faculty;

            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $pid=$this->sesion->pid;

            $uid=$this->sesion->uid;
            $this->view->uid=$uid;

            $wheres=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
            $bd_hours= new Api_Model_DbTable_HoursBeginClasses();
            $datahours=$bd_hours->_getFilter($wheres);   
            $valhoras[0]=$datahours[0]['hours_begin'];
            $hora=new Api_Model_DbTable_Horary();
            for ($k=0; $k < 20; $k++) { 
                $dho=$hora->_getsumminutes($valhoras[$k],'50');
                $valhoras[$k+1]=$dho[0]['hora'];
            }
            $this->view->valhoras=$valhoras;

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['perid']=$perid;
            $where['subid']=$subid;
            $where['teach_uid']=$uid;
            $where['teach_pid']=$pid;
            $order=array('courseid');
            // print_r($where);
            $dathora=$hora->_getFilter($where,'',$order);
            // print_r($dathora);
            $this->view->horarys=$dathora;
            $curso=new Api_Model_DbTable_Coursexteacher();
            $where1['eid']=$eid;
            $where1['oid']=$oid;
            $where1['escid']=$escid;
            $where1['perid']=$perid;
            $where1['subid']=$subid;
            $where1['pid']=$pid;
            $datcur=$curso->_getFilter($where1);
            // print_r($datcur);
            $this->view->cursos=$datcur;

            $spe=array();
            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
            $esc = new Api_Model_DbTable_Speciality();
            $desc = $esc->_getOne($where);
            $parent=$desc['parent'];
            $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
            $parentesc= $esc->_getOne($wher);
            if ($parentesc) {
                $pala='ESPECIALIDAD DE ';
                $spe['esc']=$parentesc['name'];
                $spe['parent']=$pala.$desc['name'];
                $this->view->spe=$spe;
            }
            else{
                $spe['esc']=$desc['name'];
                $spe['parent']='';  
                $this->view->spe=$spe;
            }

            $wheres=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'uid'=>$uid,'subid'=>$subid,'pid'=>$pid); 
            $user = new Api_Model_DbTable_Users();
            $duser = $user->_getInfoUser($wheres);
            // print_r($duser);
            $this->view->duser=$duser;
        } catch (Exception $ex) {
            print $ex->getMessage();
        }
    }
    
   
}
