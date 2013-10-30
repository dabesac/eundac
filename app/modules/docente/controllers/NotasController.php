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
		// Periods Now
		$where['eid']=$this->sesion->eid;
		$where['oid']=$this->sesion->oid;
		$where['uid']=$this->sesion->uid;
		$where['pid']=$this->sesion->pid;
		$where['perid']=$this->sesion->period->perid;
		$where['is_main']='S';

		//print_r($this->sesion);
		$this->view->uid=$where['uid'];

		$this->view->perid= $this->sesion->period->perid;
		$docente = new Api_Model_DbTable_PeriodsCourses();
		$data = $docente->_getCourseTeacher($where);
		
		$l=count($data);
		$faculty=array();

		$a=0;
		$faculty[$a]['facid']=$data[0]['facid'];
		$faculty[$a]['name']=$data[0]['name'];
		for ($i=0; $i < $l ; $i++) { 
			if (($faculty[$a]['facid'])!= $data[$i]['facid']) {
				$a++;
				$faculty[$a]['facid']=$data[$i]['facid']; 
				$faculty[$a]['name']=$data[$i]['name']; 
			}
		}
		$this->view->faculty=$faculty;

		$base_period = new Api_Model_DbTable_Periods();
	    $data_period = $base_period->_getOne($where);
	    
	    if ($data_period) {
	        $time = time();
	        //primer partial
	        if($time >= strtotime($data_period['start_register_note_p'])  && $time <= strtotime($data_period['end_register_note_p'])){
                $this->view->partial = 1; 
                $partial = 1;
	        }else{
	            //segundo partial
	            if($time >= strtotime($data_period['start_register_note_s'])  && $time <= strtotime($data_period['end_register_note_s'])){
	                $this->view->partial = 2; 
	                $partial = 2;
	            }
	        }
	    }

	    $persetage = $this->persetage_notes($data,$partial);

		$this->view->data=$persetage;
		
		// Periods Later
		$where['uid']=$this->sesion->uid;
		
		$where['uid']=$this->sesion->uid;
		$where['perid']="13D";
		
		$where['is_main']='S';
		$docente_ = new Api_Model_DbTable_PeriodsCourses();
		$data_ = $docente_->_getCourseTeacher($where);
		 $l_=count($data_);
		$faculty_=array();
		
		$a_=0;
		$faculty_[$a_]['facid']=$data_[0]['facid'];
		$faculty_[$a_]['name']=$data_[0]['name'];
		for ($i_=0; $i_ < $l_ ; $i_++) {
			if (($faculty_[$a_]['facid'])!= $data_[$i_]['facid']) {
				$a_++;
				$faculty_[$a_]['facid']=$data_[$i_]['facid'];
				$faculty_[$a_]['name']=$data_[$i_]['name'];
			}
		}
		$this->view->faculty_=$faculty_;
		$this->view->data_=$data_;
	
	}


	public function coursesAction(){
        $this->_helper->layout()->disablelayout();

        $perid = trim($this->_getParam("perid"));

        $escid = trim($this->_getParam("escid"));
        $curid = trim($this->_getParam("curid"));
        $turno = trim($this->_getParam("turno"));
        $courseid =trim($this->_getParam("courseid"));

        // $escid = $this->_getParam("escid");
        // $turno = $this->_getParam("turno");
        
        $courseidorigen = trim($this->_getParam("courseidorigen"));
        $turnoorigen = trim($this->_getParam("turnoorigen"));
        $escidorigen = trim($this->_getParam("escidorigen"));
      

       	
        if($courseid==$courseidorigen)
        {

        $silabo = new Api_Model_DbTable_Syllabus();
        	if($silabo->_getDuplicasilabo($perid,$escid,$courseid,$curid,$turno,$escidorigen,$turnoorigen))
        	{
        	?>
        	<script>
        	alert('El silabo ha sido duplicado correctamente')
        	</script>
        	<?php
        	}

        }
        else
        {
        	echo "no son cursos compatibles";
        }



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
				if ($course['type_rate']=="C") {
					$base_registration_course = new Api_Model_DbTable_Registrationxcourse();
        			$result_conpetency = $base_registration_course->_closureconpetency($where);
			        $notes_conpetency = (isset($result_conpetency) && count($result_conpetency)>0)?$result_conpetency[0]:array();

			        	if($partial==1){
			        		if (
			                    (!empty($notes_conpetency['num_reg'])) 

			        			) {
			        			$num_reg = $notes_conpetency['num_reg'];
			        			for ($i=1; $i <= 6 ; $i++) { 
			        				$num = $num + $num_reg;
			        			}

			        			$num_temp = 
		        							intval($notes_conpetency['nota1_i'])+
		        							intval($notes_conpetency['nota2_i'])+
		        							intval($notes_conpetency['nota3_i'])+
		        							intval($notes_conpetency['nota6_i'])+
		        							intval($notes_conpetency['nota7_i'])+
		        							intval($notes_conpetency['nota8_i']);
			        			$persetage = ($num_temp != 0)? (((0.5*$num_temp)/$num)*100) : 0;
			        		}

			        	}
			        	if($partial==2){
			        		if (
			                    (!empty($notes_conpetency['num_reg'])) 

			        			) {
			        			$num_reg = $notes_conpetency['num_reg'];
			        			for ($i=1; $i <= 12 ; $i++) { 
			        				$num = $num + $num_reg;
			        			}

			        			$num_temp = 
		        							intval($notes_conpetency['nota1_i'])+
		        							intval($notes_conpetency['nota2_i'])+
		        							intval($notes_conpetency['nota3_i'])+
		        							intval($notes_conpetency['nota6_i'])+
		        							intval($notes_conpetency['nota7_i'])+
		        							intval($notes_conpetency['nota8_i']);
			        			$persetage = ($num_temp != 0)? (((100 *$num_temp)/$num)) : 0;
			        		}

			        	}
    			}

    			if ($course['type_rate']=="O") {
					$base_registration_course = new Api_Model_DbTable_Registrationxcourse();
			        $result_target = $base_registration_course->_closuretarget($where);
			        $result_target = (isset($result_target) && count($result_target)>0)?$result_target[0]:array();

			        	if($partial==1){
			        		if (
			                    (!empty($result_target['num_reg'])) 

			        			) {
			        			$num_reg = $result_target['num_reg'];
			        			for ($i=1; $i <= 9 ; $i++) { 
			        				if ($result_target['nota'.$i.'_i']>0) {
			        					$num = $num +$num_reg;
			        				}
			        				$num_temp =  $num_temp + intval($result_target['nota'.$i.'_i']);
			        			}

			        			$persetage = ($num_temp != 0)? (((0.5 *$num_temp)/$num)*100) : 0;

			        		}

			        	}

			        	if($partial==2){
			        		if (
			                    (!empty($result_target['num_reg'])) 

			        			) {
			        			$num_reg = $result_target['num_reg'];
			        			for ($i=1; $i <= 9 ; $i++) { 
			        				if ($result_target['nota'.$i.'_i']>0 || $result_target['nota'.$i.'_ii']) {
			        					$num = $num +$num_reg;
			        				}
			        				$num_temp =  $num_temp + intval($result_target['nota'.$i.'_i'])+
			        										intval($result_target['nota'.$i.'_ii']);
			        			}

			        			$persetage = ($num_temp != 0)? (((100 *$num_temp)/$num)) : 0;
			        		}

			        	}
    			}


        		$data[$key]['persetage_notes']=$persetage;
			}


			$num_temp = array();
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
	            $assist_25 = 0; $assist_27 = 0; $assist_28 = 0;$assist_29 = 0;$assist_30 = 0;
	            $assist_31 = 0; $assist_32 = 0; $assist_33 = 0;$assist_34 = 0;
	           	
	           	if ($partial == 1) {
	           		for ($i=1; $i <=17 ; $i++) { 
	    				$num_reg = $num_reg + $count;
	    			}
	           	}

	           	if ($partial == 2) {
	           		for ($i=1; $i <=34 ; $i++) { 
	    				$num_reg = $num_reg + $count;
	    			}
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

	                    if ($partial == 2) {

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
		                    }if ($infoassist['a_sesion_33']=='R' || $infoassist['a_sesion_33']=='A' || $infoassist['a_sesion_33']=='F' || $infoassist['a_sesion_33']=='T') {
		                        $assist_33++;
		                    }if ($infoassist['a_sesion_34']=='R' || $infoassist['a_sesion_34']=='A' || $infoassist['a_sesion_34']=='F' || $infoassist['a_sesion_34']=='T') {
		                        $assist_34++;
		                    }
		                    
		                }

	                    if ($partial == 1) {
	                    	$sum = $assist_1 + $assist_2 + $assist_3 + $assist_4 +$assist_5+$assist_6 +$assist_7+
	                    	   $assist_8 +$assist_9 + $assist_10 + $assist_11 + $assist_12 +$assist_13 + $assist_14+
	                    	   $assist_15 +$assist_16 +$assist_17;

	                   		$persetage_assit = ($num_reg > 0)? ((0.5*$sum)/$num_reg)*100:0;
	                    }
	                    if ($partial == 2) {
	                    	$sum = $assist_1 + $assist_2 + $assist_3 + $assist_4 +$assist_5+$assist_6 +$assist_7+
	                    	   $assist_8 +$assist_9 + $assist_10 + $assist_11 + $assist_12 +$assist_13 + $assist_14+
	                    	   $assist_15 +$assist_16 +$assist_17+$assist_18+$assist_19+$assist_20+$assist_21+$assist_22+
	                    	   $assist_23+$assist_24+$assist_25+$assist_26+$assist_27+$assist_28+$assist_29+$assist_30+
	                    	   $assist_31+$assist_32+$assist_33+$assist_34;
	                   		$persetage_assit = ($num_reg > 0)? (100*$sum)/$num_reg:0;
	                    }
                  	
        		}

	    		$data[$key]['persetage_assit']=$persetage_assit;
			}


		}

		// print_r($data);
		return $data;

    }





}