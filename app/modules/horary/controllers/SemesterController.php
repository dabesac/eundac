<?php
class Horary_SemesterController extends Zend_Controller_Action{

	public function init(){
		$sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		$this->sesion = $login;

	}

	public function indexAction(){
		try {
			$fm=new Horary_Form_HoraryPeriods();
			$this->view->fm=$fm;
			$anio=date('Y');
            $this->view->anio=$anio;
		} catch (Exception $e) {
			print "Error: get semester".$e->getMessage();
		}
	}

	public function periodlistAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$perid=$this->sesion->period->perid;
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$anio=$this->_getParam('anio');
			$anio=substr($anio,2,2);
			$periodsDb = new Api_Model_DbTable_Periods();
	        $where = array('eid'=> $eid,'oid'=> $oid,'year'=> $anio);
	        $period = $periodsDb->_getPeriodsxYears($where);
	        $this->view->period=$period;
			$this->view->perid=$perid;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function semesterlistAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$escid=$this->sesion->escid;
			$perid=$this->_getParam('anio');
			$perid=$this->_getParam('perid');
			$this->view->perid=$perid;
			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'perid'=>$perid);
			$sem= new Api_Model_DbTable_Semester();
			$dsem=$sem->_getSemesterXPeriodsXEscid($where);
			$i=0;
			if ($dsem) {
				foreach ($dsem as $semes) {
					$where['semid']=$semes['semid'];
					$turnosxsem=$sem->_getSemesterXPeriodsXEscidXTurno($where);
					$dsem[$i]['turnos']=$turnosxsem;
					$i++;
				}
			}
			// $len=count($turnosxsem);
			$this->view->semester=$dsem;

			// $eid=$this->sesion->eid;
			// $oid=$this->sesion->oid;
			// $anio=substr($anio,2,2);
			// $periodsDb = new Api_Model_DbTable_Periods();
	  		// $where = array('eid'=> $eid,'oid'=> $oid,'year'=> $anio);
	  		// $period = $periodsDb->_getPeriodsxYears($where);
	  		// $this->view->period=$period;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function horarysemesterAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$this->view->eid=$eid;
			$oid=$this->sesion->oid;
			$this->view->oid=$oid;
			$escid=$this->sesion->escid;
			$semid=$this->_getParam('semid');
			$perid=$this->_getParam('perid');
			$turno=$this->_getParam('turno');
			$subid=$this->sesion->subid;
			// $perid=$this->sesion->period->perid;
			$this->view->semid=$semid;
			$this->view->perid=$perid;

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

	                for ($g=0; $g < $len; $g++) {
	                	$valhorasm[$g]=(isset($valhorasm[$g]))?$valhorasm[$g]:null;
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

		        $module = 'horary_course';
			    $params = array(
			        				'escid' => base64_encode($escid),
			        				'eid' => base64_encode($eid),
			        				'oid' =>base64_encode($oid),
			        				'perid'=>base64_encode($perid),
			        				'subid'=>base64_encode($subid),
			        				'semid'=>base64_encode($semid)
			        				);

    			$prueba = new Eundac_Connect_Api($module,$params);
    			$data= $prueba->connectAuth();
    			if ($turno) {
    				$i=0;
    				if ($data) {
	    				foreach ($data as $turnos) {
	    					$turnoss=$turnos['turno'];
	    					if ($turno==$turnoss) {
	    						$datatur[$i]=$turnos;
	    						$i++;
	    					}
	    				}
    				}
    				else{
    					$datatur=null;
    				}
    				$this->view->turno=$turno;
	        		$this->view->horarys=$datatur;
    			}
    			else{
	        		$this->view->horarys=$data;
    			}

	        	$where=array('eid'=>$eid,'oid'=>$oid,'subid'=>$subid,'perid'=>$perid,'escid'=>$escid,'semid'=>$semid);
	        	$cper= new Api_Model_DbTable_PeriodsCourses();
	        	$dcur=$cper->_getCoursesxPeriodxspecialityxsemester($where);

	        	$len=count($dcur);
	        	for ($i=0; $i < $len; $i++) {
	        		$escid=$dcur[$i]['escid'];
	        		$curid=$dcur[$i]['curid'];
	        		$semid=$dcur[$i]['semid'];
	        		$courseid=$dcur[$i]['courseid'];
	        		$whe=array('eid'=>$eid,'oid'=>$oid,'curid'=>$curid,'escid'=>$escid,'subid'=>$subid,'courseid'=>$courseid);
	        		$attrib=array('courseid','semid','credits');
	        		$bdcourse = new Api_Model_DbTable_Course();
	        		$datacourse[$i]= $bdcourse->_getFilter($whe,$attrib);
	        		$uid=$dcur[$i]['uid'];
	        		$where=array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid);
	        		$user= new Api_Model_DbTable_Users();
	        		$duser=$user->_getUserXUid($where);
	        		$dcur[$i]['namet']= $duser[0]['last_name0']." ".$duser[0]['last_name1'].", ".$duser[0]['first_name'];
	        	}
	        	$this->view->datacourse=$datacourse;
	        	$this->view->dcurso=$dcur;
		    }


		} catch (Exception $e) {
			print "Error: get horary semester".$e->getMessage();
		}

	}

	public function printhorarysemesterAction(){
		$this->_helper->layout()->disableLayout();
		$eid=$this->sesion->eid;
		$oid=$this->sesion->oid;
		$escid=$this->sesion->escid;
		$faculty=$this->sesion->faculty->name;
		$semid=base64_decode($this->_getParam('semid'));
		$perid=base64_decode($this->_getParam('perid'));
		$turno=base64_decode($this->_getParam('turno'));
		$subid=$this->sesion->subid;
		// $perid=$this->sesion->period->perid;
		$this->view->semid=$semid;
		$this->view->perid=$perid;

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

                for ($g=0; $g < $len; $g++) {
                    $valhorasm[$g]=(isset($valhorasm[$g]))?$valhorasm[$g]:null;
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

	        $module = 'horary_course';
		    $params = array(
		        				'escid' => base64_encode($escid),
		        				'eid' => base64_encode($eid),
		        				'oid' =>base64_encode($oid),
		        				'perid'=>base64_encode($perid),
		        				'subid'=>base64_encode($subid),
		        				'semid'=>base64_encode($semid)
		        				);

		    $prueba = new Eundac_Connect_Api($module,$params);
    		$data= $prueba->connectAuth();
		    //$data = $server->connectAuth();
		    if ($turno) {
				$i=0;
				foreach ($data as $turnos) {
					$turnoss=$turnos['turno'];
					if ($turno==$turnoss) {
						$datatur[$i]=$turnos;
						$i++;
					}
				}
				$this->view->turno=$turno;
        		$this->view->horarys=$datatur;
			}
			else{
        		$this->view->horarys=$data;
			}
	        // $this->view->horarys=$data;
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

	        $where=array('eid'=>$eid,'oid'=>$oid,'subid'=>$subid,'perid'=>$perid,'escid'=>$escid,'semid'=>$semid);
	        $cper= new Api_Model_DbTable_PeriodsCourses();
	        if ($turno) {
    			$where['turno']=$turno;
    			$dcur=$cper->_getCoursesxPeriodxspecialityxsemesterxturno($where);
    			$this->view->turno=$turno;
    			$nameimpre='horarysemester'.$semid.$turno;
    		}
    		else{
	        	$dcur=$cper->_getCoursesxPeriodxspecialityxsemester($where);
	        	$nameimpre='horarysemester'.$semid;
    		}

	        $len=count($dcur);
	        	for ($i=0; $i < $len; $i++) {
	        		$uid=$dcur[$i]['uid'];
	        		$where=array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid);
	        		$user= new Api_Model_DbTable_Users();
	        		$duser=$user->_getUserXUid($where);
	        		$dcur[$i]['namet']= $duser[0]['last_name0']." ".$duser[0]['last_name1'].", ".$duser[0]['first_name'];
	        	}
	        $this->view->dcurso=$dcur;

        	$namelogo = (!empty($desc['header']))?$desc['header']:"blanco";

            $dbimpression = new Api_Model_DbTable_Countimpressionall();

            $uidim=$this->sesion->pid;
            $pid=$uidim;
            $uid=$this->sesion->uid;

            $data = array(
                'eid'=>$eid,
                'oid'=>$oid,
                'uid'=>$uid,
                'escid'=>$escid,
                'subid'=>$subid,
                'pid'=>$this->sesion->pid,
                'type_impression'=>$nameimpre,
                'date_impression'=>date('Y-m-d H:i:s'),
                'pid_print'=>$uidim
                );
            $dbimpression->_save($data);

            $wheri = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>$nameimpre);
            $dataim = $dbimpression->_getFilter($wheri);

            $co=count($dataim);
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
		}

	}
}