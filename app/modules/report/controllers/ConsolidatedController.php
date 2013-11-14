<?php

class Report_ConsolidatedController extends Zend_Controller_Action {

    public function init()
    {
          $sesion  = Zend_Auth::getInstance();
          if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
          }
          $login = $sesion->getStorage()->read();
          $this->sesion = $login; 
    }

  public function indexAction()
    {
        try {
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $rid = $this->sesion->rid;
            $facid = $this->sesion->faculty->facid;
            $escid = $this->sesion->escid;
            $is_director = $this->sesion->infouser['teacher']['is_director'];
            if ($rid=="DC" && $is_director=="S"){
                $rid="DIREC";
                if ($facid=="2") $escid=substr($escid,0,3);
                $this->view->escid=$escid;        
            }
            if ($rid=="RF" || $rid=="DIREC") $this->view->facid=$facid;
            $where = array('eid' => $eid, 'oid' => $oid, 'state' => 'A');
            $fac= new Api_Model_DbTable_Faculty();
            $facultad=$fac->_getFilter($where,$attrib=null,$orders=null);
            $this->view->facultades=$facultad;
            $anio = date('Y');
            $this->view->anio = $anio;
            $this->view->rid = $rid;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function schoolsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $rid = $this->sesion->rid;
            $escid = $this->sesion->escid;
            $subid = $this->sesion->subid;
            $is_director = $this->sesion->infouser['teacher']['is_director'];
            $facid = $this->_getParam('facid');
            if ($rid=="DC" && $is_director=="S"){
                if ($facid=="2") $escid=substr($escid,0,3);
                $this->view->escid=$escid;
            }
            if ($facid=="TODO") 
                {
                    if($rid=='RF'){
                        $esp = new Api_Model_DbTable_Speciality();
                        $wheres = array('eid' => $eid, 'oid' => $oid, 'subid' => $subid);                      
                        $escu = $esp->_getFilter($wheres);
                        $this->view->escuelas=$escu;
                     }
                     else{
                         $this->view->facid=$facid;
                        }                
                }
            else{
                $where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid);
                $es = new Api_Model_DbTable_Speciality();
                $escu = $es->_getSchoolXFacultyNOTParent($where);
                $this->view->escuelas=$escu;
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function specialityAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $subid = $this->sesion->subid;
            $escid = $this->_getParam('escid');
            if ($escid=="TODOEC") {
                $this->view->escid=$escid;}
            else{
                $where = array('eid' => $eid, 'oid' => $oid, 'parent' => $escid);
                $es = new Api_Model_DbTable_Speciality();
                $especia = $es->_getFilter($where,$attrib=null,$orders=null);
                $this->view->especialidad=$especia;
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function periodsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $perid = $this->sesion->period->perid;
            $this->view->perid = $perid;
            $anio = $this->_getParam("anio");
            if ($eid=="" || $oid==""|| $anio=="") return false;
            $p = substr($anio, 2, 3);
            $p1=$p."A";
            $p2=$p."B";
            $where = array('eid' => $eid, 'oid' => $oid, 'p1' => $p1, 'p2' => $p2);
            $periodos = new Api_Model_DbTable_Periods();
            $this->view->lper = $periodos->_getPeriodsXAyB($where);
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    } 

    public function windowsAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $where['perid'] = $this->_getParam('perid');
        $where['escid'] = $this->_getParam('escid');
        $where['espec'] = $this->_getParam('espec');
        $where['facid'] = $this->_getParam('facid');
        $where['subid'] = $this->_getParam('subid');
        $this->view->subid=$where['subid'];
        $this->view->escid=$where['escid'];
        $this->view->facid=$where['facid'];
        $this->view->espec=$where['espec'];
        $this->view->perid=$where['perid'];
      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }
    public function registerxcourseAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $where['eid'] = $this->sesion->eid;
        $where['oid'] = $this->sesion->oid;
        $where['perid'] = $this->_getParam('perid');
        $where['escid'] = $this->_getParam('escid');
        $where['espec'] = $this->_getParam('espec');
        $where['facid'] = $this->_getParam('facid');
        $where['subid'] = $this->_getParam('subid');
        $where['tipo'] = $this->_getParam('tipo');
        $this->view->subid=$where['subid'];
        $this->view->escid=$where['escid'];
        $this->view->facid=$where['facid'];
        $this->view->espec=$where['espec'];
        $this->view->perid=$where['perid'];
        $this->view->tipo=$where['tipo'];

         // Obteniendo la facultad
        $escuela= new Api_Model_DbTable_Speciality();
        $dataescid=$escuela->_getFacspeciality($where);
        // print_r($dataescid);
        if ($dataescid) {
                $fac= new Api_Model_DbTable_Faculty();
                $datafacid=$fac->_getOne($where);
                $this->view->facultad =$datafacid['name'];               
           }
        //Obteniendo la escuela y especialidad(si lo tuviera)
        if ($dataescid['parent']==""){
        $this->view->escuela=strtoupper($dataescid[0]['nomesc']);
        }else{
                $dato['eid'] = $this->sesion->eid;    
                $dato['oid'] = $this->sesion->oid;
                $dato['escid'] = $dataescid['parent']; 
                $dato['subid'] = $dataescid['sub']; 
                $esc = $escuela->_getOne($dato);
                $this->view->escuela=strtoupper($esc['name']);
                $dataescid=$escuela->_getOne($where);
                $this->view->especialidad= strtoupper($dataescid['name']);
            }
        $data['eid']=$where['eid'];
        $data['oid']=$where['oid'];
        if ($where['espec']) {  $data['escid']=$where['espec'];  }
        else{ $data['escid']=$where['escid']; }
        $cur= new Api_Model_DbTable_Curricula();
        $lcur=$cur->_getFilter($data);
        $this->view->curriculas =$lcur; 
      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }
    
    public function coursesxcurriculaAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $where['eid'] = $this->sesion->eid;
        $where['oid'] = $this->sesion->oid;
        $where['perid'] = $this->_getParam('perid');
        $where['curid'] = $this->_getParam('curid');
        $escid = $this->_getParam('escid');
        $espec = $this->_getParam('espec');
        if ($espec) {  echo  $where['escid']=$espec;  }
        else{ $where['escid']=$escid; }
        $cur= new Api_Model_DbTable_PeriodsCourses();
        $lcur=$cur->_getFilter($where);
        $this->view->courses =$lcur; 

      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }
        public function studentregistrationAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $where['eid'] = $this->sesion->eid;
        $where['oid'] = $this->sesion->oid;
        $where['perid'] = $this->_getParam('perid');
        $where['curid'] = $this->_getParam('curid');
        $where['turno'] = $this->_getParam('turno');
        $where['courseid'] = $this->_getParam('courseid');
        $where['subid'] = $this->_getParam('subid');
        $where['tipo'] = $this->_getParam('tipo');
        $this->view->tipo =$where['tipo']; 


        $escid = $this->_getParam('escid');
        $espec = $this->_getParam('espec');
        if ($espec) {  $where['escid']=$espec;  }
        else{ $where['escid']=$escid; }
        $tcur= new Api_Model_DbTable_PeriodsCourses();
        $tipo=$tcur->_getOne($where);
        $this->view->tcur =$tipo['type_rate']; 
        $cur= new Api_Model_DbTable_Registrationxcourse();
        $lcur=$cur->_getStudentXcoursesXescidXperiods($where);
        // print_r($lcur);
        $this->view->data=$lcur;

      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }
    public function printregisterxcourseAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $where['eid'] = $this->sesion->eid;
        $where['oid'] = $this->sesion->oid;
        $where['perid'] = $this->_getParam('perid');
        $where['curid'] = $this->_getParam('curid');
        $where['turno'] = $this->_getParam('turno');
        $where['courseid'] = $this->_getParam('courseid');
        $where['subid'] = $this->_getParam('subid');
        $where['tipo'] = $this->_getParam('tipo');
        $this->view->tipo =$where['tipo']; 


        $escid = $this->_getParam('escid');
        $espec = $this->_getParam('espec');
        if ($espec) {  $where['escid']=$espec;  }
        else{ $where['escid']=$escid; }
         // Obteniendo la facultad
        $escuela= new Api_Model_DbTable_Speciality();
        $dataescid=$escuela->_getFacspeciality($where);
        // print_r($dataescid);
        if ($dataescid) {
         if ($dataescid) {
                $fac= new Api_Model_DbTable_Faculty();
                $datafacid=$fac->_getOne($where);
                $this->view->facultad =$datafacid['name'];               
           }
           }
        $this->view->escuela=strtoupper($esc['name']);
        $dataescid=$escuela->_getOne($where);
        $this->view->escuela =$dataescid['name']; 

        $cur= new Api_Model_DbTable_Registrationxcourse();
        $lcur=$cur->_getStudentXcoursesXescidXperiods($where);
        $this->view->data=$lcur;
        $course= new Api_Model_DbTable_Course();
        $lcourse=$course->_getOne($where);
        $this->view->courseid =$lcourse['name']; 
        $this->view->perid = $where['perid']; 
      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }

    public function registerxsemesterAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $where['eid'] = $this->sesion->eid;
        $where['oid'] = $this->sesion->oid;
        $where['perid'] = $this->_getParam('perid');
        $where['escid'] = $this->_getParam('escid');
        $where['espec'] = $this->_getParam('espec');
        $where['facid'] = $this->_getParam('facid');
        $where['subid'] = $this->_getParam('subid');
        $where['tipo'] = $this->_getParam('tipo');
        $this->view->subid=$where['subid'];
        $this->view->escid=$where['escid'];
        $this->view->facid=$where['facid'];
        $this->view->espec=$where['espec'];
        $this->view->perid=$where['perid'];
        $this->view->tipo=$where['tipo'];

         // Obteniendo la facultad
        $escuela= new Api_Model_DbTable_Speciality();
        $dataescid=$escuela->_getFacspeciality($where);
        // print_r($dataescid);
        if ($dataescid) {
                $fac= new Api_Model_DbTable_Faculty();
                $datafacid=$fac->_getOne($where);
                $this->view->facultad =$datafacid['name'];               
           }
        //Obteniendo la escuela y especialidad(si lo tuviera)
        if ($dataescid['parent']==""){
        $this->view->escuela=strtoupper($dataescid[0]['nomesc']);
        }else{
                $dato['eid'] = $this->sesion->eid;    
                $dato['oid'] = $this->sesion->oid;
                $dato['escid'] = $dataescid['parent']; 
                $dato['subid'] = $dataescid['sub']; 
                $esc = $escuela->_getOne($dato);
                $this->view->escuela=strtoupper($esc['name']);
                $dataescid=$escuela->_getOne($where);
                $this->view->especialidad= strtoupper($dataescid['name']);
            }

        if ($where['espec']) {  $where['escid']=$where['espec']; }
        
        $sem= new Api_Model_DbTable_Semester();
        $lsem=$sem->_getSemesterXPeriodsXEscid($where);
        $this->view->semester=$lsem; 

      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }
    public function studentregistrationxsemesterAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $where['eid'] = $this->sesion->eid;
        $where['oid'] = $this->sesion->oid;
        $where['perid'] = $this->_getParam('perid');
        $where['curid'] = $this->_getParam('curid');
        $where['turno'] = $this->_getParam('turno');
        $where['courseid'] = $this->_getParam('courseid');
        $where['subid'] = $this->_getParam('subid');
        $where['semid'] = $this->_getParam('semid');
        $where['tipo'] = $this->_getParam('tipo');
        $this->view->tipo =$where['tipo'];
        $this->view->semid =$where['semid']; 
        $escid = $this->_getParam('escid');
        $espec = $this->_getParam('espec');
        if ($espec) {  $where['escid']=$espec;  }
        else{ $where['escid']=$escid; }
        $sem= new Api_Model_DbTable_Registration();
        $lsem=$sem->_getStudentXespXsemester($where);
        $this->view->data=$lsem;

      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }

    public function printstudentregistrationxsemesterAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $where['eid'] = $this->sesion->eid;
        $where['oid'] = $this->sesion->oid;
        $where['perid'] = $this->_getParam('perid');
        $where['curid'] = $this->_getParam('curid');
        $where['turno'] = $this->_getParam('turno');
        $where['courseid'] = $this->_getParam('courseid');
        $where['subid'] = $this->_getParam('subid');
        $where['semid'] = $this->_getParam('semid');
        $where['tipo'] = $this->_getParam('tipo');
        $this->view->tipo =$where['tipo'];
        $this->view->semid =$where['semid']; 
        $escid = $this->_getParam('escid');
        $espec = $this->_getParam('espec');
        if ($espec) {  $where['escid']=$espec;  }
        else{ $where['escid']=$escid; }
         // Obteniendo la facultad
        $escuela= new Api_Model_DbTable_Speciality();
        $dataescid=$escuela->_getFacspeciality($where);
        // print_r($dataescid);
        if ($dataescid) {
          $fac= new Api_Model_DbTable_Faculty();
          $datafacid=$fac->_getOne($where);
          $this->view->facultad =$datafacid['name']; 
           }
         $dataescid=$escuela->_getOne($where);
        $this->view->escuela =$dataescid['name']; 
        $sem= new Api_Model_DbTable_Registration();
        $lsem=$sem->_getStudentXespXsemester($where);
        $this->view->data=$lsem;
        $this->view->perid=$where['perid'];


      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }


    public function registerxspecialityAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $where['eid'] = $this->sesion->eid;
        $where['oid'] = $this->sesion->oid;
        $where['perid'] = $this->_getParam('perid');
        $where['escid'] = $this->_getParam('escid');
        $where['espec'] = $this->_getParam('espec');
        $where['facid'] = $this->_getParam('facid');
        $where['subid'] = $this->_getParam('subid');
        $where['tipo'] = $this->_getParam('tipo');
        $this->view->subid=$where['subid'];
        $this->view->escid=$where['escid'];
        $this->view->facid=$where['facid'];
        $this->view->espec=$where['espec'];
        $this->view->perid=$where['perid'];
        $this->view->tipo=$where['tipo'];

         // Obteniendo la facultad
        $escuela= new Api_Model_DbTable_Speciality();
        $dataescid=$escuela->_getFacspeciality($where);
        // print_r($dataescid);
        if ($dataescid) {
          $fac= new Api_Model_DbTable_Faculty();
          $datafacid=$fac->_getOne($where);
          $this->view->facultad =$datafacid['name']; 
           }
        //Obteniendo la escuela y especialidad(si lo tuviera)
        if ($dataescid['parent']==""){
        $this->view->escuela=strtoupper($dataescid[0]['nomesc']);
        }else{
                $dato['eid'] = $this->sesion->eid;    
                $dato['oid'] = $this->sesion->oid;
                $dato['escid'] = $dataescid['parent']; 
                $dato['subid'] = $dataescid['sub']; 
                $esc = $escuela->_getOne($dato);
                $this->view->escuela=strtoupper($esc['name']);
                $dataescid=$escuela->_getOne($where);
                $this->view->especialidad= strtoupper($dataescid['name']);
            }
        if ($where['espec']) {  $where['escid']=$where['espec']; }
        $student= new Api_Model_DbTable_Registration();
        $lstudent=$student->_getStudentXspeciality($where);
        $this->view->data=$lstudent; 

      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }

        public function printregisterxspecialityAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $where['eid'] = $this->sesion->eid;
        $where['oid'] = $this->sesion->oid;
        $where['perid'] = $this->_getParam('perid');
        $where['escid'] = $this->_getParam('escid');
        $where['espec'] = $this->_getParam('espec');
        $where['facid'] = $this->_getParam('facid');
        $where['subid'] = $this->_getParam('subid');
        $where['tipo'] = $this->_getParam('tipo');
        $this->view->subid=$where['subid'];
        $this->view->escid=$where['escid'];
        $this->view->facid=$where['facid'];
        $this->view->espec=$where['espec'];
        $this->view->perid=$where['perid'];
        $this->view->tipo=$where['tipo'];

         // Obteniendo la facultad
        $escuela= new Api_Model_DbTable_Speciality();
        $dataescid=$escuela->_getFacspeciality($where);
        // print_r($dataescid);
        if ($dataescid) {
          $fac= new Api_Model_DbTable_Faculty();
          $datafacid=$fac->_getOne($where);
          $this->view->facultad =$datafacid['name']; 
           }
        //Obteniendo la escuela y especialidad(si lo tuviera)
        if ($dataescid['parent']==""){
        $this->view->escuela=strtoupper($dataescid[0]['nomesc']);
        }else{
                $dato['eid'] = $this->sesion->eid;    
                $dato['oid'] = $this->sesion->oid;
                $dato['escid'] = $dataescid['parent']; 
                $dato['subid'] = $dataescid['sub']; 
                $esc = $escuela->_getOne($dato);
                $this->view->escuela=strtoupper($esc['name']);
                $dataescid=$escuela->_getOne($where);
                $this->view->especialidad= strtoupper($dataescid['name']);
            }
        if ($where['espec']) {  $where['escid']=$where['espec']; }
        $student= new Api_Model_DbTable_Registration();
        $lstudent=$student->_getStudentXspeciality($where);
        $this->view->data=$lstudent; 

      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }

        public function countregisterxcourseAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $rid = $this->sesion->rid;
        $where['eid'] = $this->sesion->eid;
        $where['oid'] = $this->sesion->oid;
        $where['perid'] = $this->_getParam('perid');
        $where['escid'] = $this->_getParam('escid');
        $where['espec'] = $this->_getParam('espec');
        $where['facid'] = $this->_getParam('facid');
        $where['subid'] = $this->_getParam('subid');
        $where['tipo'] = $this->_getParam('tipo');
        $this->view->subid=$where['subid'];
        $this->view->escid=$where['escid'];
        $this->view->facid=$where['facid'];
        $this->view->espec=$where['espec'];
        $this->view->perid=$where['perid'];
        $this->view->tipo=$where['tipo'];

         // Obteniendo la facultad
        $escuela= new Api_Model_DbTable_Speciality();
        $dataescid=$escuela->_getFacspeciality($where);
        // print_r($dataescid);
        if ($dataescid) {
         $fac= new Api_Model_DbTable_Faculty();
         $datafacid=$fac->_getOne($where);
         $this->view->facultad =$datafacid['name']; 
           }
        //Obteniendo la escuela y especialidad(si lo tuviera)
        if ($dataescid['parent']==""){
        $this->view->escuela=strtoupper($dataescid[0]['nomesc']);
        }else{
                $dato['eid'] = $this->sesion->eid;    
                $dato['oid'] = $this->sesion->oid;
                $dato['escid'] = $dataescid['parent']; 
                $dato['subid'] = $dataescid['sub']; 
                $esc = $escuela->_getOne($dato);
                $this->view->escuela=strtoupper($esc['name']);
                $dataescid=$escuela->_getOne($where);
                $this->view->especialidad= strtoupper($dataescid['name']);
            }
        if ($where['espec']) {  $where['escid']=$where['espec']; }
        $sem= new Api_Model_DbTable_Semester();
        $lsem=$sem->_getSemesterXPeriodsXEscid($where);
        $this->view->semester=$lsem;

           $pc = new Api_Model_DbTable_PeriodsCourses();
              if ($rid=='RF' || $rid=='RC' || $rid=='VA')
             {               
             $listacursos = $pc->_getCountStudentxCourse($where);
             $this->view->listacursos=$listacursos;
            }
            if ($rid=='DC')
             {
            $where['subid'] = $this->sesion->subid;
            $this->view->listacursos = $pc->_getCountStudentxCourse($where);
            }

      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }

        public function printcountregisterxcourseAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $rid = $this->sesion->rid;
        $where['eid'] = $this->sesion->eid;
        $where['oid'] = $this->sesion->oid;
        $where['perid'] = $this->_getParam('perid');
        $where['escid'] = $this->_getParam('escid');
        $where['espec'] = $this->_getParam('espec');
        $where['facid'] = $this->_getParam('facid');
        $where['subid'] = $this->_getParam('subid');
        $where['tipo'] = $this->_getParam('tipo');
        $this->view->subid=$where['subid'];
        $this->view->escid=$where['escid'];
        $this->view->facid=$where['facid'];
        $this->view->espec=$where['espec'];
        $this->view->perid=$where['perid'];
        $this->view->tipo=$where['tipo'];

         // Obteniendo la facultad
        $escuela= new Api_Model_DbTable_Speciality();
        $dataescid=$escuela->_getFacspeciality($where);
        // print_r($dataescid);
        if ($dataescid) {
          $fac= new Api_Model_DbTable_Faculty();
          $datafacid=$fac->_getOne($where);
          $this->view->facultad =$datafacid['name']; 
           }
        //Obteniendo la escuela y especialidad(si lo tuviera)
        if ($dataescid['parent']==""){
        $this->view->escuela=strtoupper($dataescid[0]['nomesc']);
        }else{
                $dato['eid'] = $this->sesion->eid;    
                $dato['oid'] = $this->sesion->oid;
                $dato['escid'] = $dataescid['parent']; 
                $dato['subid'] = $dataescid['sub']; 
                $esc = $escuela->_getOne($dato);
                $this->view->escuela=strtoupper($esc['name']);
                $dataescid=$escuela->_getOne($where);
                $this->view->especialidad= strtoupper($dataescid['name']);
            }
        if ($where['espec']) {  $where['escid']=$where['espec']; }
        $sem= new Api_Model_DbTable_Semester();
        $lsem=$sem->_getSemesterXPeriodsXEscid($where);
        $this->view->semester=$lsem;

           $pc = new Api_Model_DbTable_PeriodsCourses();
              if ($rid=='RF' || $rid=='RC' || $rid=='VA')
             {               
             $listacursos = $pc->_getCountStudentxCourse($where);
             $this->view->listacursos=$listacursos;
            }
            if ($rid=='DC')
             {
            $where['subid'] = $this->sesion->subid;
            $this->view->listacursos = $pc->_getCountStudentxCourse($where);
            }

      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }

     public function totalturnosxspecialityAction(){
        try{
              $this->_helper->layout()->disableLayout();
              $perid= ($this->_getParam("perid"));            
              $curid = ($this->_getParam("curid"));
              $semid = ($this->_getParam("semid"));
              $escid = ($this->_getParam("escid"));
              $sedid = ($this->_getParam("sedid"));
              $sede = $this->sesion->sedid;
              $this->view->perid=$perid; 
              $this->view->curid=$curid;
              $this->view->semid=$semid; 
              $this->view->escid=$escid; 
              $this->view->sedid=$sedid;      
              $eid= $this->sesion->eid;
              $oid= $this->sesion->oid;
              $rid= $this->sesion->rid;
              $facid= $this->sesion->facid;
            if ($escid=="" || $perid=="") return false;
          //    $pc = new Admin_Model_DbTable_Periodoscursos();
          //     if ($rid=='RF' || $rid=='RC' || $rid=='VA' || $rid=='PD')
          //    {                   
          //    $this->view->listacursos = $pc->_getCantidaddeturnos($eid, $oid, $sedid, $escid,$perid);
          //   }
          //   if ($rid=='DC')
          //    {
          // $this->view->listacursos = $pc->_getCantidaddeturnos($eid, $oid, $sede, $escid,$perid);
          //   }
          //    $sem = new Admin_Model_DbTable_Semestre();
          //   $this->view->semestres = $sem->_getSemestreXPer($eid,$oid,$perid,$escid);
        }  catch (Exception $ex){
            print "Error: Cargar Cursos del Periodo Seleccionado";//.$ex->getMessage()
        }
    }
}