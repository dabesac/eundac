<?php
class Syllabus_SyllabusController extends Zend_Controller_Action {

    public function init(){
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
         $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        // if (!$login->modulo=="syllabus"){
        //  $this->_helper->redirector('index','index','default');
        // }
        $this->sesion = $login; 
    }

    public function indexAction(){
        try {

            $where['eid']=$this->sesion->eid;
            $where['oid']=$this->sesion->oid;
            $where['escid']=base64_decode($this->_getParam('escid'));
            $where['curid']=base64_decode($this->_getParam('curid'));
            $where['courseid']=base64_decode($this->_getParam('courseid'));
            $where['turno']=base64_decode($this->_getParam('turno'));
            $where['subid']=base64_decode($this->_getParam('subid'));
            $where['perid']=base64_decode($this->_getParam('perid'));

            $tb_period = new Api_Model_DbTable_Periods();
            $data_period =$tb_period->_getOne($where);

            $date_stard = $data_period['class_start_date'];
            $date_end = $data_period['class_end_date'];

            $data_stard = new Zend_Date($date_stard);
            $date_stard =$data_stard->get(Zend_Date::DATE_LONG);
            // echo $data_stard->get('dd/mm/yyyy');
            $data_end = new Zend_Date($date_end);
            $date_end = $data_end->toString(Zend_Date::DATE_LONG);

            $this->view->date_stard = $date_stard;
            $this->view->date_end = $date_end;


            $this->view->where=$where;
            $syl= new Api_Model_DbTable_Syllabus();
            $datsyl=$syl->_getOne($where);
            $this->view->num=$datsyl['number'];
            
            if ($datsyl['state']=='C') {
                $escid=base64_encode($where['escid']);
                $curid=base64_encode($where['curid']);
                $courseid=base64_encode($where['courseid']);
                $turno=base64_encode($where['turno']);
                $subid=base64_encode($where['subid']);
                $perid=base64_encode($where['perid']);
                $params = array('escid'=>$escid,'curid'=>$curid,'courseid'=>$courseid,'turno'=>$turno,'subid'=>$subid,'perid'=>$perid);
                $this->_helper->redirector('viewimpress','syllabus','syllabus', $params);
            }

            $datasum=array('eid'=>$where['eid'],'oid'=>$where['oid'],'curid'=>$where['curid'],
                'escid'=>$where['escid'],'subid'=>$where['subid'],'courseid'=>$where['courseid']);
            
            $querysum=new Api_Model_DbTable_Course();
            $this->view->sumgral=$querysum->_getOne($datasum);


            if (!$datsyl) {
                $data['eid']=$where['eid'];
                $data['oid']=$where['oid'];
                $data['escid']=$where['escid'];
                $data['curid']=$where['curid'];
                $data['courseid']=$where['courseid'];
                $data['turno']=$where['turno'];
                $data['subid']=$where['subid'];
                $data['perid']=$where['perid'];
                $data['number']=$where['perid'].$where['escid'].$where['courseid'].$where['turno'];
                $data['units']='4';
                $data['teach_uid']=$this->sesion->uid;
                $data['teach_pid']=$this->sesion->pid;
                $data['register']=$this->sesion->uid;
                $data['created']=date('Y-m-d');
                $data['state']='B';
                $syl->_save($data);
                $datsyl=$syl->_getOne($where);
            }
            // $this->view->syllabus=$datsyl;

            $facid=substr($where['escid'],0,1);
            $wherefac['eid']=$where['eid'];
            $wherefac['oid']=$where['oid'];
            $wherefac['facid']=$facid;
            $fac = new Api_Model_DbTable_Faculty();
            $facultad=$fac->_getOne($wherefac);
            $this->view->facultad=$facultad;

            $whereesc['eid']=$where['eid'];
            $whereesc['oid']=$where['oid'];
            $whereesc['escid']=$where['escid'];
            $whereesc['subid']=$where['subid'];
            $esc= new Api_Model_DbTable_Speciality();
            $escuelas=$esc->_getOne($whereesc);
            $this->view->escuelas=$escuelas;

            $wherecour['eid']=$where['eid'];
            $wherecour['oid']=$where['oid'];
            $wherecour['curid']=$where['curid'];
            $wherecour['escid']=$where['escid'];
            $wherecour['subid']=$where['subid'];
            $wherecour['courseid']=$where['courseid'];
            $cour= new Api_Model_DbTable_Course();
            $curso=$cour->_getOne($wherecour);
            $this->view->curso=$curso;

            $wheredoc['eid']=$where['eid'];
            $wheredoc['oid']=$where['oid'];
            $wheredoc['escid']=$where['escid'];
            $wheredoc['subid']=$where['subid'];
            $wheredoc['courseid']=$where['courseid'];
            $wheredoc['curid']=$where['curid'];
            $wheredoc['perid']=$where['perid'];
            $wheredoc['turno']=$where['turno'];
            $wheredoc['uid'] = $this->sesion->uid;
            $wheredoc['pid'] = $this->sesion->pid;
            $doc= new Api_Model_DbTable_Coursexteacher();
            $docente=$doc->_getOne($wheredoc);
            $this->view->docente=$docente;

            $whereper['eid']=$where['eid'];
            $whereper['pid']=$this->sesion->pid;
            $per= new Api_Model_DbTable_Person();
            $persona=$per->_getOne($whereper);
            $this->view->persona=$persona;

            $percur= new Api_Model_DbTable_PeriodsCourses();
            $periodocurso= $percur->_getOne($wheredoc);
            $this->view->periodocurso=$periodocurso;




        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function viewfrmAction(){
        try {

            $this->_helper->layout()->disableLayout();
            $where['eid']=$this->sesion->eid;
            $where['oid']=$this->sesion->oid;
            $where['escid']=base64_decode($this->_getParam('escid'));
            $where['curid']=base64_decode($this->_getParam('curid'));
            $where['courseid']=base64_decode($this->_getParam('courseid'));
            $where['turno']=base64_decode($this->_getParam('turno'));
            $where['subid']=base64_decode($this->_getParam('subid'));
            $where['perid']=base64_decode($this->_getParam('perid'));


        
            $this->view->where=$where;
            // $this->view->escid=$where['escid'];
            // $this->view->curid=$where['curid'];
            // $this->view->courseid=$where['courseid'];
            // $this->view->subid=$where['subid'];
            // $this->view->perid=$where['perid'];
            $syl= new Api_Model_DbTable_Syllabus();
            $datsyl=$syl->_getOne($where);
            $this->view->num=$datsyl['number'];

            $datasum=array('eid'=>$where['eid'],'oid'=>$where['oid'],'curid'=>$where['curid'],
                'escid'=>$where['escid'],'subid'=>$where['subid'],'courseid'=>$where['courseid']);
            
            $querysum=new Api_Model_DbTable_Course();
            $this->view->sumgral=$querysum->_getOne($datasum);


            if (!$datsyl) {
                $data['eid']=$where['eid'];
                $data['oid']=$where['oid'];
                $data['escid']=$where['escid'];
                $data['curid']=$where['curid'];
                $data['courseid']=$where['courseid'];
                $data['turno']=$where['turno'];
                $data['subid']=$where['subid'];
                $data['perid']=$where['perid'];
                $data['number']=$where['perid'].$where['escid'].$where['courseid'].$where['turno'];
                $data['units']='4';
                $data['teach_uid']=$this->sesion->uid;
                $data['teach_pid']=$this->sesion->pid;
                $data['register']=$this->sesion->uid;
                $data['created']=date('Y-m-d');
                $data['state']='B';
                $syl->_save($data);
                $datsyl=$syl->_getOne($where);
            }
            $this->view->syllabus=$datsyl;

            $wherepercur['eid']=$where['eid'];
            $wherepercur['oid']=$where['oid'];
            $wherepercur['courseid']=$where['courseid'];
            $wherepercur['escid']=$where['escid'];
            $wherepercur['perid']=$where['perid'];
            $wherepercur['subid']=$where['subid'];
            $wherepercur['curid']=$where['curid'];
            $wherepercur['turno']=$where['turno'];

            $percur= new Api_Model_DbTable_PeriodsCourses();
            $periodocurso= $percur->_getOne($wherepercur);
            $this->view->periodocurso=$periodocurso;
            // print_r($wherepercur);exit();

            $form= new Syllabus_Form_Syllabus();
            if ($periodocurso["type_rate"]=="C") $form->methodology->setRequired(true)->addErrorMessage('Rellene Metodologia');
            $form->populate($datsyl);

            if ($this->getRequest()->isPost())
            {
                $formData = $this->getRequest()->getPost();
                $pk['perid']=$where['perid'];
                $pk['curid']=$where['curid'];
                $pk['escid']=$where['escid'];
                $pk['courseid']=$where['courseid'];                   
                $pk['eid']=$where['eid'];
                $pk['oid']=$where['oid'];
                $pk['turno']=$where['turno'];
                $pk['subid']=$where['subid'];
                $syll= new Api_Model_DbTable_Syllabus();
                $state='C';
                $data=array('sumilla'=>$formData['sumilla'],'competency'=>$formData['competency'],'capacity'=>$formData['capacity'],
                            'units'=>$formData['units'],'media'=>$formData['media'],'sources'=>$formData['sources'],
                            'evaluation'=>$formData['evaluation']);
                if ($form->isValid($formData)) 
                    {   
                        $data['state']=$state;
                        if ($syll->_update($data,$pk)){ ?>
                            <script type="text/javascript">
                                alert("Se cerró el Silabo");
                                // window.location.reload();
                            </script>
                        <?php
                            $ban="1";
                            $this->view->ban=$ban;
                        }
                    }
                else{ 
                    // print_r($data);
                    // print_r($pk);exit();
                    $syll->_update($data,$pk);
                    $this->view->msgclose=1;
                }
            }
            $this->view->form=$form;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function savemodifiedAction(){
        try {
            if ($this->getRequest()->isPost())
            {
                $formData = $this->getRequest()->getPost();
                $pk = array(
                        'eid'=>$this->sesion->eid,
                        'oid'=>$this->sesion->oid,
                        'escid'=>base64_decode($formData['escid']),
                        'subid'=>base64_decode($formData['subid']),
                        'curid'=>base64_decode($formData['curid']),
                        'courseid'=>base64_decode($formData['courseid']),
                        'turno'=>base64_decode($formData['turno']),
                        'perid'=>base64_decode($formData['perid']),
                    );
                $data=array(
                    'sumilla'=>$formData['sumilla'],
                    'competency'=>$formData['competency'],
                    'capacity'=>$formData['capacity'],
                    'units'=>$formData['units'],
                    'media'=>$formData['media'],
                    'sources'=>$formData['sources'],
                    'evaluation'=>$formData['evaluation'],
                    'methodology'=>$formData['methodology'],
                    );
                $syll= new Api_Model_DbTable_Syllabus();
                if ($syll->_update($data,$pk)){
                    $json = array('status' => true);
                }else{
                    $json = array('status' => false);
                }
                
            }
        } catch (Exception $e) {
            $json  = array('status' => false
                        );
        }
        $this->_helper->layout->disableLayout();
        $this->_response->setHeader('Content-Type', 'application/json');                   
        $this->view->data = Zend_Json::encode($json); 
    }
    public function viewimpressAction(){
        try {
            $where['eid']=$this->sesion->eid;
            $where['oid']=$this->sesion->oid;
            $where['escid']=base64_decode($this->_getParam('escid'));
            $where['curid']=base64_decode($this->_getParam('curid'));
            $where['courseid']=base64_decode($this->_getParam('courseid'));
            $where['turno']=base64_decode($this->_getParam('turno'));
            $where['subid']=base64_decode($this->_getParam('subid'));
            $where['perid']=base64_decode($this->_getParam('perid'));
            $this->view->where=$where;
            // print_r($where);exit();
            $syl= new Api_Model_DbTable_Syllabus();
            $datsyl=$syl->_getOne($where);
            $this->view->num=$datsyl['number'];

            $facid=substr($where['escid'],0,1);
            $wherefac['eid']=$where['eid'];
            $wherefac['oid']=$where['oid'];
            $wherefac['facid']=$facid;
            $fac = new Api_Model_DbTable_Faculty();
            $facultad=$fac->_getOne($wherefac);
            $this->view->facultad=$facultad;

            $whereesc['eid']=$where['eid'];
            $whereesc['oid']=$where['oid'];
            $whereesc['escid']=$where['escid'];
            $whereesc['subid']=$where['subid'];
            $esc= new Api_Model_DbTable_Speciality();
            $escuelas=$esc->_getOne($whereesc);
            $this->view->escuelas=$escuelas;

            $wherecour['eid']=$where['eid'];
            $wherecour['oid']=$where['oid'];
            $wherecour['curid']=$where['curid'];
            $wherecour['escid']=$where['escid'];
            $wherecour['subid']=$where['subid'];
            $wherecour['courseid']=$where['courseid'];
            $cour= new Api_Model_DbTable_Course();
            $curso=$cour->_getOne($wherecour);
            $this->view->curso=$curso;

            $wheredoc['eid']=$where['eid'];
            $wheredoc['oid']=$where['oid'];
            $wheredoc['escid']=$where['escid'];
            $wheredoc['subid']=$where['subid'];
            $wheredoc['courseid']=$where['courseid'];
            $wheredoc['curid']=$where['curid'];
            $wheredoc['perid']=$where['perid'];
            $wheredoc['turno']=$where['turno'];
            $wheredoc['uid'] = $this->sesion->uid;
            $wheredoc['pid'] = $this->sesion->pid;
            $doc= new Api_Model_DbTable_Coursexteacher();
            $docente=$doc->_getOne($wheredoc);
            $this->view->docente=$docente;

            $whereper['eid']=$where['eid'];
            $whereper['pid']=$this->sesion->pid;
            $per= new Api_Model_DbTable_Person();
            $persona=$per->_getOne($whereper);
            $this->view->persona=$persona;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
    public function unitsAction(){
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $this->_helper->layout()->disableLayout();
            $form= new Syllabus_Form_Syllabusunits();
            $tb_units = new Api_Model_DbTable_Syllabusunits();
            $tb_syllabus = new Api_Model_DbTable_Syllabus();

            if ($this->getRequest()->isPost()) {
                    $data_form = $this->getRequest()->getPost();
                    $subid=base64_decode($data_form['subid']);
                    $escid=base64_decode($data_form['escid']);
                    $curid=base64_decode($data_form['curid']);
                    $courseid=base64_decode($data_form['courseid']);
                    $turno=base64_decode($data_form['turno']);
                    $perid=base64_decode($data_form['perid']);
                    $unit=base64_decode($data_form['unit']);
                    $type_rate=base64_decode($data_form['type_rate']);
                    $params= array(
                            'eid'=>$eid,
                            'oid'=>$oid,
                            'subid'=>$subid,
                            'escid'=>$escid,
                            'curid'=>$curid,
                            'courseid'=>$courseid,
                            'turno'=>$turno,
                            'perid'=>$perid,
                            'unit'=>$unit,
                            );
                    if ($form->isValid($data_form)) {
                        
                        $data_unit = $tb_units->_getOne($params);
                        if (!$data_unit) {
                            $params['name']=$data_form['name'];
                            $params['objetive']=$data_form['objetive'];
                            $params['activity']=$data_form['activity'];
                            $params['reading']=$data_form['reading'];
                            if ($tb_units->_save($params)) {
                                $json = array(
                                    'status'=>true
                                );
                            }
                        }else{
                            $data['name']=$data_form['name'];
                            $data['objetive']=$data_form['objetive'];
                            $data['activity']=$data_form['activity'];
                            $data['reading']=$data_form['reading'];
                            try {
                                if ($tb_units->_update($data,$params)) {
                                    $json = array(
                                        'status'=>true
                                    );
                                }
                            } catch (Exception $e) {
                                $json = array('status'=>false);
                            }
                        }
                        $this->_response->setHeader('Content-Type', 'application/json');
                        $this->view->json= Zend_Json::encode($json);                
                    }else{
                        $params['type_rate']=$type_rate;
                        $form->populate($data_form);
                        $data_syllabus = $tb_syllabus->_getOne($params);
                        $this->view->data_syllabus=$data_syllabus;
                        $this->view->params = $params;
                        $this->view->form=$form;
                    }
            }else{
                $params = $this->getRequest()->getParams();
                $paramsdecode = array();
                foreach ( $params as $key => $value ){
                    if($key!="module" && $key!="controller" && $key!="action"){
                        $paramsdecode[base64_decode($key)] = base64_decode($value);
                    }
                }

                $params = $paramsdecode;
                $subid= trim($params['subid']);
                $escid= trim($params['escid']);
                $curid = trim($params['curid']);
                $courseid= trim($params['courseid']);
                $turno= trim($params['turno']);
                $perid = trim($params['perid']);
                $unit= trim($params['unit']);
                $type_rate = trim($params['type_rate']);
                $data = array(
                        'eid'=>$eid,
                        'oid'=>$oid,
                        'subid'=>$subid,
                        'escid'=>$escid,
                        'curid'=>$curid,
                        'courseid'=>$courseid,
                        'turno'=>$turno,
                        'perid'=>$perid,
                        'unit'=>$unit,
                    );
                $data_syllabus = $tb_syllabus->_getOne($data);
                $data_unit = $tb_units->_getOne($data);
                $status = false;
                if ($data_unit) {
                    $form->populate($data_unit);
                    $status = true;
                }
                $this->view->status=$status;
                $this->view->data_syllabus=$data_syllabus;
                $this->view->params = $params;
                $this->view->form=$form;
            }

    }
    public function contentAction(){
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $form= new Syllabus_Form_Syllabusunitcontent();
        $this->view->form=$form;
        $params = $this->getRequest()->getParams();
        $paramsdecode = array();
        foreach ( $params as $key => $value ){
            if($key!="module" && $key!="controller" && $key!="action"){
                $paramsdecode[base64_decode($key)] = base64_decode($value);
            }
        }

        $params = $paramsdecode;
        $subid= trim($params['subid']);
        $escid= trim($params['escid']);
        $curid = trim($params['curid']);
        $courseid= trim($params['courseid']);
        $turno= trim($params['turno']);
        $perid = trim($params['perid']);
        $unit= trim($params['unit']);
        $type_rate = trim($params['type_rate']);
        $data = array(
                        'eid'=>$eid,
                        'oid'=>$oid,
                        'subid'=>$subid,
                        'escid'=>$escid,
                        'curid'=>$curid,
                        'courseid'=>$courseid,
                        'turno'=>$turno,
                        'perid'=>$perid,
                        'unit'=>$unit,
                    );
        $this->view->params=$params;
        $tb_units_content = new Api_Model_DbTable_Syllabusunitcontent();
        $data_content = $tb_units_content->_getAllXUnit($data);
        $this->view->data_content=$data_content;
        $this->_helper->layout()->disableLayout();
        
    }
    public function addcontentAction(){
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->isPost()) {
            $data= $this->getRequest()->getPost();
            $subid=base64_decode($data['subid']);
            $escid=base64_decode($data['escid']);
            $curid=base64_decode($data['curid']);
            $courseid=base64_decode($data['courseid']);
            $turno=base64_decode($data['turno']);
            $perid=base64_decode($data['perid']);
            $unit=base64_decode($data['unit']);
            $type_rate=base64_decode($data['type_rate']);

            $params= array(
                'eid'=>$eid,
                'oid'=>$oid,
                'subid'=>$subid,
                'escid'=>$escid,
                'curid'=>$curid,
                'courseid'=>$courseid,
                'turno'=>$turno,
                'perid'=>$perid,
                'unit'=>$unit,
                'week'=>$data['week'],
                'session'=>$data['session']
                );

            if ($type_rate == 'O') {
                $params['obj_content'] = $data['obj_content'];
                $params['obj_strategy'] = $data['obj_strategy'];
                $params['com_indicators'] = $data['com_indicators'];
                $params['com_instruments'] = $data['com_instruments'];
            }elseif ($type_rate == 'C') {
                $params['com_conceptual'] = $data['com_conceptual'];
                $params['com_procedimental'] = $data['com_procedimental'];
                $params['com_actitudinal'] = $data['com_actitudinal'];
                $params['com_indicators'] = $data['com_indicators'];
                $params['com_instruments'] = $data['com_instruments'];
            }
            try {
                $tb_units_content = new Api_Model_DbTable_Syllabusunitcontent();
                if ($tb_units_content->_save($params)) {
                        $json = array('status' => true, );
                    }
            } catch (Exception $e) {
                $json = array('status' => false, );
            }
        }

        $this->_response->setHeader('Content-Type', 'application/json');
        $this->view->json = Zend_Json::encode($json);
    }
    public function modifycontentAction(){
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->isPost()) {
                $data_form = $this->getRequest()->getPost();
                $type_rate = trim($data_form['type_rate']);
                $pk = array(
                        'eid'=>$eid,
                        'oid'=>$oid,
                        'subid'=>trim($data_form ['subid']),
                        'escid'=>trim($data_form ['escid']),
                        'curid'=>trim($data_form ['curid']),
                        'courseid'=>trim($data_form ['courseid']),
                        'turno'=>trim($data_form ['turno']),
                        'perid'=>trim($data_form ['perid']),
                        'unit'=>trim($data_form ['unit']),
                        'week'=>$data_form['week'],
                        'session'=>$data_form['session']
                    );

                 if ($type_rate == 'O') {
                    $data['obj_content'] = $data_form['obj_content'];
                    $data['obj_strategy'] = $data_form['obj_strategy'];
                    $data['com_indicators'] = $data_form['com_indicators'];
                    $data['com_instruments'] = $data_form['com_instruments'];
                }elseif ($type_rate == 'C') {
                    $data['com_conceptual'] = $data_form['com_conceptual'];
                    $data['com_procedimental'] = $data_form['com_procedimental'];
                    $data['com_actitudinal'] = $data_form['com_actitudinal'];
                    $data['com_indicators'] = $data_form['com_indicators'];
                    $data['com_instruments'] = $data_form['com_instruments'];
                }
                
                try {
                    $tb_units_content = new Api_Model_DbTable_Syllabusunitcontent();
                    if ($tb_units_content->_update($data,$pk)) {
                        $json = array('status'=>true);
                    }
                } catch (Exception $e) {
                    $json = array('status'=>false,);
                }
            } 
         $this->_response->setHeader('Content-Type', 'application/json');
        $this->view->json = Zend_Json::encode($json);
    }

    public function deletecontentAction(){
       
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $params = $this->getRequest()->getParams();
            $paramsdecode = array();
            foreach ( $params as $key => $value ){
                if($key!="module" && $key!="controller" && $key!="action"){
                    $paramsdecode[base64_decode($key)] = base64_decode($value);
                }
            }
            $params = $paramsdecode;
            $params['eid']=$eid;
            $params['oid']=$oid;
            try {
                $tb_units_content = new Api_Model_DbTable_Syllabusunitcontent();
                if ($tb_units_content->_delete($params)) {
                    $json = array('status'=>true);
                }
            } catch (Exception $e) {
                    $json = array('status'=>false);
                
            }
            $this->_response->setHeader('Content-Type','application/json');
            $this->view->json = Zend_Json::encode($json);

    }

    public function viewAction(){
        try {
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $courseid = base64_decode($this->_getParam("courseid"));
            $turno = base64_decode($this->_getParam("turno"));
            $curid = base64_decode($this->_getParam("curid"));
            $escid = base64_decode($this->_getParam("escid"));
            $subid = base64_decode($this->_getParam("subid"));
            $perid = base64_decode($this->_getParam("perid"));
            $this->view->subid=$subid;
            $this->view->perid=$perid;
            $this->view->escid=$escid;
            $this->view->curid=$curid;
            $this->view->courseid=$courseid;
            $this->view->pid=$this->sesion->pid;
            $this->view->turno=$turno;
            $this->view->infouser=$this->sesion->infouser;
            
            $whereper['eid']=$eid;
            $whereper['pid']=$this->sesion->pid;
            $per= new Api_Model_DbTable_Person();
            $persona=$per->_getOne($whereper);
            $this->view->persona=$persona;

            $wherecur['eid']=$eid;
            $wherecur['oid']=$oid;
            $wherecur['escid']=$escid;
            $wherecur['perid']=$perid;
            $wherecur['courseid']=$courseid;
            $wherecur['turno']=$turno;
            $wherecur['curid']=$curid;
            $percurso = new Api_Model_DbTable_PeriodsCourses();
            $datcurso = $percurso->_getInfocourseXescidXperidXcourseXturno($wherecur);
            $this->view->curso = $datcurso;
            
            $wheresc['eid']=$eid;
            $wheresc['oid']=$oid;
            $wheresc['escid']=$escid;
            $wheresc['subid']=$subid;
            $esc = new Api_Model_DbTable_Speciality();
            $escuela = $esc ->_getOne($wheresc);
            $this->view->escuela=$escuela;

            $wherefac['eid']=$eid;
            $wherefac['oid']=$oid;
            $wherefac['facid']=$escuela['facid'];
            $fac = new Api_Model_DbTable_Faculty();
            $facu = $fac ->_getOne($wherefac);
            $this->view->facu=$facu;
            
            $whereperi['eid']=$eid;
            $whereperi['oid']=$oid;
            $whereperi['perid']=$perid;
            $bdperiodo = new Api_Model_DbTable_Periods();
            $periods = $bdperiodo->_getOne($whereperi);
            $this->view->periods=$periods; 
            
            $wheresyl['eid']=$eid;
            $wheresyl['oid']=$oid;
            $wheresyl['subid']=$subid;
            $wheresyl['perid']=$perid;
            $wheresyl['escid']=$escid;
            $wheresyl['curid']=$curid;
            $wheresyl['courseid']=$courseid;
            $wheresyl['turno']=$turno;
            $dbsilabos = new Api_Model_DbTable_Syllabus();
            $silabo = $dbsilabos->_getOne($wheresyl);
            $this->view->silabo=$silabo;

            $syluni = new Api_Model_DbTable_Syllabusunits();
            $datsyluni=$syluni->_getAllXSyllabus($wheresyl);
            $this->view->datunidades=$datsyluni;

            $buscar=array('eid'=>$wheresyl['eid'],'oid'=>$wheresyl['oid'],'curid'=>$wheresyl['curid'],
                'escid'=>$wheresyl['escid'],'subid'=>$wheresyl['subid'],'courseid'=>$wheresyl['courseid']);
            $syl_sumg=new Api_Model_DbTable_Course();
            $this->view->sumgral=$syl_sumg->_getOne($buscar);
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
    public function savedefaultAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $subid = base64_decode($this->_getParam("subid"));
            $perid = base64_decode($this->_getParam("perid"));
            $curid = base64_decode($this->_getParam("curid"));
            $escid = base64_decode($this->_getParam("escid"));
            $courseid = base64_decode($this->_getParam("courseid"));
            $turno = base64_decode($this->_getParam("turno"));
            $tipo_cali = base64_decode($this->_getParam("tipo_cali"));
            $unit = base64_decode($this->_getParam("unit"));

            $bdconsult = new Api_Model_DbTable_Syllabusunitcontent();
            $where1=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'subid'=>$subid,'curid'=>$curid,'escid'=>$escid,
                          'courseid'=>$courseid,'turno'=>$turno);
            $where2=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'subid'=>$subid,'curid'=>$curid,'escid'=>$escid,
                          'courseid'=>$courseid,'turno'=>$turno);

            if ($tipo_cali=="O") {

            $unit2=2;
            $unit4=4;
            $name1 = "EXAMEN PRIMER PARCIAL";
            $name2 = "EXAMEN SEGUNDO PARCIAL";
            $name3 = "EXAMEN DE APLAZADOS";

            $week1 = 8;
            $week2 = 16;
            $week3 = 17;

                $where1['unit']=$unit2;
                $where2['unit']=$unit2;
                $where1['session']=15;
                $where2['session']=16;
                $data1=$bdconsult->_getOne($where1);
                $data2=$bdconsult->_getOne($where2);
                    if($data1=="") {
                        $where1['week']=$week1;
                        $where1['obj_content']=$name1;
                        $bdconsult->_save($where1);
                    }
                    if($data2=="") {
                        $where2['week']=$week1;
                        $where2['obj_content']=$name1;
                        $bdconsult->_save($where2);
                    }
                $where1['unit']=$unit4;
                $where2['unit']=$unit4;
                $where1['session']=31;
                $where2['session']=32;
                $data3=$bdconsult->_getOne($where1);
                $data4=$bdconsult->_getOne($where2);
                    if($data3=="") {
                        $where1['week']=$week2;
                        $where1['obj_content']=$name2;
                        $bdconsult->_save($where1);
                    }
                    if($data4=="") {
                        $where2['week']=$week2;
                        $where2['obj_content']=$name2;
                        $bdconsult->_save($where2);
                    }
                $where1['unit']=$unit4;
                $where2['unit']=$unit4;
                $where1['session']=33;
                $where2['session']=34;
                $data5=$bdconsult->_getOne($where1);
                $data6=$bdconsult->_getOne($where2);
                    if($data5=="") {
                        $where1['week']=$week3;
                        $where1['obj_content']=$name3;
                        $bdconsult->_save($where1);
                    }
                    if($data6=="") {
                        $where2['week']=$week3;
                        $where2['obj_content']=$name3;
                        $bdconsult->_save($where2);
                    }             
            }
            if ($tipo_cali=="C") {
                $unit1=1;
                $unit2=2;
                $unit3=3;
                $unit4=4;

                $name1 = "I EVALUACIÓN";
                $name2 = "II EVALUACIÓN";
                $name3 = "III EVALUACIÓN";
                $name4 = "IV EVALUACIÓN";
                $name5 = "EXAMEN DE APLAZADOS";

                $week1 = 4;
                $week2 = 8;
                $week3 = 12;
                $week4 = 16;
                $week5 = 17;


                $where1['unit']=$unit1;
                $where2['unit']=$unit2;
                $where1['session']=4;
                $where2['session']=8;
                $data1=$bdconsult->_getOne($where1);
                $data2=$bdconsult->_getOne($where2);
                    if($data1=="") {
                        $where1['week']=$week1;
                        $where1['com_conceptual']=$name1;
                        $bdconsult->_save($where1);
                    }
                    if($data2=="") {
                        $where2['week']=$week2;
                        $where2['com_conceptual']=$name2;
                        $bdconsult->_save($where2);
                    }
                $where1['unit']=$unit3;
                $where2['unit']=$unit4;
                $where1['session']=12;
                $where2['session']=16;
                $data3=$bdconsult->_getOne($where1);
                $data4=$bdconsult->_getOne($where2);
                    if($data3=="") {
                        $where1['week']=$week3;
                        $where1['com_conceptual']=$name3;
                        $bdconsult->_save($where1);
                    }
                    if($data4=="") {
                        $where2['week']=$week4;
                        $where2['com_conceptual']=$name4;
                        $bdconsult->_save($where2);
                    }
                $where1['unit']=$unit4;
                // $where2['unit']=$unit4;
                $where1['session']=17;
                // $where2['session']=34;
                $data5=$bdconsult->_getOne($where1);
                // $data6=$bdconsult->_getOne($where2);
                    if($data5=="") {
                        $where1['week']=$week5;
                        $where1['com_conceptual']=$name5;
                        $bdconsult->_save($where1);
                    }
                    // if($data6=="") {
                    //     $where2['week']=$week3;
                    //     $where2['com_conceptual']=$name3;
                    //     $bdconsult->_save($where2);
                    // }     
            }  
            
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}