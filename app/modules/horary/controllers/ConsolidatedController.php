<?php
 class Horary_ConsolidatedController extends Zend_Controller_Action{

 	public function init(){
 		$sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
 		require_once 'Zend/Loader.php';
        Zend_Loader::loadClass('Zend_Rest_Client');
 	}

 	public function indexAction(){
        $this->_helper->layout()->disableLayout();

        $eid=$this->sesion->eid;
        $oid=$this->sesion->oid;
        $escid=$this->sesion->escid;
        $uid=$this->sesion->uid;
        $pid=$this->sesion->pid;
        $subid=$this->sesion->subid;
        $perid=$this->sesion->period->perid;

        $wheres=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
        $bd_hours= new Api_Model_DbTable_HoursBeginClasses();
        $datahours=$bd_hours->_getFilter($wheres);

        if ($datahours) {
            $valhoras[0]=$datahours[0]['hours_begin'];
            $hora=new Api_Model_DbTable_Horary();
            for ($k=0; $k < 20; $k++) {
                $dho=$hora->_getsumminutes($valhoras[$k],'50');
                $valhoras[$k+1]=$dho[0]['hora'];
            }
            $this->view->valhoras=$valhoras;

 		    try {
                $module = "horary_student";

                $data = array(
                        'escid' => base64_encode($escid),
                        'eid' =>base64_encode($eid),
                        'oid' =>base64_encode($oid),
                        'perid'=>base64_encode($perid),
                        'subid'=>base64_encode($subid),
                        'uid'=>base64_encode($uid)
                    );
                $server = new Eundac_Connect_Api($module,$data);
                $data = $server->connectAuth();
                $this->view->horarys=$data;

            } catch (Exception $e) {
                // print "Error: get Horary".$e->getMessage();
                $this->view->error="A";
            }
        }
 	}

 	public function printconsolidatedAction(){
 		try {
            $this->_helper->layout()->disableLayout();
 			$eid=$this->sesion->eid;
	        $oid=$this->sesion->oid;
	        $escid=$this->sesion->escid;
	        $faculty=$this->sesion->faculty->name;

	        $uid=$this->sesion->uid;
	        $pid=$this->sesion->pid;
	        $subid=$this->sesion->subid;
	        $perid=$this->sesion->period->perid;
	        $this->view->uid=$uid;
            $this->view->pid=$pid;

            $wheres=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
            $bd_hours= new Api_Model_DbTable_HoursBeginClasses();
            $datahours=$bd_hours->_getFilter($wheres);
            if ($datahours) {
                $valhoras[0]=$datahours[0]['hours_begin'];
                $hora=new Api_Model_DbTable_Horary();
                for ($k=0; $k < 20; $k++) {
                    $dho=$hora->_getsumminutes($valhoras[$k],'50');
                    $valhoras[$k+1]=$dho[0]['hora'];
                }
                $this->view->valhoras=$valhoras;

                $module = "horary_student";

                $data = array(
                        'escid' => base64_encode($escid),
                        'eid' =>base64_encode($eid),
                        'oid' =>base64_encode($oid),
                        'perid'=>base64_encode($perid),
                        'subid'=>base64_encode($subid),
                        'uid'=>base64_encode($uid)
                    );

                $server = new Eundac_Connect_Api($module,$data);
                $data = $server->connectAuth();

                $this->view->horarys=$data;
                // print_r($data);
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
                }
                else{
                    $spe['esc']=$desc['name'];
                    $spe['parent']='';
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

                $wheri = array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'consolidadohorary','perid'=>$perid);
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
                        'type_impression'=>'consolidadohorary',
                        'date_impression'=>date('Y-m-d H:i:s'),
                        'pid_print'=>$uidim,
                        'perid'=>$perid,
                        'count_impression'=>1                        
                        );
                    $dbimpression->_save($data);
                    $co=1;
                }

                $codigo=$co." - ".$uidim;

                $h1="h1";
                $h2="h2";
                $h3="h3";

                $header=$this->sesion->org['header_print'];
                $footer=$this->sesion->org['footer_print'];
                $header = str_replace("?facultad",$namef,$header);
                $header = str_replace("?escuela",$namefinal,$header);
                $header = str_replace("?logo", $namelogo, $header);
                $header = str_replace("?codigo", $codigo, $header);
                $header = str_replace("h2", $h1, $header);
                $header = str_replace("h3", $h2, $header);
                $header = str_replace("h4", $h3, $header);
                $header = str_replace("11%", "9%", $header);

                $this->view->header=$header;
                $this->view->footer=$footer;
			}
        } catch (Exception $e) {
            print "Error: print Horary".$e->getMessage();
        }
    }
 }
