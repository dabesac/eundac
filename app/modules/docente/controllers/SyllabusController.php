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
            print_r($this->view->periodocurso=$periodocurso);
        } catch (Exception $e) {
            
        }
    }
}