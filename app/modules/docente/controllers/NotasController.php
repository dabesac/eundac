<?php
class Docente_NotasController extends Zend_Controller_Action{

	public function init(){
		$sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		
 		$this->sesion = $login;

	}

	public function indexAction(){

		$perid_encode = base64_encode($this->sesion->period->perid);
		$perid = base64_decode($this->_getParam('perid',$perid_encode));

		//Bloquear Docentes

		$where['eid']=$this->sesion->eid;
		$where['oid']=$this->sesion->oid;
		$where['uid']=$this->sesion->uid;
		$where['pid']=$this->sesion->pid;
		$where['perid']=$perid;
		$where['is_main']='S';
		$this->view->uid=$where['uid'];

		$this->view->perid= $perid;
		$tb_periods_course = new Api_Model_DbTable_PeriodsCourses();
		$data_courses = $tb_periods_course->_getCourseTeacher($where);
		
        $tb_course= new Api_Model_DbTable_Course();
        $tb_specialyti = new Api_Model_DbTable_Speciality();

		$faculty=array();

		if ($data_courses) {
			foreach ($data_courses as $key => $tmp_course) {
				$where_1['eid']=$tmp_course['eid'];
		        $where_1['oid']=$tmp_course['oid'];
		        $where_1['curid']=$tmp_course['curid'];
		        $where_1['escid']=$tmp_course['escid'];
		        $where_1['subid']=$tmp_course['subid'];
		        $where_1['courseid']=$tmp_course['courseid']; 
	            $course_name=$tb_course->_getOne($where_1);
				$data_courses[$key]['name_course']	=	$course_name['name'];
				$name_speciality = $tb_specialyti->_getOne($where_1);
				$data_courses[$key]['specialyti'] = $name_speciality['name'];
			}

			$this->view->data_courses = $data_courses;


			$l=count($data_courses);
			$a=0;
			$faculty[$a]['facid']=$data_courses[0]['facid'];
			$faculty[$a]['name']=$data_courses[0]['name'];
			for ($i=0; $i < $l ; $i++) { 
				if (($faculty[$a]['facid'])!= $data_courses[$i]['facid']) {
					$a++;
					$faculty[$a]['facid']=$data_courses[$i]['facid']; 
					$faculty[$a]['name']=$data_courses[$i]['name']; 
				}
			}
		}
		$this->view->faculty=$faculty;

		$base_period = new Api_Model_DbTable_Periods();
	    $data_period = $base_period->_getOnePeriod($where);
	    $period_tm_act_ini = $base_period->_get_periods_ini_temp_activo($where);
	    

	    $li = '';
		 if ($period_tm_act_ini) {
		 	foreach ($period_tm_act_ini as $key => $value) {
                $li = "<li><a href=/docente/notas/index/perid/".base64_encode($value['perid']).">".$value['name']." | ".$value['perid']."</a></li> \n" . $li;
			}
		}
	    $this->view->periods = $li;

        $partial = 0;
	    if ($data_period) {
	        $time = time();
	        //primer partial
	        if($time >= strtotime($data_period['start_register_note_p'])  && $time <= strtotime($data_period['end_register_note_p'])){
                $this->view->partial = 1; 
                $partial = 1;
	        }
	            //segundo partial
            if($time >= strtotime($data_period['start_register_note_s'])  && $time <= strtotime($data_period['end_register_note_s'])){
                $this->view->partial = 2; 
                $partial = 2;
            }
	        
	    }
	    $persetage = $this->persetage_notes($data_courses,$partial);
	    // print_r($persetage);exit();

		$this->view->data=$persetage;
		
		


	}


	public function coursesAction(){
        $this->_helper->layout()->disablelayout();

        $params = array();
       	$params = $this->getRequest()->getParams();
       	$perid = trim($params['perid']);
       	$escid = trim($params['escid']);
       	$courseid = trim($params['courseid']);
       	$curid = trim($params['curid']);
       	$turno = trim($params['turno']);
       	$escidorigen = trim($params['escido']);
       	$turnoorigen = trim($params['turnoo']);

    	try {
    		$silabo = new Api_Model_DbTable_Syllabus();
    		$data= $silabo->_getDuplicasilabo($perid,$escid,$courseid,$curid,$turno,$escidorigen,$turnoorigen);
	    	if($data['duplicar_silabo5']==1)
	    	{
	    		$json = array('status' => true, );
	    	}else{
	    		$json =  array('status' => false, );
	    	}
    	} catch (Exception $e) {
    		$json = array('status' => false, );
    	}
       
    	$this->_response->setHeader('Content-Type', 'application/json');
        $this->view->json= Zend_Json::encode($json);  
	}

	public function persetage_notes($data=array(),$partial){
		
		if ($data) {
			foreach ($data as $key => $course) {
				$where = array(
                'eid' => $course['eid'],
                'oid' => $course['oid'],
                'escid' => $course['escid'],
                'subid' => $course['subid'],
                'courseid' => $course['courseid'],
                'curid' => $course['curid'],
                'turno' => $course['turno'],
                'perid' => $course['perid'],
            	);
            	$base_syllabus = new Api_Model_DbTable_Syllabus();
		        $closure_syllabus = $base_syllabus->_getOne($where);

        		$data[$key]['closure_syllabus'] = $closure_syllabus['state'];

        		$base_period_course = new Api_Model_DbTable_PeriodsCourses();
		        $state_record_c = $base_period_course ->_getOne($where);

		        $data[$key]['closure_record_n'] = $state_record_c['state'];

				$num_temp = 0;
				$persetage=0;
				$num =0;
				$data[$key]['persetage_notes'] = 0;

				if ($data[$key]['closure_syllabus']=='C' && $data[$key]['closure_record_n']=='P') {
						$data[$key]['persetage_notes'] = 50;
				}elseif ($data[$key]['closure_syllabus']=='C' && $data[$key]['closure_record_n']=='C') {
						$data[$key]['persetage_notes'] = 100;
					
				}
        		
			}


			foreach ($data as $key => $course_1) {
				$where = array(
                'eid' => $course_1['eid'],
                'oid' => $course_1['oid'],
                'escid' => $course_1['escid'],
                'subid' => $course_1['subid'],
                'coursoid' => $course_1['courseid'],
                'curid' => $course_1['curid'],
                'turno' => $course_1['turno'],
                'perid' => $course_1['perid'],
            	);

				
				$persetage_assit=0;
				$sum = 0;
				$num_reg = 0;
        		$base_assistance = new Api_Model_DbTable_StudentAssistance();
		        $assistence = $base_assistance ->_getAll($where);
		        $data[$key]['persetage_assit'] = 0;
		        $count = count($assistence);
		        
		        $assist_1 = 0; $assist_2 = 0; $assist_3 = 0;$assist_4 = 0;$assist_5 = 0;
	            $assist_6 = 0; $assist_7 = 0; $assist_8 = 0;$assist_9 = 0;$assist_10 = 0;
	            $assist_11 = 0; $assist_12 = 0; $assist_13 = 0;$assist_14 = 0;$assist_15 = 0;
	            $assist_16 = 0; $assist_17 = 0; $assist_18 = 0;$assist_19 = 0;$assist_20 = 0;
	            $assist_21 = 0; $assist_22 = 0; $assist_23 = 0;$assist_24 = 0;$assist_25 = 0;
	            $assist_26 = 0; $assist_27 = 0; $assist_28 = 0;$assist_29 = 0;$assist_30 = 0;
	            $assist_31 = 0; $assist_32 = 0; ;
	           	
	           	
           		for ($i=1; $i <= 32 ; $i++) { 
    				$num_reg = $num_reg + $count;
    			}


    			foreach ($assistence as $key1 => $infoassist) {

	        			if ($infoassist['a_sesion_1']=='R' || $infoassist['a_sesion_1']=='A' || $infoassist['a_sesion_1']=='F' || $infoassist['a_sesion_1']=='T') {
                        $assist_1++;
	                    }
	                     if ($infoassist['a_sesion_2']=='R' || $infoassist['a_sesion_2']=='A' || $infoassist['a_sesion_2']=='F' || $infoassist['a_sesion_2']=='T') {
	                        $assist_2++;
	                    }
	                     if ($infoassist['a_sesion_3']=='R' || $infoassist['a_sesion_3']=='A' || $infoassist['a_sesion_3']=='F' || $infoassist['a_sesion_3']=='T') {
	                        $assist_3++;
	                    }
	                     if ($infoassist['a_sesion_4']=='R' || $infoassist['a_sesion_4']=='A' || $infoassist['a_sesion_4']=='F' || $infoassist['a_sesion_4']=='T') {
	                        $assist_4++;
	                    } if ($infoassist['a_sesion_5']=='R' || $infoassist['a_sesion_5']=='A' || $infoassist['a_sesion_5']=='F' || $infoassist['a_sesion_5']=='T') {
	                        $assist_5++;
	                    } if ($infoassist['a_sesion_6']=='R' || $infoassist['a_sesion_6']=='A' || $infoassist['a_sesion_6']=='F' || $infoassist['a_sesion_6']=='T') {
	                        $assist_6++;
	                    } if ($infoassist['a_sesion_7']=='R' || $infoassist['a_sesion_7']=='A' || $infoassist['a_sesion_7']=='F' || $infoassist['a_sesion_7']=='T') {
	                        $assist_7++;
	                    } if ($infoassist['a_sesion_8']=='R' || $infoassist['a_sesion_8']=='A' || $infoassist['a_sesion_8']=='F' || $infoassist['a_sesion_8']=='T') {
	                        $assist_8++;
	                    } if ($infoassist['a_sesion_9']=='R' || $infoassist['a_sesion_9']=='A' || $infoassist['a_sesion_9']=='F' || $infoassist['a_sesion_9']=='T') {
	                        $assist_9++;
	                    } if ($infoassist['a_sesion_10']=='R' || $infoassist['a_sesion_10']=='A' || $infoassist['a_sesion_10']=='F' || $infoassist['a_sesion_10']=='T') {
	                        $assist_10++;
	                    } if ($infoassist['a_sesion_11']=='R' || $infoassist['a_sesion_11']=='A' || $infoassist['a_sesion_11']=='F' || $infoassist['a_sesion_11']=='T') {
	                        $assist_11++;
	                    } if ($infoassist['a_sesion_12']=='R' || $infoassist['a_sesion_12']=='A' || $infoassist['a_sesion_12']=='F' || $infoassist['a_sesion_12']=='T') {
	                        $assist_12++;
	                    }if ($infoassist['a_sesion_13']=='R' || $infoassist['a_sesion_13']=='A' || $infoassist['a_sesion_13']=='F' || $infoassist['a_sesion_13']=='T') {
	                        $assist_13++;
	                    }if ($infoassist['a_sesion_14']=='R' || $infoassist['a_sesion_14']=='A' || $infoassist['a_sesion_14']=='F' || $infoassist['a_sesion_14']=='T') {
	                        $assist_14++;
	                    }if ($infoassist['a_sesion_15']=='R' || $infoassist['a_sesion_15']=='A' || $infoassist['a_sesion_15']=='F' || $infoassist['a_sesion_15']=='T') {
	                        $assist_15++;
	                    }if ($infoassist['a_sesion_16']=='R' || $infoassist['a_sesion_16']=='A' || $infoassist['a_sesion_16']=='F' || $infoassist['a_sesion_16']=='T') {
	                        $assist_16++;
	                    }if ($infoassist['a_sesion_17']=='R' || $infoassist['a_sesion_17']=='A' || $infoassist['a_sesion_17']=='F' || $infoassist['a_sesion_17']=='T') {
	                        $assist_17++;
	                    }

	                    if ($infoassist['a_sesion_18']=='R' || $infoassist['a_sesion_18']=='A' || $infoassist['a_sesion_18']=='F' || $infoassist['a_sesion_18']=='T') {
	                        $assist_18++;
	                    }
	                     if ($infoassist['a_sesion_19']=='R' || $infoassist['a_sesion_19']=='A' || $infoassist['a_sesion_19']=='F' || $infoassist['a_sesion_19']=='T') {
	                        $assist_19++;
	                    }
	                     if ($infoassist['a_sesion_20']=='R' || $infoassist['a_sesion_20']=='A' || $infoassist['a_sesion_20']=='F' || $infoassist['a_sesion_20']=='T') {
	                        $assist_20++;
	                    }
	                     if ($infoassist['a_sesion_21']=='R' || $infoassist['a_sesion_21']=='A' || $infoassist['a_sesion_21']=='F' || $infoassist['a_sesion_21']=='T') {
	                        $assist_21++;
	                    } if ($infoassist['a_sesion_22']=='R' || $infoassist['a_sesion_22']=='A' || $infoassist['a_sesion_22']=='F' || $infoassist['a_sesion_22']=='T') {
	                        $assist_22++;
	                    } if ($infoassist['a_sesion_23']=='R' || $infoassist['a_sesion_23']=='A' || $infoassist['a_sesion_23']=='F' || $infoassist['a_sesion_23']=='T') {
	                        $assist_23++;
	                    } if ($infoassist['a_sesion_24']=='R' || $infoassist['a_sesion_24']=='A' || $infoassist['a_sesion_24']=='F' || $infoassist['a_sesion_24']=='T') {
	                        $assist_24++;
	                    } if ($infoassist['a_sesion_25']=='R' || $infoassist['a_sesion_25']=='A' || $infoassist['a_sesion_25']=='F' || $infoassist['a_sesion_25']=='T') {
	                        $assist_25++;
	                    } if ($infoassist['a_sesion_26']=='R' || $infoassist['a_sesion_26']=='A' || $infoassist['a_sesion_26']=='F' || $infoassist['a_sesion_26']=='T') {
	                        $assist_26++;
	                    } if ($infoassist['a_sesion_27']=='R' || $infoassist['a_sesion_27']=='A' || $infoassist['a_sesion_27']=='F' || $infoassist['a_sesion_27']=='T') {
	                        $assist_27++;
	                    } if ($infoassist['a_sesion_28']=='R' || $infoassist['a_sesion_28']=='A' || $infoassist['a_sesion_28']=='F' || $infoassist['a_sesion_28']=='T') {
	                        $assist_28++;
	                    } if ($infoassist['a_sesion_29']=='R' || $infoassist['a_sesion_29']=='A' || $infoassist['a_sesion_29']=='F' || $infoassist['a_sesion_29']=='T') {
	                        $assist_29++;
	                    }if ($infoassist['a_sesion_30']=='R' || $infoassist['a_sesion_30']=='A' || $infoassist['a_sesion_30']=='F' || $infoassist['a_sesion_30']=='T') {
	                        $assist_30++;
	                    }if ($infoassist['a_sesion_31']=='R' || $infoassist['a_sesion_31']=='A' || $infoassist['a_sesion_31']=='F' || $infoassist['a_sesion_31']=='T') {
	                        $assist_31++;
	                    }if ($infoassist['a_sesion_32']=='R' || $infoassist['a_sesion_32']=='A' || $infoassist['a_sesion_32']=='F' || $infoassist['a_sesion_32']=='T') {
	                        $assist_32++;
	                    }

	            
                    	$sum = $assist_1 + $assist_2 + $assist_3 + $assist_4 +$assist_5+$assist_6 +$assist_7+
                    	   $assist_8 +$assist_9 + $assist_10 + $assist_11 + $assist_12 +$assist_13 + $assist_14+
                    	   $assist_15 +$assist_16 +$assist_17+$assist_18+$assist_19+$assist_20+$assist_21+$assist_22+
                    	   $assist_23+$assist_24+$assist_25+$assist_26+$assist_27+$assist_28+$assist_29+$assist_30+
                    	   $assist_31+$assist_32;
                    	
                   		$persetage_assit = ($num_reg > 0)? ((100*$sum)/$num_reg):0;
                  	
        		}
                    	//echo $sum; exit();

	    		$data[$key]['persetage_assit']=$persetage_assit;
			}


		}

		return $data;

    }





}
