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
            $this->view->turno=$where['turno'];
            $this->view->escid=$where['escid'];
            $this->view->curid=$where['curid'];
            $this->view->courseid=$where['courseid'];
            $this->view->subid=$where['subid'];
            $this->view->perid=$where['perid'];
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

            $whereperi['eid']=$where['eid'];
            $whereperi['oid']=$where['oid'];
            $whereperi['perid']=$where['perid'];
            $peri= new Api_Model_DbTable_Periods();
            $periodo=$peri->_getOne($whereperi);
            $this->view->periodo=$periodo;

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

            $form= new Syllabus_Form_Syllabus();
            if ($periodocurso["type_rate"]=="C") $form->methodology->setRequired(true)->addErrorMessage('Rellene Metodologia');
            $form->populate($datsyl);
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

    public function unitsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $pk['eid'] = $this->sesion->eid;
            $pk['oid'] = $this->sesion->oid;
            $pk['subid'] = base64_decode($this->_getParam("subid"));
            $pk['perid'] = base64_decode($this->_getParam("perid"));
            $pk['curid'] = base64_decode($this->_getParam("curid"));
            $pk['escid'] = base64_decode($this->_getParam("escid"));
            $pk['courseid'] = base64_decode($this->_getParam("courseid"));
            $pk['turno'] = base64_decode($this->_getParam("turno"));
            $tipo_cali = base64_decode($this->_getParam("tipo_cali"));
            $formData['units'] = $this->_getParam("units");
            $numunidad = $this->_getParam("numunidad");
            $this->view->turno=$pk['turno'];
            $this->view->perid=$pk['perid'];
            $this->view->courseid=$pk['courseid'];
            $this->view->curid=$pk['curid'];
            $this->view->escid=$pk['escid'];
            $this->view->subid=$pk['subid'];
            $this->view->tipo_cali=$tipo_cali;
            $this->view->units=$formData['units'];
            $this->view->numunidad=$numunidad;

            $syllabus= new Api_Model_DbTable_Syllabus();
            $syllabus->_update($formData,$pk);

            $whereunit['eid']=$pk['eid'];
            $whereunit['oid']=$pk['oid'];
            $whereunit['perid']=$pk['perid'];
            $whereunit['escid']=$pk['escid'];
            $whereunit['subid']=$pk['subid'];
            $whereunit['courseid']=$pk['courseid'];
            $whereunit['curid']=$pk['curid'];
            $whereunit['turno']=$pk['turno'];
            $whereunit['unit']=$numunidad;
            $syllunits= new Api_Model_DbTable_Syllabusunits();
            $datuni=$syllunits->_getOne($whereunit);

            $whereunitcont['eid']=$pk['eid'];
            $whereunitcont['oid']=$pk['oid'];
            $whereunitcont['perid']=$pk['perid'];
            $whereunitcont['escid']=$pk['escid'];
            $whereunitcont['subid']=$pk['subid'];
            $whereunitcont['courseid']=$pk['courseid'];
            $whereunitcont['curid']=$pk['curid'];
            $whereunitcont['turno']=$pk['turno'];
            $whereunitcont['unit']=$numunidad;
            $syllunitscont= new Api_Model_DbTable_Syllabusunitcontent();
            $cont=$syllunitscont->_getAllXUnit($whereunitcont);
            $this->view->conte=$cont;

            $form= new Syllabus_Form_Syllabusunits();
            if ($tipo_cali=="O") $form->reading->setRequired(true)->addErrorMessage('Rellene lectura.');
            elseif ($tipo_cali=="C") $form->activity->setRequired(true)->addErrorMessage('Rellene actividad.');
            $band=0;
            $act=0;

            if ($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();
                if ($form->isValid($frmdata))
                { 
                    if ($datuni) {
                        unset($frmdata['guardar']);
                        trim($frmdata['name']);
                        trim($frmdata['objetive']);
                        trim($frmdata['reading']);
                        trim($frmdata['activity']);
                        // Creamos nuevo array para comparar con el original.
                        $data=array();
                        $data=$frmdata;
                        $data['eid']=$pk['eid'];
                        $data['oid']=$pk['oid'];
                        $data['perid']=$pk['perid'];
                        $data['curid']=$pk['curid'];
                        $data['escid']=$pk['escid'];
                        $data['courseid']=$pk['courseid'];
                        $data['subid']=$pk['subid'];
                        $data['turno']=$pk['turno'];
                        $data['unit']=$numunidad;
                        $result = array_diff($data,$datuni);
                        // print_r($result);
                        if ($result) {
                            $pkunit=array();
                            $pkunit['eid']=$pk['eid'];
                            $pkunit['oid']=$pk['oid'];
                            $pkunit['perid']=$pk['perid'];
                            $pkunit['curid']=$pk['curid'];
                            $pkunit['escid']=$pk['escid'];
                            $pkunit['courseid']=$pk['courseid'];
                            $pkunit['subid']=$pk['subid'];
                            $pkunit['turno']=$pk['turno'];
                            $pkunit['unit']=$numunidad;
                            if ($syllunits->_update($frmdata,$pkunit)) $act=1;
                        }
                        else $band=1;
                    }else{
                        unset($frmdata['guardar']);
                        trim($frmdata['name']);
                        trim($frmdata['objetive']);
                        trim($frmdata['reading']);
                        trim($frmdata['activity']);
                        $frmdata['eid']=$pk['eid'];
                        $frmdata['oid']=$pk['oid'];
                        $frmdata['perid']=$pk['perid'];
                        $frmdata['curid']=$pk['curid'];
                        $frmdata['escid']=$pk['escid'];
                        $frmdata['courseid']=$pk['courseid'];
                        $frmdata['subid']=$pk['subid'];
                        $frmdata['turno']=$pk['turno'];
                        $frmdata['unit']=$numunidad;
                        // print_r($frmdata);
                        $syllunits->_save($frmdata);
                        if ($syllunits) $band=1;
                    }

                }
                else echo "Ingrese nuevamente por favor";
            }
            if ($datuni || $act==1) {
                $datuni=$syllunits->_getOne($whereunit);
                $form->populate($datuni);
            }
            if ($band==1) {
                if ($numunidad<$formData['units']) {
                    $numunidad=$numunidad+1;
                    $url= "perid/".base64_encode($pk['perid'])."/subid/".base64_encode($pk['subid'])."/escid/".base64_encode($pk['escid'])."/curid/".base64_encode($pk['curid'])."/courseid/".base64_encode($pk['courseid'])."/turno/".base64_encode($pk['turno'])."/tipo_cali/".base64_encode($tipo_cali)."/units/".$formData['units']."/numunidad/".$numunidad;
                    $this->_redirect("/syllabus/syllabus/units/".$url);
                }
            }
            $this->view->form=$form;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function contentAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $pk['eid'] = $this->sesion->eid;
            $pk['oid'] = $this->sesion->oid;
            $pk['subid'] = base64_decode($this->_getParam("subid"));
            $pk['perid'] = base64_decode($this->_getParam("perid"));
            $pk['curid'] = base64_decode($this->_getParam("curid"));
            $pk['escid'] = base64_decode($this->_getParam("escid"));
            $pk['courseid'] = base64_decode($this->_getParam("courseid"));
            $pk['turno'] = base64_decode($this->_getParam("turno"));
            $tipo_cali = base64_decode($this->_getParam("tipo_cali"));
            $units = $this->_getParam("units");
            $numunidad = $this->_getParam("numunidad");
            $this->view->turno=$pk['turno'];
            $this->view->perid=$pk['perid'];
            $this->view->courseid=$pk['courseid'];
            $this->view->curid=$pk['curid'];
            $this->view->escid=$pk['escid'];
            $this->view->subid=$pk['subid'];
            $this->view->tipo_cali=$tipo_cali;
            $this->view->units=$units;
            $this->view->numunidad=$numunidad;

            $form= new Syllabus_Form_Syllabusunitcontent();
            $this->view->form=$form;

            if ($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();
                if ($form->isValid($frmdata))
                { 
                    trim($frmdata['week']);
                    trim($frmdata['session']);
                    trim($frmdata['obj_content']);
                    trim($frmdata['obj_strategy']);
                    trim($frmdata['com_conceptual']);
                    trim($frmdata['com_procedimental']);
                    trim($frmdata['com_actitudinal']);
                    trim($frmdata['com_indicators']);
                    trim($frmdata['com_instruments']);
                    $frmdata['eid']=$this->sesion->eid;
                    $frmdata['oid']=$this->sesion->oid;
                    // print_r($frmdata);

                    $where = array(
                        'eid' => $frmdata['eid'], 'oid' => $frmdata['oid'], 'subid' => $frmdata['subid'],
                        'perid' => $frmdata['perid'], 'escid' => $frmdata['escid'], 'curid' => $frmdata['curid'],
                        'courseid' => $frmdata['courseid'], 'turno' => $frmdata['turno'], 'session' => $frmdata['session']);
                    $sylconte = new Api_Model_DbTable_Syllabusunitcontent();
                    $data_exis = $sylconte->_getFilter($where,$attrib=null,$orders=null);
                    if ($data_exis) {
                        $this->view->exis = 1;
                    }else{
                        $sylconte->_save($frmdata);
                        $this->view->save_success = 1;
                    }
                }
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function modifycontentAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $where['eid'] = $this->sesion->eid;
            $where['oid'] = $this->sesion->oid;
            $where['subid'] = base64_decode($this->_getParam("subid"));
            $where['perid'] = base64_decode($this->_getParam("perid"));
            $where['curid'] = base64_decode($this->_getParam("curid"));
            $where['escid'] = base64_decode($this->_getParam("escid"));
            $where['courseid'] = base64_decode($this->_getParam("courseid"));
            $where['turno'] = base64_decode($this->_getParam("turno"));
            $where['unit'] = $this->_getParam("numunidad");
            $where['session'] = $this->_getParam("session");
            $tipo_cali = base64_decode($this->_getParam("tipo_cali"));
            $this->view->turno=$where['turno'];
            $this->view->perid=$where['perid'];
            $this->view->courseid=$where['courseid'];
            $this->view->curid=$where['curid'];
            $this->view->escid=$where['escid'];
            $this->view->subid=$where['subid'];
            $this->view->tipo_cali=$tipo_cali;
            $this->view->numunidad=$where['unit'];
            $this->view->session=$where['session'];

            $conten= new Api_Model_DbTable_Syllabusunitcontent();
            $datacont=$conten->_getOne($where);

            $form= new Syllabus_Form_Syllabusunitcontent();

            if ($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();
                if ($form->isValid($frmdata))
                { 
                    trim($frmdata['week']);
                    trim($frmdata['session']);
                    trim($frmdata['obj_content']);
                    trim($frmdata['obj_strategy']);
                    trim($frmdata['com_conceptual']);
                    trim($frmdata['com_procedimental']);
                    trim($frmdata['com_actitudinal']);
                    trim($frmdata['com_indicators']);
                    trim($frmdata['com_instruments']);
                    $pk['eid'] = $this->sesion->eid;
                    $pk['oid'] = $this->sesion->oid;
                    $pk['perid']=$frmdata['perid'];
                    $pk['curid']=$frmdata['curid'];
                    $pk['escid']=$frmdata['escid'];
                    $pk['courseid']=$frmdata['courseid'];
                    $pk['subid']=$frmdata['subid'];
                    $pk['turno']=$frmdata['turno'];
                    $pk['unit']=$frmdata['unit'];
                    $pk['session']=$frmdata['session'];
                    $sylconte = new Api_Model_DbTable_Syllabusunitcontent();
                    // print_r($frmdata);
                    if ($sylconte->_update($frmdata,$pk)) {
                        $this->view->success = 1;
                    }
                }
            }
            $form->populate($datacont);
            $this->view->form=$form;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function deletecontentAction(){
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
            $units = $this->_getParam("units");
            $numunidad = $this->_getParam("numunidad");
            $session = $this->_getParam("session");
            $data=array();
            $data['eid']=$eid;
            $data['oid']=$oid;
            $data['subid']=$subid;
            $data['perid']=$perid;
            $data['escid']=$escid;
            $data['curid']=$curid;
            $data['courseid']=$courseid;
            $data['turno']=$turno;
            $data['unit']=$numunidad;
            $data['session']=$session;
            $elim = new Api_Model_DbTable_Syllabusunitcontent();
            $elim->_delete($data);
            if ($elim) { 
                $url="perid/".base64_encode($perid)."/subid/".base64_encode($subid)."/escid/".base64_encode($escid)."/curid/".base64_encode($curid)."/courseid/".base64_encode($courseid)."/turno/".base64_encode($turno)."/tipo_cali/".base64_encode($tipo_cali)."/units/".$units."/numunidad/".$numunidad;
                $this->_redirect("/syllabus/syllabus/units/".$url);
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
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
}