<?php
class Docente_SyllabusController extends Zend_Controller_Action {

    public function init(){
    }

    public function indexAction(){
        try {
            $where['eid']="20154605046";
            $where['oid']="1";
            $where['escid']="4SI";
            $where['curid']="94A4SI";
            $where['courseid']="94501";
            $where['turno']="A";
            $where['subid']="1901";
            $where['perid']="13A";
            $this->view->turno=$where['turno'];
            $syl= new Api_Model_DbTable_Syllabus();
            $datsyl=$syl->_getOne($where);
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
                $data['register']='41951064DC';
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
            $wheredoc['uid']='41951064DC';
            $wheredoc['pid']='41951064';
            $doc= new Api_Model_DbTable_Coursexteacher();
            $docente=$doc->_getOne($wheredoc);
            $this->view->docente=$docente;

            $whereper['eid']=$where['eid'];
            $whereper['pid']='41951064';
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

            $form= new Docente_Form_Syllabus();
            if ($periodocurso["type_rate"]=="C") $form->methodology->setRequired(true)->addErrorMessage('Rellene Metodologia');

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
                if ($formData['save']!="") {
                    unset($formData['save']);
                    if ($syll->_update($formData,$pk)){
                        $datsyl=$syl->_getOne($where);
                    }
                }elseif ($formData['close']!="") {
                    $formData['save']='Guardar Avance';
                    if ($form->isValid($formData)) 
                    {
                        $data['state']='C';
                        if ($syll->_update($data,$pk)){ ?>
                            <script type="text/javascript">
                                alert("Se cerró el Silabo");
                                window.location.reload();
                            </script>
                        <?php
                        }
                    }
                    else $this->view->msgclose=1;
                }
            }
            $form->populate($datsyl);
            $this->view->form=$form;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function unitsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $pk['eid'] = "20154605046";
            $pk['oid'] = "1";
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

            $form= new Docente_Form_Syllabusunits();
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
                    $this->_redirect("/docente/syllabus/units/".$url);
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
            $pk['eid'] = "20154605046";
            $pk['oid'] = "1";
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

            $form= new Docente_Form_Syllabusunitcontent();
            $this->view->form=$form;

            if ($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();
                if ($form->isValid($frmdata))
                { 
                    unset($frmdata['guardar']);
                    trim($frmdata['week']);
                    trim($frmdata['session']);
                    trim($frmdata['obj_content']);
                    trim($frmdata['obj_strategy']);
                    trim($frmdata['com_conceptual']);
                    trim($frmdata['com_procedimental']);
                    trim($frmdata['com_actitudinal']);
                    trim($frmdata['com_indicators']);
                    trim($frmdata['com_instruments']);
                    $frmdata['eid']=$pk['eid'];
                    $frmdata['oid']=$pk['oid'];
                    $frmdata['perid']=$pk['perid'];
                    $frmdata['curid']=$pk['curid'];
                    $frmdata['escid']=$pk['escid'];
                    $frmdata['courseid']=$pk['courseid'];
                    $frmdata['sedid']=$pk['subid'];
                    $frmdata['turno']=$pk['turno'];
                    $frmdata['unit']=$numunidad;
                    $sylconte = new Docente_Model_DbTable_Sylabusunidadcontenidos();
                    // print_r($frmdata);
                    $sylconte->_guardar($frmdata);
                    if ($sylconte) {
                        $mod=1;
                    }
                }
            }
            if ($mod==1) {
                $url="perid/".base64_encode($perid)."/escid/".base64_encode($escid)."/curid/".base64_encode($curid)."/cursoid/".base64_encode($cursoid)."/turno/".base64_encode($turno)."/tipo_cali/".base64_encode($tipo_cali)."/unidad/".$unidad."/numunidad/".$numunidad;
                $this->_redirect("/docente/sylabus/unidades/".$url);                
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }
}