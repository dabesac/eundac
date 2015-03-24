<?php

class Print_StudentController extends Zend_Controller_Action {

    public function init()
    {
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
    }

    public function indexAction(){
    	print_r('Impresiones');
    }

    public function preregisterAction() {
    	$eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $pid = $this->sesion->infouser['pid'];
        $uid = $this->sesion->uid;
        $escid = $this->sesion->escid;
        $subid = $this->sesion->subid;
        //$perid = $this->sesion->period->perid;
        $perid = '14A';

        $regid = $uid.$perid;
        $curid = $this->sesion->curid;

        $namef = strtoupper($this->sesion->faculty->name);
        $fullname = $this->sesion->infouser['fullname'];

        $this->view->fullname   = $fullname;
        $this->view->uid = $uid;

        $this->view->perid = $perid;

        $wheres=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
        $dbspeciality = new Api_Model_DbTable_Speciality();
        $speciality = $dbspeciality ->_getOne($wheres);
        $parent=$speciality['parent'];
        $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
        $parentesc= $dbspeciality->_getOne($wher);
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
        $namefinal=$names." <br> ".$namep;

        $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";

        $where = array(
            'eid'=>$eid,'oid'=>$oid,
            'escid'=>$escid,'subid'=>$subid,
            'pid'=>$pid,'uid'=>$uid,
            'regid'=>$regid,'perid'=>$perid,
            'curid'=>$curid,
            );
        $order = "courseid ASC";
        $base_registration_subjet = new Api_Model_DbTable_Registrationxcourse();
        $base_subjets_teacher = new Api_Model_DbTable_Coursexteacher();
        $base_subjets = new Api_Model_DbTable_Course();
        $base_person = new Api_Model_DbTable_Person();

        $data_subjects = $base_registration_subjet->_getAll($where,$order);
        $matricula = new Api_Model_DbTable_Registration();
        $wheremat = array(
                'eid'=>$eid,'oid'=>$oid,
                'escid'=>$escid,'subid'=>$subid,
                'pid'=>$pid,'uid'=>$uid,
                'regid'=>$regid,'perid'=>$perid,
        );
        $regmatr = $matricula->_getRegister($wheremat);

        if ($regmatr) $this->view->regmatr = $regmatr;

        // $attrib =array('pid','last_name0');

        foreach ($data_subjects as $key => $value) {
            $where = array(
                'eid'=>$eid,'oid'=>$oid,
                'curid'=>$value['curid'],
                'escid'=>$value['escid'],
                'subid'=>$value['subid'],
                'courseid'=>$value['courseid'],
                'turno' =>$value['turno'],
                'perid' => $perid,);

            $info_subjects =  $base_subjets->_getOne($where);
            $data_subjects [$key]['name'] = $info_subjects['name'];
            $data_subjects [$key]['type'] = $info_subjects['type'];
            $data_subjects  [$key]['credits'] = $info_subjects['credits'];
            $data_subjects [$key]['semid'] = $info_subjects['semid'];

            $data_pid_teacher = $base_subjets_teacher ->_getinfoDoc($where);

           $data_subjects [$key]['name_t']  = $data_pid_teacher[0]['nameteacher'];

        }
        $this->view->data_subjects  =   $data_subjects;

        $dbimpression = new Api_Model_DbTable_Countimpressionall();

        $uidim=$this->sesion->pid;

        $data = array(
            'eid'=>$eid,
            'oid'=>$oid,
            'uid'=>$uid,
            'escid'=>$escid,
            'subid'=>$subid,
            'pid'=>$pid,
            'type_impression'=>'prematricula_'.$perid,
            'date_impression'=>date('Y-m-d h:m:s'),
            'pid_print'=>$uidim
            );
        $dbimpression->_save($data);

        $wheri = array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'prematricula_'.$perid);
        $dataim = $dbimpression->_getFilter($wheri);

        $co=count($dataim);
        $codigo=$co." - ".$uidim;

        $header=$this->sesion->org['header_print'];
        $footer=$this->sesion->org['footer_print'];
        $header = str_replace("?facultad",$namef,$header);
        $header = str_replace("?escuela",$namefinal,$header);
        $header = str_replace("?logo", $namelogo, $header);
        $header = str_replace("?codigo", $codigo, $header);

        $this->view->codigo=$codigo;
        $this->view->header=$header;
        $this->view->footer=$footer;
        $this->_helper->layout->disableLayout();
    }
}