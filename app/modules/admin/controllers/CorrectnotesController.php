<?php
class Admin_CorrectnotesController extends Zend_Controller_Action{

	public function init(){
       	$sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
		$this->eid=$login->eid;
		$this->oid=$login->oid;
	}

	public function indexAction(){   
        $speciality= new Api_Model_DbTable_Speciality();
        $data= array("escid","subid","name");
        $where = array(
            'eid'=>$this->sesion->eid,
            'oid'=>$this->sesion->oid,
            'state'=>'A'
            );
        $rows = $speciality->_getFilter($where,$data='');
        $this->view->speciality = $rows;

    }
	public function listcoursesAction(){
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $perid = $this->_getParam('perid');
        $par_tmp = $this->_getParam('escid');
        $var_tmp = split("-", $par_tmp);
        $escid = $var_tmp[0];        
        $subid = $var_tmp[1];
        $curso = new Api_Model_DbTable_PeriodsCourses();

        $where= array("eid"=>$eid,"oid"=>$oid,"escid"=>$escid,"perid"=>$perid,'subid'=>$subid);
            $atrib = array('courseid','turno','semid','closure_date','curid','type_rate','state','subid');
            $order = array('courseid  ASC','turno asc','semid asc');
            $data= $curso->_getFilter($where,$atrib,$order);
            //print_r($data);
            foreach ($data as $key => $curs) {
                $where = array('eid'=>$eid,"oid"=>$oid,'courseid'=>$curs['courseid'],'escid'=>$escid,'subid'=>$subid);
                $attrib=array('name','courseid');
                $pack_couse=$curso ->_getinfoCourse($where,$attrib);
                $info_curso[$key]=$pack_couse[0];
                $info_curso[$key]['turno']=$curs['turno'];
                $info_curso[$key]['semid']=$curs['semid'];
                $info_curso[$key]['curid']=$curs['curid'];
                $info_curso[$key]['type_rate']=$curs['type_rate'];
                $info_curso[$key]['closure_date']=$curs['closure_date'];
                $info_curso[$key]['state']=$curs['state'];
                $info_curso[$key]['subid']=$curs['subid'];                
            }
        $this->view->registers=$info_curso;
        $this->view->perid=$perid;
        $this->view->escid=$escid;
        $this->_helper->layout()->disableLayout();

    }
    public function listperiodAction()
    {
        $anio_t = base64_decode($this->_getParam('anio'));
        $anio = substr($anio_t, 2, 4);
        $base_period = new Api_Model_DbTable_Periods();
        $where = array(
            'eid'=>$this->eid,
            'oid'=>$this->oid,
            'year'=>$anio
            );
        $data_periods = $base_period->_getPeriodsxYears($where);
        $this->view->periods=$data_periods;
        $this->_helper->layout()->disableLayout();
    }
}