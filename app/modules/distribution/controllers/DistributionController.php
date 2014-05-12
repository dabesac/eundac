<?php
class Distribution_DistributionController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (($login->infouser['teacher']['is_director']<>"S")){
    		$this->_helper->redirector('index','error','default');
    	}
    	$this->sesion = $login;
    }
    
    public function indexAction()
    {   
        try {
            $periodActive = $this->sesion->period->perid;
            $yearActive = substr($periodActive, 0, 2);
            $yearActive = '20'.$yearActive;
            $this->view->yearActive = $yearActive;

            $anio = base64_decode($this->_getParam('anio'));
            $this->view->anio = $anio;
        } catch (Exception $e) {
            print 'Error Controlador Index'.$e->getMessage();
        }
    }
    
    public function showdistributionAction(){
        try {
            $this->_helper->layout()->disableLayout();

       		$distribution = new Distribution_Model_DbTable_Distribution();
       		$data['eid']=$this->sesion->eid;
       		$data['oid']=$this->sesion->oid;
       		$data['escid']=$this->sesion->escid;
       		$data['subid']=$this->sesion->subid;

            $anio = $this->getParam('anio');
            $this->view->anio = $anio;
            $anio = substr($anio, 2, 2);
            $data['year'] = $anio;
       		//$campos = array("eid","oid","escid","subid","perid","dateaccept","number","state","distid");

            $c = 0;

       		$rows_distribution =$distribution->_getDistributionsxYear($data);
    
            $bdhorary = new Api_Model_DbTable_HoursBeginClasses();
            $ldistribution=new Distribution_Model_DbTable_logObsrvationDistribution();
            $i=0;
            foreach ($rows_distribution as $periodos) {
                $distid=$periodos['distid'];
                $perid=$periodos['perid'];
                $escid=$periodos['escid'];
                $subid=$periodos['subid'];
                $wheres=array('eid'=>$data['eid'],'oid'=>$data['oid'],'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
                //$wheresp=array('eid'=>$data['eid'],'oid'=>$data['oid'],'distid'=>$distid,'escid'=>$escid,'subid'=>$subid,'perid'=>$perid);
                //$dataobs=$ldistribution->_getUltimateObservation($wheresp);
                //if ($dataobs) {
                    //$rows_distribution[$i]['observation']=$dataobs[0]['observation'];
                    //$rows_distribution[$i]['state']=$dataobs[0]['state'];
                //}
                
                $datahours=$bdhorary->_getFilter($wheres);
                $rows_distribution[$i]['hours']=$datahours; 
                $i++;
            }
            // print_r($rows_distribution);

            if ($rows_distribution) $this->view->ldistribution=$rows_distribution ;
        } catch (Exception $e) {
            print 'Error'.$e->getMessage();
        }
    }


    public function newAction(){    
        $anio = $this->_getParam('anio');
        $this->view->anio = $anio;        
        $form = new Distribution_Form_Distribution();
        $this->view->form = $form;

    }

    public function sendistributionAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $state=base64_decode($this->_getParam("state"));
            $escid=base64_decode($this->_getParam("escid"));
            $subid=base64_decode($this->_getParam("subid"));
            $distid=base64_decode($this->_getParam("distid"));
            $perid=base64_decode($this->_getParam("perid"));
            $comment=$this->_getParam("comment");
            $dbdistribution=new Distribution_Model_DbTable_Distribution();
            $ldistribution=new Distribution_Model_DbTable_logObsrvationDistribution();
            $pk=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'distid'=>$distid,
                            'perid'=>$perid);
            $dataobs=$ldistribution->_getUltimateObservation($pk);
            $dataobs=$dataobs[0];
            $data=array('comments'=>$comment,'state'=>$state);
            if ($dataobs) {
                $pk['logobdistrid']=$dataobs['logobdistrid'];
                if ($ldistribution->_update($data,$pk)) {
                    // $dbdistribution->_update($data,$pk);
                    $this->_redirect("/distribution/distribution/index");
                }
            }
            else{
                $datadis=$dbdistribution->_getFilter($pk);
                $formData=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'distid'=>$distid,
                                'perid'=>$perid,'observation'=>$datadis[0]['observation'],'register'=>$this->sesion->uid,
                                'comments'=>$comment,'state'=>'A');
                if ($ldistribution->_save($formData)) {
                    $data=array("state"=>'A');
                    $dbdistribution->_update($data,$pk);
                    $this->_redirect("/distribution/distribution/index");
                }
            }
        } catch (Exception $e) {
            print "Error:".$e->getMessage();
        }

    }

    public function editAction()
    {
        $distid = base64_decode($this->_getParam("distid"));
        $perid = base64_decode($this->_getParam("perid"));
        $anio = $this->_getParam("anio");

        $dataforUpdate = array( 'distid' => $distid,
                                'perid'  => $perid,
                                'anio'   => $anio );
        $this->view->dataforUpdate = $dataforUpdate;
        
        $periodsDb = new Api_Model_DbTable_Periods();
        $distributionDb = new Distribution_Model_DbTable_Distribution();
        $form = new Distribution_Form_Distribution();

        //$form->perid->setAttrib("disabled", "");
        $eid   = $this->sesion->eid;
        $oid   = $this->sesion->oid;
        $escid = $this->sesion->escid;
        $subid = $this->sesion->subid;

        $where = array('eid'=>$eid, 'oid'=>$oid, 'perid'=>$perid);
        $attrib = array('name', 'perid');
        $period = $periodsDb->_getFilter($where, $attrib);
        $form->perid->addMultioption($period[0]['perid'], $period[0]['perid'].' | '.$period[0]['name']);
        $form->perid->setAttrib("disabled", "");

        $where = array( 
                        'eid'    => $eid,
                        'oid'    => $oid,
                        'escid'  => $escid,
                        'subid'  => $subid,
                        'distid' => $distid,
                        'perid'  => $perid );
        $distribution = $distributionDb->_getOne($where);
        $this->view->form = $form;      
        $form->populate($distribution);

       //$form->setAction("/distribution/distribution/edit/");
        /*if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $formData['perid'] = $r['perid'];
            if ($form->isValid($formData)) {
                $pk = array('eid'=>$data['eid'], 
                        'oid'=>$data['oid'], 
                        'distid'=>$data['distid'], 
                        'escid'=>$data['escid'], 
                        'subid'=>$data['subid'], 
                        'perid'=>$data['perid']);
                $formData['perid'] = base64_decode($formData['perid']);
                $formData['modified'] = $this->sesion->uid;
                $formData['updated'] = date('Y-m-d H:m:s');

                $distr = new Distribution_Model_DbTable_Distribution();
                $distr->_update($formData,$pk);
                $this->_redirect('/distribution/distribution/index/anio/'.$anio);
            }else{
                $form->populate($formData);
                print_r($formData);
            }
        }*/
    }

    public function savedistributionAction(){
        $this->_helper->layout()->disablelayout();
        $distributionDb = new Distribution_Model_DbTable_Distribution();
        $form           = new Distribution_Form_Distribution();

        $eid   = $this->sesion->eid;
        $oid   = $this->sesion->oid;
        $escid = $this->sesion->escid;
        $subid = $this->sesion->subid;
        $uid   = $this->sesion->uid;

        if ($this->getRequest()->isPost()) {
            $formdata = $this->getRequest()->getPost();
            if ($formdata['whySubmit'] == 'save') {
                if ($form->isValid($formdata)) {
                    unset($formdata['whySubmit']);
                    $where = array( 'eid'   => $eid,
                                    'oid'   => $oid,
                                    'escid' => $escid,
                                    'subid' => $subid,
                                    'perid' => $formdata['perid'] );

                    $attrib = array('perid');

                    $exist = $distributionDb->_getFilter($where, $attrib);

                    if (!$exist) {
                        $formdata['eid']       = $eid;
                        $formdata['oid']       = $oid;
                        $formdata['escid']     = $escid;
                        $formdata['subid']     = $subid;
                        $formdata['register']  = $uid;
                        $formdata['distid']    = time();
                        $formdata['datepress'] = date("Y-m-d", strtotime($formdata['datepress']));

                        if ($distributionDb->_save($formdata)) {
                            echo 'exito';
                        }else{
                            echo 'fallo-guardar';
                        }
                    }else{
                        echo 'existe';
                    }
                }else{
                    echo 'falta-datos';
                }
            }elseif($formdata['whySubmit'] == 'update'){
                unset($formdata['whySubmit']);
                if($formdata['datepress']){
                    $pk = array(    'eid'    => $eid, 
                                    'oid'    => $oid, 
                                    'escid'  => $escid, 
                                    'subid'  => $subid, 
                                    'distid' => $formdata['distid'], 
                                    'perid'  => $formdata['perid'] );
                    unset($formdata['distid']);
                    unset($formdata['perid']);
                    $formdata['datepress'] = date("Y-m-d", strtotime($formdata['datepress']));
                    $formdata['modified']  = $this->sesion->uid;
                    $formdata['updated']   = date('Y-m-d H:m:s');
                    if ($distributionDb->_update($formdata,$pk)) {
                        echo 'exito';
                    }else{
                        echo 'fail-update';
                    }
                }else{
                    echo 'falta-datos';
                }
            }
        }
    }
    

    public function deleteAction()
    {
    	$distid = base64_decode($this->_getParam("distid"));
    	$perid = base64_decode($this->_getParam("perid"));
    	$data['eid']=$this->sesion->eid;
    	$data['oid']=$this->sesion->oid;
    	$data['escid']=$this->sesion->escid;
    	$data['subid']=$this->sesion->subid;
    	$data['distid']=$distid;
    	$data['perid']=$perid;
    	$distr_ = new Distribution_Model_DbTable_Distribution();
    	$r = $distr_->_delete($data);
    	$this->_helper->redirector('index','distribution','distribution');
    }

    public function academicloadAction(){
        try {
            $distid = base64_decode($this->_getParam("distid"));
            $perid = base64_decode($this->_getParam("perid"));
            $escid = base64_decode($this->_getParam("escid"));
            $subid = base64_decode($this->_getParam("subid"));
            $this->view->perid=$perid;
            $this->view->escid=$escid;
            $this->view->subid=$subid;
            $this->view->distid=$distid;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function coursesAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid = $this->_getParam("perid"); 
            $escid = $this->_getParam("escid"); 
            $subid = $this->_getParam("subid"); 
            $distid = $this->_getParam("distid");
            $this->view->escid=$escid;
            $this->view->subid=$subid;
            $this->view->perid=$perid;
            $this->view->distid=$distid;

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['subid']=$subid;
            $where['perid']=$perid;
            $order = array('curid  ASC','semid asc','courseid  ASC','turno asc');
            $percourses = new Api_Model_DbTable_PeriodsCourses();
            $courses=$percourses->_getFilter($where,$attrib=null,$order);
            if ($courses) {
                $tam=count($courses);
                $wherecourse['eid']=$eid;
                $wherecourse['oid']=$oid;
                $wherecourse['escid']=$escid;
                $wherecourse['subid']=$subid;
                $cours= new Api_Model_DbTable_Course();
                for ($i=0; $i < $tam; $i++) { 
                    $wherecourse['curid']=$courses[$i]['curid'];
                    $wherecourse['courseid']=$courses[$i]['courseid'];
                    $dbcourse=$cours->_getOne($wherecourse);
                    $courses[$i]['name']=$dbcourse['name'];
                    $courses[$i]['type']=$dbcourse['type'];
                    $courses[$i]['credits']=$dbcourse['credits'];
                }
            }
            $this->view->courses=$courses;

        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function modifytypecourseAction(){
            $param  =   $this->getRequest()->getParams();
            if (count($param)>3) {
                foreach ($param as $key => $value) {
                    if($key!="module" && $key!="controller" && $key!="action"){
                        $params_decode[base64_decode($key)] = base64_decode($value);
                    }
                }
                $param = $params_decode;
            }
            $curid =    trim($param['curid']);
            $courseid =    trim($param['courseid']);
            $turno =    trim($param['turno']);
            $escid =    trim($param['escid']);
            $subid =    trim($param['subid']);
            $distid =    trim($param['distid']);
            $perid =    trim($param['perid']);
            $type_rate  = trim($param['type_rate']);
            $pk  =   array(
                            'eid'   =>$this->sesion->eid,
                            'oid'   =>$this->sesion->oid,
                            'curid'   =>$curid,
                            'courseid'   =>$courseid,
                            'turno'   =>$turno,
                            'escid'   =>$escid,
                            'subid'   =>$subid,
                            'distid'   =>$distid,
                            'perid'   =>$perid,
                        );
            $data = array(
                            'type_rate' =>  $type_rate
                        );
        try {
            $percourses = new Api_Model_DbTable_PeriodsCourses();
            if ($percourses->_update($data,$pk)) {
                $json   =   array(
                                'status'    =>   true
                            );
            }
        } catch (Exception $e) {
            $json   =   array(
                            'status'    => false
                        );
        }
        $this->_helper->layout()->disablelayout();
        $this->_response->setHeader('Content-Type', 'application/json');                   
        $this->view->data = $json; 
    }

    public function addcoursesAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid = $this->_getParam("perid"); 
            $escid = $this->_getParam("escid"); 
            $subid = $this->_getParam("subid"); 
            $distid = $this->_getParam("distid");
            $this->view->perid=$perid;
            $this->view->distid=$distid;

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['state']="A";
            $cur = new Api_Model_DbTable_Curricula();
            $datacur = $cur->_getFilter($where,$attrib=null,$orders=null);
            $where['state']="T";
            $datacur1 = $cur->_getFilter($where,$attrib=null,$orders=null);
            if (!$datacur) $curriculas=$datacur1;
            else {
                if ($datacur1) $curriculas=array_merge($datacur,$datacur1);
                else $curriculas=$datacur;
            }
            $this->view->curriculas=$curriculas;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function listcoursesAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $curid = $this->_getParam("curid"); 
            $escid = $this->_getParam("escid"); 
            $semid = $this->_getParam("semid");
            $perid = $this->_getParam("perid");
            $distid = $this->_getParam("distid");
            $this->view->escid=$escid;
            $this->view->perid=$perid;
            $this->view->semid=$semid;
            $this->view->distid=$distid;

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['curid']=$curid;
            $where['escid']=$escid;
            $where['semid']=$semid;
            $where['state']="A";
            $order = array('courseid  ASC');
            $cour = new Api_Model_DbTable_Course();
            $courses = $cour->_getFilter($where,$attrib=null,$order);
            $this->view->courses=$courses;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function savecourseAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid=$this->sesion->uid;
            $subid=$this->_getParam("subid");
            $escid=$this->_getParam("escid");
            $perid=$this->_getParam("perid");
            $courseid=$this->_getParam("courseid");
            $curid=$this->_getParam("curid");
            $semid=$this->_getParam("semid");
            $distid=$this->_getParam("distid");
            $turno_ = $this->_getParam("turno");
            $turno1_ = $this->_getParam("turno1");
            $turno2_ = $this->_getParam("turno2");
            $turno3_ = $this->_getParam("turno3");
            $turno4_ = $this->_getParam("turno4");
            $turno5_ = $this->_getParam("turno5");
            $this->view->subid=$subid;
            $this->view->escid=$escid;
            $this->view->perid=$perid;
            $this->view->distid=$distid;
            $tam=count($courseid);
            
            $reg = new Api_Model_DbTable_PeriodsCourses();
            $where['eid'] = $data['eid'] = $eid;
            $where['oid'] = $data['oid'] = $oid;
            $where['perid'] = $data['perid'] = $perid;
            $where['curid'] = $data['curid'] = $curid;
            $where['escid'] = $data['escid'] = $escid;
            $where['subid'] = $data['subid'] = $subid;
            $data['semid'] = $semid;
            $data['distid'] = $distid;
            for($i=0; $i<$tam; $i++){
                $where['courseid'] = $data['courseid'] = $courseid[$i];
                $data['state_record'] = 'A';
                $data['state'] = 'A';
                $data['receipt'] = 'N';
                $data['register'] = $uid;
                $data['type_rate'] = '';
                $data['created'] = date("Y-m-d h:m:s");
                
                if($turno_[$i]=="A"){
                    $where['turno']= $turno_[$i];
                    $data['turno'] = $turno_[$i];
                    $datpercour=$reg->_getOne($where);
                    if (!$datpercour) $reg-> _save($data);
                }

                if($turno1_[$i]=="B"){
                    $where['turno']= $turno1_[$i];
                    $data['turno'] = $turno1_[$i];
                    $datpercour=$reg->_getOne($where);
                    if (!$datpercour) $reg-> _save($data);
                }

                if($turno2_[$i]=="C"){
                    $where['turno']= $turno2_[$i];
                    $data['turno'] = $turno2_[$i];
                    $datpercour=$reg->_getOne($where);
                    if (!$datpercour) $reg-> _save($data);
                }

                if($turno3_[$i]=="D"){
                    $where['turno']= $turno3_[$i];
                    $data['turno'] = $turno3_[$i];
                    $datpercour=$reg->_getOne($where);
                    if (!$datpercour) $reg-> _save($data);
                }

                if($turno4_[$i]=="E"){
                    $where['turno']= $turno4_[$i];
                    $data['turno'] = $turno4_[$i];
                    $datpercour=$reg->_getOne($where);
                    if (!$datpercour) $reg-> _save($data);
                }

                if($turno5_[$i]=="F"){
                    $where['turno']= $turno5_[$i];
                    $data['turno'] = $turno5_[$i];
                    $datpercour=$reg->_getOne($where);
                    if (!$datpercour) $reg-> _save($data);
                }
                if ($i==($tam-1)) $this->view->msg=1;
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function deletecourseAction(){
        try {
            $this->_helper->layout()->disablelayout();          
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $courseid=$this->_getParam("courseid");
            $semid=$this->_getParam("semid");
            $curid=$this->_getParam("curid");
            $escid=$this->_getParam("escid");
            $perid=$this->_getParam("perid");
            $turno=$this->_getParam("turno");
            $subid=$this->_getParam("subid");
            $this->view->subid=$subid;
            $this->view->escid=$escid;
            $this->view->perid=$perid;

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['perid']=$perid;
            $where['curid']=$curid;
            $where['courseid']=$courseid;
            $where['subid']=$subid;
            $where['turno']=$turno;
            $percourses = new Api_Model_DbTable_PeriodsCourses();
            $datpercour=$percourses->_getOne($where);
            $doccour= new Api_Model_DbTable_Coursexteacher();
            $datadoccour=$doccour->_getFilter($where,$attrib=null,$orders=null);
            if (!$datadoccour) {
                $percourses->_delete($where);
                $datpercourse=$percourses->_getOne($where);
                if($datpercourse) $this->view->exis=1;
            }else $this->view->exis=1;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function teachersAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid = $this->_getParam("perid"); 
            $escid = $this->_getParam("escid"); 
            $subid = $this->_getParam("subid"); 
            $distid = $this->_getParam("distid");
            $this->view->escid=$escid;
            $this->view->subid=$subid;
            $this->view->perid=$perid;
            $this->view->distid=$distid;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function primaryteacherAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid = $this->_getParam("perid"); 
            $escid = $this->_getParam("escid"); 
            $subid = $this->_getParam("subid"); 
            $distid = $this->_getParam("distid");
            $this->view->perid=$perid;
            $this->view->subid=$subid;
            $this->view->distid=$distid;
            $this->view->escid=$escid;
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['subid']=$subid;
            $where['rid']='DC';
            $where['state']='A';  

            $doc = new Api_Model_DbTable_Users();
            $teacher = $doc->_getUserXRidXEscidAll($where);
            $where['state']='I';
            $teacher1 = $doc->_getUserXRidXEscidAll($where);
            if ($teacher1) $datateacher=array_merge($teacher,$teacher1);
            else $datateacher=$teacher;
            $tam=count($datateacher);
            $whereinfo['eid']=$eid;
            $whereinfo['oid']=$oid;
            $whereinfo['escid']=$escid;
            $whereinfo['subid']=$subid;

            $info= new Api_Model_DbTable_UserInfoTeacher();
            $bdcourseteach=new Api_Model_DbTable_Coursexteacher();
            $whe = array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
            for ($i=0; $i < $tam; $i++) {
                $whereinfo['pid']=$datateacher[$i]['pid'];
                $whereinfo['uid']=$datateacher[$i]['uid'];
                $datainfo=$info->_getOne($whereinfo);
                $datateacher[$i]['category']=$datainfo['category'];
                $datateacher[$i]['condision']=$datainfo['condision'];
                $datateacher[$i]['dedication']=$datainfo['dedication'];
                $datateacher[$i]['charge']=$datainfo['charge'];
                $datateacher[$i]['contract']=$datainfo['contract'];
                $whe['pid']=$datateacher[$i]['pid'];
                $datacourseteacher=$bdcourseteach->_getFilter($whe);
                $datateacher[$i]['courseasig']=$datacourseteacher[0]['courseid'];
            }
            // print_r($datateacher);
            $this->view->teacher=$datateacher;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function modifyteacherAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid = $this->_getParam("uid"); 
            $pid = $this->_getParam("pid"); 
            $subid = $this->_getParam("subid"); 
            $escid = $this->_getParam("escid");
            $content = $this->_getParam("content");
            $option = $this->_getParam("option");
            $this->view->escid=$escid;
            $this->view->subid=$subid;
            $this->view->perid=$perid;
            $this->view->distid=$distid;
            $pk['eid']=$eid;
            $pk['oid']=$oid;
            $pk['escid']=$escid;
            $pk['uid']=$uid;
            $pk['pid']=$pid;
            $pk['subid']=$subid;
            if ($option=="CAT") $data['category']=$content;
            if ($option=="CON") $data['condision']=$content;
            if ($option=="DED") $data['dedication']=$content;
            if ($option=="STA") {
                $data['state']=$content;
                $user = new Api_Model_DbTable_Users();
                if ($user->_update($data,$pk)) $this->view->state=1;
            }else{
                $info = new Api_Model_DbTable_UserInfoTeacher();
                $info->_update($data,$pk);
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function practiceteacherAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid = $this->_getParam("perid"); 
            $escid = $this->_getParam("escid"); 
            $subid = $this->_getParam("subid"); 
            $distid = $this->_getParam("distid");
            $this->view->perid=$perid;
            $this->view->subid=$subid;
            $this->view->distid=$distid;
            $this->view->escid=$escid;
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['subid']=$subid;
            $where['rid']='JP';
            $where['state']='A';            
            $doc = new Api_Model_DbTable_Users();
            $teacher = $doc->_getUserXRidXEscidAll($where);
            $where['state']='I';
            $teacher1 = $doc->_getUserXRidXEscidAll($where);
            if (!$teacher) $datateacher=$teacher1;
            else{
                if ($teacher1) $datateacher=array_merge($teacher,$teacher1);
                else $datateacher=$teacher;
            }
            $tam=count($datateacher);
            $whereinfo['eid']=$eid;
            $whereinfo['oid']=$oid;
            $whereinfo['escid']=$escid;
            $whereinfo['subid']=$subid;
            $info= new Api_Model_DbTable_UserInfoTeacher();
            $bdcourseteach=new Api_Model_DbTable_Coursexteacher();
            $whe = array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
            $attrib=array('courseid');
            if ($datateacher) {
                for ($i=0; $i < $tam; $i++) {
                    $whereinfo['pid']=$datateacher[$i]['pid'];
                    $whereinfo['uid']=$datateacher[$i]['uid'];
                    $datainfo=$info->_getOne($whereinfo);
                    $datateacher[$i]['category']=$datainfo['category'];
                    $datateacher[$i]['condision']=$datainfo['condision'];
                    $datateacher[$i]['dedication']=$datainfo['dedication'];
                    $datateacher[$i]['charge']=$datainfo['charge'];
                    $datateacher[$i]['contract']=$datainfo['contract'];
                    $whe['pid']=$datateacher[$i]['pid'];             
                    $datacourseteacher=$bdcourseteach->_getFilter($whe,$attrib);
                    $datateacher[$i]['courseasig']=$datacourseteacher[0]['courseid'];
                }
            }

            $this->view->teachers=$datateacher;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function supportteacherAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $perid = $this->_getParam("perid"); 
            $escid = $this->_getParam("escid"); 
            $subid = $this->_getParam("subid"); 
            $distid = $this->_getParam("distid");
            $this->view->perid=$perid;
            $this->view->distid=$distid;

            $form = new Distribution_Form_Search();            
            $this->view->form = $form;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function listsupportteacherAction(){
        try {
            $this->_helper->layout()->disablelayout();           
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $name=$this->_getParam("name");
            $pid =$this->_getParam("pid");
            $distid =$this->_getParam("distid"); 
            $perid =$this->_getParam("perid");
            $this->view->distid=$distid;
            $this->view->perid=$perid;
            $this->view->escid=$escid;
            $this->view->subid=$subid;

            $where['eid']=$eid;
            $user = new Api_Model_DbTable_Users();
            if($pid<>''){
                $where['oid']=$oid;
                $where['pid']=$pid;
                $datauser = $user->_getUserXPid($where);
            }else{
                $where['rid']='DC';
                $where['nom']=strtoupper($name);
                $datauser = $user->_getUsuarioXNombre($where);
            }
            $bdcourseteach=new Api_Model_DbTable_Coursexteacher();
            $whe = array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'subid'=>$subid);
            $attrib=array('courseid');
            $len=count($datauser);
            for ($i=0; $i < $len; $i++) {            
                $whe['pid']=$datauser[$i]['pid'];             
                $datacourseteacher=$bdcourseteach->_getFilter($whe,$attrib);
                $datauser[$i]['courseasig']=$datacourseteacher[0]['courseid'];
                
            }
            $this->view->usuarios=$datauser;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
            
        }
    }

    public function assigncoursesAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $perid = base64_decode($this->_getParam("perid"));
            $esciddoc = base64_decode($this->_getParam("escid"));
            $subiddoc = base64_decode($this->_getParam("subid"));
            $distid = base64_decode($this->_getParam("distid"));
            $uid = base64_decode($this->_getParam("uid"));
            $pid = base64_decode($this->_getParam("pid"));
            $this->view->eid=$eid;
            $this->view->oid=$oid;
            $this->view->uid=$uid;
            $this->view->pid=$pid;
            $this->view->escid=$escid;
            $this->view->subid=$subid;
            $this->view->subiddoc=$subiddoc;
            $this->view->esciddoc=$esciddoc;
            $this->view->perid=$perid;
            $this->view->subid=$subid;
            $this->view->distid=$distid;

            if ($esciddoc<>$escid) {
                $this->view->support=1;
                $pkdist['eid'] = $eid;
                $pkdist['oid'] = $oid;
                $pkdist['escid'] = $esciddoc;
                $pkdist['perid'] = $perid;
                $dist = new Distribution_Model_DbTable_Distribution();
                $datadist = $dist->_getFilter($pkdist);
                if(!$datadist){ ?>
                    <script>  
                        alert("No se encuentra disponible los docentes de apoyo de esa Escuela\nIntentelo mas tarde.");
                        window.close();
                    </script>
                    <?php
                    break;
                }
            }

            $wherepers['eid']=$eid;
            $wherepers['pid']=$pid;
            $pers = new Api_Model_DbTable_Person();
            $dataperson=$pers->_getOne($wherepers);
            $this->view->person=$dataperson;

            $form= new Distribution_Form_DistributionAdmin();
            $this->view->form=$form;

            $wherecurr['eid']=$eid;
            $wherecurr['oid']=$oid;
            $wherecurr['escid']=$escid;
            $wherecurr['state']="A";
            $cur = new Api_Model_DbTable_Curricula();
            $datacur = $cur->_getFilter($wherecurr,$attrib=null,$orders=null);
            $wherecurr['state']="T";
            $datacur1 = $cur->_getFilter($wherecurr,$attrib=null,$orders=null);
            if (!$datacur) $curriculas=$datacur1;
            else {
                if ($datacur1) $curriculas=array_merge($datacur,$datacur1);
                else $curriculas=$datacur;
            }
            $this->view->curriculas=$curriculas;

            $wheredoc['eid']=$eid;
            $wheredoc['oid']=$oid;
            $wheredoc['uid']=$uid;
            $wheredoc['pid']=$pid;
            $wheredoc['perid']=$perid;
            $wheredoc['distid']=$distid;
            $courdoc = new Api_Model_DbTable_Coursexteacher();
            $courasig = $courdoc->_getFilter($wheredoc,$attrib=null,$orders=array('curid','courseid','turno'));
            if ($courasig) {
                $tam=count($courasig);
                $wherecourse['eid']=$eid;
                $wherecourse['oid']=$oid;
                $cours= new Api_Model_DbTable_Course();
                for ($i=0; $i < $tam; $i++) { 
                    $wherecourse['subid']=$courasig[$i]['subid'];
                    $wherecourse['escid']=$courasig[$i]['escid'];
                    $wherecourse['curid']=$courasig[$i]['curid'];
                    $wherecourse['courseid']=$courasig[$i]['courseid'];
                    $dbcourse=$cours->_getOne($wherecourse);
                    $courasig[$i]['name']=$dbcourse['name'];
                    $courasig[$i]['credits']=$dbcourse['credits'];
                }
            }
            $this->view->cursosasignados=$courasig;
            
            $pk['eid']=$eid;
            $pk['oid']=$oid;
            $pk['escid']=$esciddoc;
            $pk['perid']=$perid;
            $pk['uid']=$uid;
            $pk['pid']=$pid;
            $distadm = new Distribution_Model_DbTable_DistributionAdmin();
            $labor=$distadm->_getFilter($pk);
            $this->view->administrativas=$labor;

        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function hourstheoreticalAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $course=$this->_getParam('courseid');
            $escid=$this->_getParam('escid');
            $subid=$this->_getParam('subid');
            $curid=$this->_getParam('curid');
            $coursep=split(";--;",$course);
            $courseid=$coursep[0];
            $dbcourse= new Api_Model_DbTable_Course();
            $where=array('eid'=>$eid,'oid'=>$oid,'curid'=>$curid,'courseid'=>$courseid,'escid'=>$escid,'subid'=>$subid);
            $datacour=$dbcourse->_getOne($where);
            $this->view->hteo=$datacour['hours_theoretical'];
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function hourspracticalAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $course=$this->_getParam('courseid');
            $escid=$this->_getParam('escid');
            $subid=$this->_getParam('subid');
            $curid=$this->_getParam('curid');
            $coursep=split(";--;",$course);
            $courseid=$coursep[0];
            $dbcourse= new Api_Model_DbTable_Course();
            $where=array('eid'=>$eid,'oid'=>$oid,'curid'=>$curid,'courseid'=>$courseid,'escid'=>$escid,'subid'=>$subid);
            $datacour=$dbcourse->_getOne($where);
            $this->view->hpra=$datacour['hours_practical'];
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
    public function printreportdistriAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $fecha= new Zend_Date();
            $dia=$fecha->get(Zend_Date::DAY);
            $mes=$fecha->get(Zend_Date::MONTH_NAME);
            $anio=$fecha->get(Zend_Date::YEAR);
            $dateact=$dia." de ".$mes." del ".$anio;
            $this->view->anio=$anio;
            $this->view->dateact=$dateact;
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid=base64_decode($this->_getParam('uid'));
            $pid=base64_decode($this->_getParam('pid'));
            $perid=base64_decode($this->_getParam('perid'));
            $escid=base64_decode($this->_getParam('escid'));
            $subid=base64_decode($this->_getParam('subid'));
            $distid=base64_decode($this->_getParam('distid'));
            $where=array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid,'perid'=>$perid,'distid'=>$distid);
            $orders=array('curid','courseid','turno');
            $courdoc = new Api_Model_DbTable_Coursexteacher();
            $courasig = $courdoc->_getFilter($where,$attrib=null,$orders);
            if ($courasig) {
                $len=count($courasig);
                $wherecourse['eid']=$eid;
                $wherecourse['oid']=$oid;
                $cours= new Api_Model_DbTable_Course();
                for ($i=0; $i < $len; $i++) { 
                    $wherecourse['subid']=$courasig[$i]['subid'];
                    $wherecourse['escid']=$courasig[$i]['escid'];
                    $wherecourse['curid']=$courasig[$i]['curid'];
                    $wherecourse['courseid']=$courasig[$i]['courseid'];
                    $dbcourse=$cours->_getOne($wherecourse);
                    $courasig[$i]['name']=$dbcourse['name'];
                    $courasig[$i]['credits']=$dbcourse['credits'];
                }
            }
            // print_r($courasig);exit();
            $this->view->cursosasignados=$courasig;

            $where1['eid']=$eid;
            $where1['oid']=$oid;
            $where1['escid']=$escid;
            $where1['perid']=$perid;
            $where1['uid']=$uid;
            $where1['pid']=$pid;
            $distadm = new Distribution_Model_DbTable_DistributionAdmin();
            $labor=$distadm->_getFilter($where1);
            // print_r($labor);exit();
            $bdperson = new Api_Model_DbTable_Person();
            $this->view->administrativas=$labor;
            $where2=array('eid'=>$eid,'oid'=>$oid,'pid'=>$pid);
            $dataperson = $bdperson->_getOne($where2);
            $this->view->dataper=$dataperson;

            $pid1=$this->sesion->pid;
            $where3=array('eid'=>$eid,'oid'=>$oid,'pid'=>$pid1);
            $dataperson1 = $bdperson->_getOne($where3);
            $this->view->dataper1=$dataperson1;

            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
            $base_speciality =  new Api_Model_DbTable_Speciality();        
            $speciality = $base_speciality ->_getOne($where);
            $parent=$speciality['parent'];
            $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
            $parentesc= $base_speciality->_getOne($wher);

            if ($parentesc) {
                $pala='ESPECIALIDAD DE ';
                $spe['esc']=$parentesc['name'];
                $spe['parent']=$pala.$speciality['name'];
            }
            else{
                $spe['esc']=$speciality['name'];
                $spe['parent']='';  
            }
            $names=strtoupper($spe['esc']);
            $namep=strtoupper($spe['parent']);
            $namev=$names." ".$namep;
            $this->view->namev=$namev;
            $namefinal=$names." <br> ".$namep;
            $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";
            
            $fac = array('eid'=>$eid,'oid'=>$oid,'facid'=>$speciality['facid']);
            $base_fac =  new Api_Model_DbTable_Faculty();        
            $datafa= $base_fac->_getOne($fac);
            $namef = strtoupper($datafa['name']);

            $dbimpression = new Api_Model_DbTable_Countimpressionall();
    
            // $uid=$this->sesion->uid;
            $uidim=$this->sesion->pid;
            // $pid=$uidim;

            $data = array(
                'eid'=>$eid,
                'oid'=>$oid,
                'uid'=>$uid,
                'escid'=>$escid,
                'subid'=>$subid,
                'pid'=>$pid,
                'type_impression'=>'impresion_memorandum_carga_acacemica',
                'date_impression'=>date('Y-m-d H:i:s'),
                'pid_print'=>$uidim
                );
            $dbimpression->_save($data);            

            $wheri = array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid,'escid'=>$escid,
                'subid'=>$subid,'type_impression'=>'impresion_memorandum_carga_acacemica');
            $dataim = $dbimpression->_getFilter($wheri);

            $wheri1 = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'impresion_memorandum_carga_acacemica');
            $dataim1 = $dbimpression->_getFilter($wheri1);
            
            $conte = count($dataim1);
            $this->view->conte=$conte;
            $co=count($dataim);            
            $codigo=$co." - ".$uidim;
            $header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
            $header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", $namelogo, $header);
            $header = str_replace("?codigo", $codigo, $header);

            $this->view->header=$header;
            $this->view->footer=$footer;
            $this->view->perid=$perid;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
    public function coursesxcurriculaAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
               
            $curid= $this->_getParam("curid");
            $escid= $this->_getParam("escid");
            $perid= $this->_getParam("perid");

            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['perid']=$perid;
            $where['curid']=$curid;
            $order = array('semid ASC','courseid ASC','turno asc');
            $percourses = new Api_Model_DbTable_PeriodsCourses();
            $courses=$percourses->_getFilter($where,$attrib=null,$order);
            if ($courses) {
                $tam=count($courses);
                $wherecourse['eid']=$eid;
                $wherecourse['oid']=$oid;
                $wherecourse['escid']=$escid;
                $wherecourse['curid']=$curid;
                $cours= new Api_Model_DbTable_Course();
                for ($i=0; $i < $tam; $i++) { 
                    $wherecourse['subid']=$courses[$i]['subid'];
                    $wherecourse['courseid']=$courses[$i]['courseid'];
                    $dbcourse=$cours->_getOne($wherecourse);
                    $courses[$i]['name']=$dbcourse['name'];
                    $courses[$i]['type']=$dbcourse['type'];
                    $courses[$i]['credits']=$dbcourse['credits'];
                }
            }
            $this->view->courses=$courses;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function saveasigncourseAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $distid =$this->_getParam("distid"); 
            $semid =$this->_getParam("semid"); 
            $curid =$this->_getParam("curid"); 
            $perid =$this->_getParam("perid"); 
            $courseid =$this->_getParam("courseid"); 
            $grupos =$this->_getParam("grupos"); 
            $hteoria =$this->_getParam("hteoria");
            $hpractica =$this->_getParam("hpractica");
            $totalhoras =$this->_getParam("totalhoras");
            $compromiso =$this->_getParam("compromiso");
            $uid =$this->_getParam("uid");
            $pid =$this->_getParam("pid");
            $turno =$this->_getParam("turno");
            $principal =$this->_getParam("principal");
            $esciddoc =$this->_getParam("esciddoc");
            $subiddoc =$this->_getParam("subiddoc");
            $estado =$this->_getParam("estado");
       
            $this->view->escid=$escid;
            $this->view->subid=$subid;
            $this->view->subiddoc=$subiddoc;
            $this->view->esciddoc=$esciddoc;
            $this->view->uid=$uid;
            $this->view->pid=$pid;
            $this->view->eid=$eid;
            $this->view->oid=$oid;
            $this->view->distid=$distid;
            $this->view->perid=$perid; 

            $wheredoc['eid'] = $datadoccour['eid'] = $eid;
            $wheredoc['oid'] = $datadoccour['oid'] = $oid;
            $wheredoc['escid'] = $datadoccour['escid'] = $escid;
            $wheredoc['subid'] = $datadoccour['subid'] = $subid;
            $wheredoc['courseid'] = $datadoccour['courseid'] = $courseid;
            $wheredoc['curid'] = $datadoccour['curid'] = $curid;
            $wheredoc['turno'] = $datadoccour['turno'] = $turno;
            $wheredoc['perid'] = $datadoccour['perid'] = $perid;
            $datadoccour['uid'] = $uid;
            $datadoccour['pid'] = $pid;
            $datadoccour['hours_t'] = $hteoria;
            $datadoccour['hours_p'] = $hpractica;
            $datadoccour['is_com'] = $compromiso;
            $datadoccour['distid'] = $distid;
            $datadoccour['hours_total'] = $totalhoras;
            $datadoccour['groups'] = $grupos;
            $datadoccour['state'] = $estado;
            $datadoccour['semid'] = $semid;
            $datadoccour['is_main'] = $principal;
            $doccourse = new Api_Model_DbTable_Coursexteacher();
            $exiscour=$doccourse->_getFilter($wheredoc,$attrib=null,$orders=null);
            if($exiscour and $principal=="S"){ ?>
                <script>
                    alert('La Asignatura ya ha sido asignada a otro Docente : Elija otra Asignatura');
                </script>
                <?php   
            }else{
                $wheredoc['uid']=$uid;
                $wheredoc['pid']=$pid;
                $exisdoccour=$doccourse->_getFilter($wheredoc,$attrib=null,$orders=null);
                if ($exisdoccour) { ?>
                    <script type="text/javascript">
                        alert("El Docente ya esta asignado a esta Asignatura.");
                    </script>
                    <?php
                }else{
                    $doccourse->_save($datadoccour);
                    $pkdistdoc['eid'] = $datadistdoc['eid'] = $eid;
                    $pkdistdoc['oid'] = $datadistdoc['oid'] = $oid;
                    $pkdistdoc['perid'] = $datadistdoc['perid'] = $perid;
                    $pkdistdoc['uid'] = $datadistdoc['uid'] = $uid;
                    $pkdistdoc['pid'] = $datadistdoc['pid'] = $pid;
                    $datadistdoc['total_hour_admin'] = 0;
                    $distdoc = new Distribution_Model_DbTable_DistributionTeacher();
                    if($esciddoc==$escid){
                        $pkdistdoc['distid'] = $datadistdoc['distid'] = $distid;
                        $pkdistdoc['subid'] = $datadistdoc['subid'] = $subid;
                        $pkdistdoc['escid'] = $datadistdoc['escid'] = $escid;
                        $exisdistdoc=$distdoc->_getOne($pkdistdoc);
                        if(!$exisdistdoc){
                            $datadistdoc['total_hour_acad'] = $totalhoras;
                            $datadistdoc['register'] = $this->sesion->uid;
                            $datadistdoc['created'] = date('Y-m-d');
                            $distdoc->_save($datadistdoc);
                        }
                    }else{
                        $wheredis['eid']=$eid;
                        $wheredis['oid']=$oid;
                        $wheredis['escid']=$esciddoc;
                        $wheredis['perid']=$perid;
                        $dist = new Distribution_Model_DbTable_Distribution();
                        $distiddoc=$dist->_getFilter($wheredis);
                        
                        $pkdistdoc['distid'] = $datadistdoc['distid'] = $distiddoc[0]['distid'];
                        $pkdistdoc['subid'] = $datadistdoc['subid'] = $subiddoc;
                        $pkdistdoc['escid'] = $datadistdoc['escid'] = $esciddoc;
                        $exisdistdoc=$distdoc->_getOne($pkdistdoc);
                        if(!$exisdistdoc){
                            $datadistdoc['total_hour_acad'] = $totalhoras;
                            $datadistdoc['register'] = $this->sesion->uid;
                            $datadistdoc['created'] = date('Y-m-d');
                            $distdoc->_save($datadistdoc);
                        }
                    }                    
                }
            }

            $wheretea['eid']=$eid;
            $wheretea['oid']=$oid;
            $wheretea['uid']=$uid;
            $wheretea['pid']=$pid;
            $wheretea['perid']=$perid;
            $wheretea['distid']=$distid;
            $courasig = $doccourse->_getFilter($wheretea,$attrib=null,$orders=array('curid','courseid','turno'));
            if ($courasig) {
                $tam=count($courasig);
                $wherecourse['eid']=$eid;
                $wherecourse['oid']=$oid;
                $cours= new Api_Model_DbTable_Course();
                for ($i=0; $i < $tam; $i++) { 
                    $wherecourse['subid']=$courasig[$i]['subid'];
                    $wherecourse['escid']=$courasig[$i]['escid'];
                    $wherecourse['curid']=$courasig[$i]['curid'];
                    $wherecourse['courseid']=$courasig[$i]['courseid'];
                    $dbcourse=$cours->_getOne($wherecourse);
                    $courasig[$i]['name']=$dbcourse['name'];
                    $courasig[$i]['credits']=$dbcourse['credits'];
                }
            }
            $this->view->cursosasignados=$courasig;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function deleteasigncourseAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $subiddoc  = base64_decode($this->_getParam("subiddoc")); 
            $esciddoc = base64_decode($this->_getParam('esciddoc'));
            $courseid = base64_decode($this->_getParam("courseid")); 
            $distid = base64_decode($this->_getParam("distid"));
            $turno = base64_decode($this->_getParam("turno")); 
            $curid = base64_decode($this->_getParam("curid"));
            $perid = base64_decode($this->_getParam("perid"));
            $uid = base64_decode($this->_getParam("uid"));
            $pid = base64_decode($this->_getParam("pid"));
            
            $pk['eid']=$eid;
            $pk['oid']=$oid;
            $pk['escid']=$escid;
            $pk['subid']=$subid;
            $pk['courseid']=$courseid;
            $pk['curid']=$curid;
            $pk['turno']=$turno;
            $pk['perid']=$perid;
            $pk['uid']=$uid;
            $pk['pid']=$pid;
            $doccour = new Api_Model_DbTable_Coursexteacher();
            $doccour->_delete($pk);
            $this->_redirect("/distribution/distribution/assigncourses/uid/".base64_encode($uid).
                "/pid/".base64_encode($pid)."/distid/".base64_encode($distid)."/perid/".base64_encode($perid).
                "/subid/".base64_encode($subiddoc)."/escid/".base64_encode($esciddoc));
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function savedistributionadminAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $escid = $this->sesion->escid;
            $subid = $this->sesion->subid;
            $perid = $this->_getParam("perid");
            $uid = $this->_getParam("uid");
            $pid = $this->_getParam("pid");
            $distid = $this->_getParam("distid");
            $subiddoc = $this->_getParam("subiddoc"); 
            $esciddoc = $this->_getParam("esciddoc");
            $work = $this->_getParam("work");            
            $hours = $this->_getParam("hours");
            $this->view->escid=$escid;
            $this->view->subid=$subid;
            $this->view->esciddoc=$esciddoc;
            $this->view->subiddoc=$subiddoc;
            $this->view->uid=$uid;
            $this->view->pid=$pid;
            $this->view->eid=$eid;
            $this->view->oid=$oid;
            $this->view->distid=$distid;
            $this->view->perid=$perid; 

            if($esciddoc<>$escid){
                $wheredis['eid']=$eid;
                $wheredis['oid']=$oid;
                $wheredis['escid']=$esciddoc;
                $wheredis['perid']=$perid;
                $dist = new Distribution_Model_DbTable_Distribution();
                $distiddoc=$dist->_getFilter($wheredis,$attrib=null,$orders=null);
                $dist = $dis->_getDistribucionEscuela($eid,$oid,'2ES',$perid);
                $distid = $distiddoc[0]['distid'];
            }

            $datadistadm['eid'] = $pk['eid'] = $eid;
            $datadistadm['oid'] = $pk['oid'] = $oid;
            $datadistadm['uid'] = $pk['uid'] = $uid;
            $datadistadm['pid'] = $pk['pid'] = $pid;
            $datadistadm['escid'] = $pk['escid'] = $esciddoc;
            $datadistadm['perid'] = $pk['perid'] = $perid;            
            $datadistadm['subid'] = $subiddoc;
            $datadistadm['distid'] = $distid;
            $datadistadm['work'] = $work;
            $datadistadm['hours'] = $hours;
            
            $distadm = new Distribution_Model_DbTable_DistributionAdmin();
            $distadm->_save($datadistadm);
            $labor = $distadm->_getFilter($pk,$attrib=null);
            $this->view->administrativas=$labor;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function deletedistributionadminAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $admdistid = base64_decode($this->_getParam("admdistid"));
            $esciddoc = base64_decode($this->_getParam("esciddoc"));
            $subiddoc = base64_decode($this->_getParam("subiddoc"));
            $distid = base64_decode($this->_getParam("distid"));
            $uid = base64_decode($this->_getParam("uid"));
            $pid = base64_decode($this->_getParam("pid"));
            $perid = base64_decode($this->_getParam("perid"));

            $pk['eid']=$eid;
            $pk['oid']=$oid;
            $pk['perid']=$perid;
            $pk['uid']=$uid;
            $pk['pid']=$pid;
            $pk['admdistid']=$admdistid;
            $distadmin = new Distribution_Model_DbTable_DistributionAdmin();
            if($esciddoc<>$escid){
                $pkdist['eid']=$eid;
                $pkdist['oid']=$oid;
                $pkdist['escid']=$esciddoc;
                $pkdist['perid']=$perid;
                $dist = new Distribution_Model_DbTable_Distribution();
                $datadist = $dist->_getFilter($pkdist,$atrib=null,$orders=null);

                $pk['escid']=$esciddoc;
                $pk['distid']=$datadist[0]['distid'];
                $pk['subid']=$datadist[0]['subid'];
                $distadmin->_delete($pk);
            }else{
                $pk['escid']=$escid;
                $pk['distid']=$distid;
                $pk['subid']=$subid;
                $distadmin->_delete($pk);
            }
            $this->_redirect("/distribution/distribution/assigncourses/uid/".base64_encode($uid).
                "/pid/".base64_encode($pid)."/distid/".base64_encode($distid)."/perid/".base64_encode($perid).
                "/subid/".base64_encode($subiddoc)."/escid/".base64_encode($esciddoc));
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function previewAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $perid = base64_decode($this->_getParam("perid"));
            $esciddoc = base64_decode($this->_getParam("esciddoc"));
            $subiddoc = base64_decode($this->_getParam("subiddoc"));
            $distid = base64_decode($this->_getParam("distid"));
            $uid = base64_decode($this->_getParam("uid"));
            $pid = base64_decode($this->_getParam("pid"));
            $this->view->eid=$eid;
            $this->view->oid=$oid;
            $this->view->uid=$uid;
            $this->view->pid=$pid;
            $this->view->escid=$escid;
            $this->view->subiddoc=$subiddoc;
            $this->view->esciddoc=$esciddoc;
            $this->view->perid=$perid;
            $this->view->subid=$subid;
            $this->view->distid=$distid;

            $person = new Api_Model_DbTable_Person();
            $data_person = $person->_getOne($where=array('eid' => $eid, 'pid' => $pid));
            $this->view->person = $data_person;

            $where = array('eid' => $eid, 'oid' => $oid, 'uid' => $uid, 'pid' => $pid, 'perid' => $perid);
            $cour_tea = new Api_Model_DbTable_Coursexteacher();
            $data_courses = $cour_tea->_getFilter($where,$attrib=null,$orders=array('curid','courseid','turno'));
            $this->view->courses = $data_courses;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function printpreviewAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $perid = base64_decode($this->_getParam("perid"));
            $distid = base64_decode($this->_getParam("distid"));
            $uid = base64_decode($this->_getParam("uid"));
            $pid = base64_decode($this->_getParam("pid"));
            $this->view->eid=$eid;
            $this->view->oid=$oid;
            $this->view->uid=$uid;
            $this->view->pid=$pid;
            $this->view->escid=$escid;
            $this->view->perid=$perid;
            $this->view->subid=$subid;
            $this->view->distid=$distid;

            $person = new Api_Model_DbTable_Person();
            $data_person = $person->_getOne($where=array('eid' => $eid, 'pid' => $pid));
            $this->view->person = $data_person;

            $where = array('eid' => $eid, 'oid' => $oid, 'uid' => $uid, 'pid' => $pid, 'perid' => $perid);
            $cour_tea = new Api_Model_DbTable_Coursexteacher();
            $data_courses = $cour_tea->_getFilter($where,$attrib=null,$orders=array('curid','courseid','turno'));
            $this->view->courses = $data_courses;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function generalpreviewAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $escid = base64_decode($this->_getParam("escid"));
            $subid = base64_decode($this->_getParam("subid"));
            $perid = base64_decode($this->_getParam("perid"));
            $distid = base64_decode($this->_getParam("distid"));
            $this->view->eid = $eid;
            $this->view->oid = $oid;
            $this->view->escid = $escid;
            $this->view->subid = $subid;
            $this->view->perid = $perid;
            $this->view->distid = $distid;

            $where = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid, 'perid' => $perid);
            $cour_tea = new Api_Model_DbTable_Coursexteacher();
            $data_courses = $cour_tea->_getFilter($where,$attrib=null,$orders=array('uid','curid','courseid','turno'));
            $this->view->courses = $data_courses;

            $per_cour = new Api_Model_DbTable_PeriodsCourses();
            $data_percour = $per_cour->_getCoursesIsNotTeacher($where);
            if ($data_percour) $this->view->coursenotassign = $data_percour;


            $doc = array();
            $tmp_doc = '';
            $c=1;
            foreach ($data_courses as $course) {
                //print_r($data_courses['uid'].'--');
                if ($tmp_doc <> $course['uid']) {
                    $doc[$c] = $course['uid'];
                    $tmp_doc = $course['uid'];
                    $c++;
                }
            }
            $this->view->teachers = $doc;

            //Docentes de la Escuela
            
            $teachersDb = new Api_Model_DbTable_Users();
            $where = array('eid'=>$eid, 'oid'=>$oid, 'escid'=>$escid, 'subid'=>$subid, 'rid'=>'DC', 'state'=>'A');
            $attrib = array('uid', 'pid');
            $teachers = $teachersDb->_getFilter($where, $attrib);

            $c = 0;
            $interruptor = 0;
            foreach ($teachers as $teacher) {
                foreach ($data_courses as $course) {
                    if ($teacher['uid'] == $course['uid']) {
                        $interruptor = 1;
                    }
                }
                if ($interruptor == 0) {
                    $teachersWcourses[$c]['uid'] = $teacher['uid'];
                    $teachersWcourses[$c]['pid'] = $teacher['pid'];
                    $c++;
                }
                $interruptor = 0;
            }

            $c = 0;
            foreach ($teachersWcourses as $teacher) {
                $where = array('eid'=>$eid, 'oid'=>$oid, 'escid'=>$escid, 'subid'=>$subid, 'uid'=>$teacher['uid'], 'pid'=>$teacher['pid']);
                $teacherInfo[$c] = $teachersDb->_getInfoUser($where);
                $c++;
            }
            $this->view->teacherUid = $teachersWcourses;
            $this->view->teacherwcInfo = $teacherInfo;

        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function printgeneralpreviewAction(){
        try {
            $this->_helper->layout()->disablelayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $escid = base64_decode($this->_getParam("escid"));
            $subid = base64_decode($this->_getParam("subid"));
            $perid = base64_decode($this->_getParam("perid"));
            $distid = base64_decode($this->_getParam("distid"));
            $this->view->eid = $eid;
            $this->view->oid = $oid;
            $this->view->perid = $perid;

            $where = array('eid' => $eid, 'oid' => $oid, 'escid' => $escid, 'subid' => $subid, 'perid' => $perid);
            $cour_tea = new Api_Model_DbTable_Coursexteacher();
            $data_courses = $cour_tea->_getFilter($where,$attrib=null,$orders=array('uid','curid','courseid','turno'));
            $this->view->courses = $data_courses;

            $per_cour = new Api_Model_DbTable_PeriodsCourses();
            $data_percour = $per_cour->_getCoursesIsNotTeacher($where);
            if ($data_percour) $this->view->coursenotassign = $data_percour;

            $doc = array();
            $tmp_doc = '';
            $c=1;
            foreach ($data_courses as $data_courses) {
                if ($tmp_doc <> $data_courses['uid']) {
                    $doc[$c] = $data_courses['uid'];
                    $tmp_doc = $data_courses['uid'];
                    $c++;
                }
            }
            $this->view->teachers = $doc;

            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
            $base_speciality =  new Api_Model_DbTable_Speciality();        
            $speciality = $base_speciality ->_getOne($where);
            $parent=$speciality['parent'];
            $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
            $parentesc= $base_speciality->_getOne($wher);

            if ($parentesc) {
                $pala='ESPECIALIDAD DE ';
                $spe['esc']=$parentesc['name'];
                $spe['parent']=$pala.$speciality['name'];
            }
            else{
                $spe['esc']=$speciality['name'];
                $spe['parent']='';  
            }
            $names=strtoupper($spe['esc']);
            $namep=strtoupper($spe['parent']);
            $namev=$names." ".$namep;
            $this->view->namev=$namev;
            $namefinal=$names." <br> ".$namep;

            if ($speciality['header']) {
                $namelogo = $speciality['header'];
            }
            else{
                $namelogo = 'blanco';
            }
            
            $fac = array('eid'=>$eid,'oid'=>$oid,'facid'=>$speciality['facid']);
            $base_fac =  new Api_Model_DbTable_Faculty();        
            $datafa= $base_fac->_getOne($fac);
            $namef = strtoupper($datafa['name']);  

            $dbimpression = new Api_Model_DbTable_Countimpressionall();
            
            $uid=$this->sesion->uid;
            $uidim=$this->sesion->pid;
            $pid=$uidim;

            $data = array(
                'eid'=>$eid,
                'oid'=>$oid,
                'uid'=>$uid,
                'escid'=>$escid,
                'subid'=>$subid,
                'pid'=>$pid,
                'type_impression'=>'vista_preliminar_general_docente_curso_'.$perid,
                'date_impression'=>date('Y-m-d H:i:s'),
                'pid_print'=>$uidim
                );
            // print_r($data);exit();
            $dbimpression->_save($data);            

            $wheri = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,
                'subid'=>$subid,'type_impression'=>'vista_preliminar_general_docente_curso_'.$perid);
            $dataim = $dbimpression->_getFilter($wheri);
                        
            $co=count($dataim);            
            $codigo=$co." - ".$uidim;

            $header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
            $header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", $namelogo, $header);
            $header = str_replace("?codigo", $codigo, $header);
          
            $this->view->header=$header;
            $this->view->footer=$footer;

        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}