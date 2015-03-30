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
            $this->_helper->layout()->disableLayout();

            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            // $perid=$this->sesion->period->perid;
            $perid=base64_decode($this->_getParam('perid'));
            $escid=base64_decode($this->_getParam('escid'));
            $subid=base64_decode($this->_getParam('subid'));
            if (substr($escid,0,3)=="2ES") {
                $escid="2ES";
            }
            // $escid=$this->sesion->escid;
            // $subid=$this->sesion->subid;
            $pid=$this->sesion->pid;
            $uid=$this->sesion->uid;

            $wheres=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
            $bd_hours= new Api_Model_DbTable_HoursBeginClasses();
            $datahours=$bd_hours->_getFilter($wheres);
            if ($datahours) {
                $hora=new Api_Model_DbTable_Horary();
                if ($datahours[0]['hours_begin_afternoon']) {
                    $valhorasm[0]=$datahours[0]['hours_begin'];
                    $valhorast[0]=$datahours[0]['hours_begin_afternoon'];
                    $k=0;
                    while ($valhorasm[$k] < $datahours[0]['hours_begin_afternoon']) {
                        $dho=$hora->_getsumminutes($valhorasm[$k],'50');
                        $valhorasm[$k+1]=$dho[0]['hora'];
                        $k++;
                    }
                    $len=count($valhorasm);
                    $w=0;
                    // print_r($len);exit();
                    for ($g=0; $g < $len; $g++) {
                        if ($valhorasm[$g]==$valhorast[0] && $w==0) {
                            $valhoras[0]=$datahours[0]['hours_begin'];
                            for ($k=0; $k < 20; $k++) {
                                $dho=$hora->_getsumminutes($valhoras[$k],'50');
                                $valhoras[$k+1]=$dho[0]['hora'];
                            }
                            $this->view->valhoras=$valhoras;
                            $w=1;
                        }
                    }
                    if ($w==0) {
                        unset($valhorasm[$k]);
                        $this->view->valhorasm=$valhorasm;
                        $j=0;
                        while ( $j < 12) {
                            $dho=$hora->_getsumminutes($valhorast[$j],'50');
                            $valhorast[$j+1]=$dho[0]['hora'];
                            $j++;
                        }
                        $endtarde=$valhorast[$j-1];
                        $this->view->valhorast=$valhorast;
                    }
                }
                else{
                    $valhoras[0]=$datahours[0]['hours_begin'];
                    for ($k=0; $k < 20; $k++) {
                        $dho=$hora->_getsumminutes($valhoras[$k],'50');
                        $valhoras[$k+1]=$dho[0]['hora'];
                    }
                    $this->view->valhoras=$valhoras;
                }

                $where['eid']=$eid;
                $where['oid']=$oid;
                $where['perid']=$perid;
                $where['escid']=$escid;
                $where['subid']=$subid;
                $where['teach_uid']=$uid;
                $where['teach_pid']=$pid;
                $order=array('courseid');
                if ($where['escid']=='2ES') {
                    $dathora=$hora->_getHoraryxTeacherXPeriodXTodasEsc($where);
                }
                else{
                    $dathora=$hora->_getFilter($where,$attrib=null,$order);
                }
                // print_r($dathora);exit();
                $this->view->horarios=$dathora;

                $curso=new Api_Model_DbTable_Coursexteacher();
                $where1['eid']=$eid;
                $where1['oid']=$oid;
                $where1['escid']=$escid;
                $where1['perid']=$perid;
                $where1['subid']=$subid;
                $where1['pid']=$pid;
                if ($where1['escid']=="2ES") {
                    $datcur=$curso->_getOneCoursexTeacherXPeriodXTodasEsc($where1);
                }
                else{
                    $datcur=$curso->_getFilter($where1);
                }
                // print_r($datcur);exit();
                $this->view->cursos=$datcur;
                $this->view->perid=$perid;
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
            // $perid=$this->sesion->period->perid;
            $perid=$this->_getParam('perid');
            $faculty=$this->sesion->faculty->name;
            $this->view->faculty=$faculty;

            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $pid=$this->sesion->pid;

            $uid=$this->sesion->uid;
            $this->view->uid=$uid;
            $this->view->pid=$pid;

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
            $this->view->desc=$desc;
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
            $names=strtoupper($spe['esc']);
            $namep=strtoupper($spe['parent']);
            $namefinal=$names." <br> ".$namep;
            $namef = strtoupper($faculty);

            $wheres=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'uid'=>$uid,'subid'=>$subid,'pid'=>$pid);
            $user = new Api_Model_DbTable_Users();
            $duser = $user->_getInfoUser($wheres);
            $this->view->duser=$duser;

            $namelogo = (!empty($desc['header']))?$desc['header']:"blanco";

            $dbimpression = new Api_Model_DbTable_Countimpressionall();

            $uidim=$this->sesion->pid;
            $pid=$uidim;
            $uid=$this->sesion->uid;

            $wheri = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'horarydocente'.$semid,'perid'=>$perid);
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
                    'pid'=>$this->sesion->pid,
                    'type_impression'=>'horarydocente'.$semid,
                    'date_impression'=>date('Y-m-d H:i:s'),
                    'pid_print'=>$uidim,
                    'perid'=>$perid,
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
            $header = str_replace("h2", "h1", $header);
            $header = str_replace("h3", "h2", $header);
            $header = str_replace("h4", "h3", $header);
            $header = str_replace("11%", "9%", $header);

            $this->view->header=$header;
            $this->view->footer=$footer;
        } catch (Exception $ex) {
            print $ex->getMessage();
        }
    }


}
