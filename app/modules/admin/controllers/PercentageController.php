<?php 

class Admin_PercentageController extends Zend_Controller_Action
{
	public function init(){
		$this->eid='20154605046';
		$this->oid='1';
	}
	public function indexAction(){
		try {
			$fm= new Admin_Form_Speciabili();
			$this->view->fm=$fm;

			$data_ac = array(
			'eid' => $this->eid,
			'oid' => $this->oid,
			);
		$perid_activo=new Api_Model_DbTable_Periods();
        $pe_ac=$perid_activo->_getPeriodsCurrent($data_ac);
        $this->view->peract=$pe_ac;
		} catch (Exception $e) {
			print "Error: Openrecords".$e->getMessage();
		}
	}
	public function coursesAction(){	
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->eid;
			$oid=$this->oid;
			$perid = $this->_getParam('perid');
			$subid = $this->_getParam('subid');
			$escid = $this->_getParam('escid');
			$where=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'subid'=>$subid,'escid'=>$escid);
			$attrib=array('perid','semid','escid','subid','courseid','curid','turno','type_rate','closure_date','state','state_record');
			$orders=array('semid','courseid','turno');
			$dbcourses= new Api_Model_DbTable_PeriodsCourses();
			$datacourses = $dbcourses->_getFilter($where,$attrib,$orders);
			if ($datacourses) {
				$i=0;			
				foreach ($datacourses as $course) {
					$curid=$course['curid'];
					$courseid=$course['courseid'];
					$where=array('eid'=>$eid,'oid'=>$oid,'curid'=>$curid,'escid'=>$escid,'subid'=>$subid,'courseid'=>$courseid);
					$attrib=array('courseid','name');
					$dbcourse = new Api_Model_DbTable_Course();
					$datacourse[$i]= $dbcourse->_getFilter($where,$attrib);
					$i++;
				}
			}
			else{
				$datacourse=null;
			}
			$this->view->datacourses=$datacourses;
			$this->view->datacourse=$datacourse;
		} catch (Exception $e) {
			print "Error: get Courses".$e->getMessage();
		}
	}
	public function percentagecompetitionAction()
    {   
        $eid = $this->eid;
        $oid = $this->oid;

        $this->_helper->layout->disableLayout();
        $form = new Admin_Form_Percentage();

        $base_persentage = new Api_Model_DbTable_CourseCompetency();
        $request = $this->getRequest();

        $this->view->errorglobal = false;
        $this->view->ejcutarcerrar = false;

        
        $params = $this->getRequest()->getParams();
            if(count($params) > 3){
                $paramsdecode = array();
                foreach ( $params as $key => $value ){
                    if($key!="module" && $key!="controller" && $key!="action"){
                        $paramsdecode[base64_decode($key)] = base64_decode($value);
                    }
                }
                $params = $paramsdecode;
            }
            /***********paramets***********/
            $eid   = $this->eid;
            $oid   = $this->oid;
            $escid        = trim($params['escid']);
            $subid        = trim($params['subid']);                  
            $courseid    = trim($params['courseid']);
            $curid        = trim($params['curid']);
            $turno        = trim($params['turno']);
            $perid        = trim($params['perid']);
            $partial      = trim($params['partial']);
            $unid		  = trim($params['unid']);
            $this->view->unidad=$unid;


            $persentage_course = null;

            $this->view->partial=$partial;

            $where = array(
                'eid' =>$eid,'oid'=>$oid,
                'escid' =>$escid,'subid'=>$subid,
                'courseid' =>$courseid,'curid'=>$curid,
                'turno' =>$turno,'perid'=>$perid,
                );

            $base_persentage = new Api_Model_DbTable_CourseCompetency();
            $result = $base_persentage->_getOne($where);

            $base_syllabus = new Api_Model_DbTable_Syllabus();
            $units = $base_syllabus->_getOne($where);
            $this->view->units=$units['units'];
            //$this->view->state_syllabus=$units['state'];

           // $persetage = (isset($result) && count($result)>0)?$result:array();
            //$this->view->persetage=$persetage;          

            if ($unid == 1) {
                $porc1 = $result['porc1_u1'];
                $porc2 = $result['porc2_u1'];
                $porc3 = $result['porc3_u1'];
                $content=1;

            }
            elseif ($unid==2) {
                $porc1 = $result['porc1_u2'];
                $porc2 = $result['porc2_u2'];
                $porc3 = $result['porc3_u2'];
                $content=1;

            }elseif ($unid==3){
            	$porc1 = $result['porc1_u3'];
                $porc2 = $result['porc2_u3'];
                $porc3 = $result['porc3_u3'];
                $content=1;

            }elseif ($unid==4){
            	$porc1 = $result['porc1_u4'];
                $porc2 = $result['porc2_u4'];
                $porc3 = $result['porc3_u4'];
                $content=1;
            }  
            if ($porc1=='' || $porc2=='' || $porc3==''){
                $content=2;
                
            }  
            $this->view->cont=$content;            
            
            $form->Persentages();
            $form->txtppporcentaje1->setValue($porc1);
            $form->txtppporcentaje2->setValue($porc2);
            $form->txtppporcentaje3->setValue($porc3);
            
            $value_units = null;
            $sil_state = null;
            $value_units = $units['units'];
            $sil_state = $units['state'];

            $form->setInputHidden($curid,$escid,$courseid,$perid,$turno,$eid,$oid,$subid,$partial,$value_units,$sil_state);
            $form->addInputHidden();
        
        $this->view->form = $form;
    }

    public function modifypercentagecompetitionAction()
    {
        $this->_helper->layout->disableLayout();
        $formdata=$this->getRequest()->getPost();
        $where['eid']=$formdata['hdeid'];
        $where['oid']=$formdata['hdoid'];
        $where['escid']=$formdata['hdescid'];
        $where['subid']=$formdata['hdsubid'];
        $where['curid']=$formdata['hdcurid'];
        $where['courseid']=$formdata['hdcourseid'];
        $where['turno']=$formdata['hdturno'];
        $where['perid']=$formdata['hdperid'];
        $where['unid']=$formdata['hdunid'];
        $where['txtppporcentaje1']=$formdata['txtppporcentaje1'];
        $where['txtppporcentaje2']=$formdata['txtppporcentaje2'];
        $where['txtppporcentaje3']=$formdata['txtppporcentaje3'];
        if ($where['unid']==1){
            $data['porc1_u1']=$formdata['txtppporcentaje1'];
            $data['porc2_u1']=$formdata['txtppporcentaje2'];
            $data['porc3_u1']=$formdata['txtppporcentaje3'];
        }elseif($where['unid']==2){
            $data['porc1_u2']=$formdata['txtppporcentaje1'];
            $data['porc2_u2']=$formdata['txtppporcentaje2'];
            $data['porc3_u2']=$formdata['txtppporcentaje3'];
        }elseif($where['unid']==3){
            $data['porc1_u3']=$formdata['txtppporcentaje1'];
            $data['porc2_u3']=$formdata['txtppporcentaje2'];
            $data['porc3_u3']=$formdata['txtppporcentaje3'];
        }elseif($where['unid']==4){
            $data['porc1_u4']=$formdata['txtppporcentaje1'];
            $data['porc2_u4']=$formdata['txtppporcentaje2'];
            $data['porc3_u4']=$formdata['txtppporcentaje3'];
        }
        $base_percentage = new Api_Model_DbTable_CourseCompetency();
        if($base_percentage->_update($data,$where) && $base_percentage->_modifypercentage($where)){
            $this->view->men='Se Ha Modificado El Porcentaje';
            //$this->_redirect('/admin/percentage/courses/perid'."/".$where['perid']."/escid/".$where['escid']."/subid/".$where['subid']);
        }
    }
}