<?php

class Docente_IndexController extends Zend_Controller_Action {

    public function init()
    {
      $sesion  = Zend_Auth::getInstance();
      if(!$sesion->hasIdentity() ){
        $this->_helper->redirector('index',"index",'default');
      }
      $login = $sesion->getStorage()->read();
      if (!$login->rol['module']=="docente"){
        $this->_helper->redirector('index','index','default');
      }
      $this->sesion = $login;   
    }
    
    public function indexAction()
    {
      try{
        $pid=$this->sesion->pid;
        $this->view->pid=$pid;
        }
        catch (Exception $e) {           
        }
    }
    public function subjectsAction()
    {
        try {

            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $escid = $this->sesion->escid;
            $subid = $this->sesion->subid;
            $perid = $this->sesion->period->perid;
            $perid_netx = $this->sesion->period->next;
            $uid = $this->sesion->uid;
            $pid = $this->sesion->infouser['pid'];           
            $this->view->perid_netx = $perid_netx;
            $where = array(
                'eid'=>$eid,'oid'=>$oid,
                'perid'=>$perid,'uid'=>$uid,
                'pid'=>$pid,);
            $base_course_x_teacher = new Api_Model_DbTable_Coursexteacher();
            $base_periods_courses = new Api_Model_DbTable_PeriodsCourses();
            $base_curses = new Api_Model_DbTable_Course();
            $base_especiality = new Api_Model_DbTable_Speciality();
            $base_faculty = new Api_Model_DbTable_Faculty();
            $subjects = $base_course_x_teacher ->_getFilter($where);           
            foreach ($subjects as $key => $subject) {
                $where = array(
                    'eid' => $eid,'oid' => $oid,
                    'escid' => $subject['escid'],
                    'subid' => $subject['subid'],
                    'curid' => $subject['curid'],
                    'courseid' => $subject['courseid'],
                    'turno' => $subject['turno'],
                    );
                $type_rate =    $base_periods_courses->_getOne($where);
                $subjects[$key]['type_rate'] = $info_subject['type_rate'];

                $info_subject = $base_curses->_getOne($where);
                $subjects[$key]['name'] = $info_subject['name'];

                $info_speciality = $base_especiality->_getOne($where);
                $where['facid'] = $info_speciality['facid'];

                $info_faculty = $base_faculty   ->_getOne($where);
                $subjects[$key]['faculty'] =$info_faculty['name'];
            }
            
            // print_r($subjects);
            $this->view->subjects=$subjects;

        } catch (Exception $e) {
            
        }
    }

   public function pollAction()
      {
        try{

         }
        catch(Exception $ex)
         {
          print $ex->getMessage();
         }  

     
      }

    public function lperiodsAction(){
      try{
         $this->_helper->layout()->disableLayout();
          $where['eid']= $this->sesion->eid;
          $where['oid']= $this->sesion->oid;
          $anio = $this->_getParam("anio");
             if ($where['eid']=="" || $where['oid']==""||$anio=="") return false;
             $p = substr($anio, 2, 3);
             $where['p1']=$p."A";
             $where['p2']=$p."B";
             $periodos = new Api_Model_DbTable_Periods();
             $lper = $periodos->_getPeriodsXAyB($where);
             $this->view->lper=$lper;  
        }
      catch(Exception $ex)
        {
          print $ex->getMessage();
        }  

    }
    public function generalAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $perid = $this->_getParam("perid");
        $this->view->perid=$perid;
      }
      catch(Exception $ex)
        {
          print $ex->getMessage();
        }  
         
    }

    public function detailsAction(){
      try{
          $this->_helper->layout()->disableLayout();        
           $where['eid'] = $this->sesion->eid;         
           $where['oid'] = $this->sesion->oid; 
           $where['escid'] = $this->sesion->escid;
           $where['perid'] = $this->_getParam("perid");
           $this->view->perid=$where['perid'];
           $where['subid'] = $this->sesion->subid;
           $where['uid'] = $this->sesion->uid;
           $where['pid'] = $this->sesion->pid;
           $dbcursos = new Api_Model_DbTable_Coursexteacher();
           $lcursos = $dbcursos->_getFilter($where);
           $this->view->cursos=$lcursos;     
          }

          catch(Exception $ex)
        {
              print $ex->getMessage();
        }  

      }

      public function xcoursesAction(){
        try{
          $this->_helper->layout()->disableLayout();          
           $where['eid'] = $this->sesion->eid;         
           $where['oid'] = $this->sesion->oid; 
           $where['escid'] = $this->sesion->escid;
           $where['perid'] = $this->_getParam("perid");
           $this->view->perid=$where['perid'];
           $where['subid'] = $this->sesion->subid;
           $where['uid'] = $this->sesion->uid;
           $where['pid'] = $this->sesion->pid;
           $dbcursos = new Api_Model_DbTable_Coursexteacher();
           $lcursos = $dbcursos->_getFilter($where);
           $this->view->cursos=$lcursos;      
           }
            catch(Exception $ex)
        {
              print $ex->getMessage();
        }  

    }

       public function graphicdetailAction()
      {
        try
        {
           $this->_helper->layout()->disableLayout();
           $where['eid'] = $this->sesion->eid;         
           $where['oid'] = $this->sesion->oid; 
           $where['escid'] = $this->sesion->escid;
           $where['subid'] = $this->sesion->subid;
           $uid = $this->sesion->uid;
           $pid = $this->sesion->pid;
           $where['perid'] = $this->_getParam("perid");
           $where['codigo'] = $this->_getParam("codigo");
           $where['courseid'] = $this->_getParam("courseid");
           $where['curid'] = $this->_getParam("curid");
           $turno = $this->_getParam("turno");
           $dcursos= new Api_Model_DbTable_Course();
           $nom=$dcursos->_getOne($where);
           $nombre=$nom['name'];
           $this->view->nom=$nombre;
           $this->view->turno=$turno;
           $this->view->escid=$where['escid'];
           $this->view->uid=$uid;
           $this->view->oid=$where['eid'];
           $this->view->eid=$where['oid'];
           $db_poll = new Api_Model_DbTable_Poll();
           $lpolldetails= $db_poll->_getPollDetail($where);  
            // print_r($lpolldetails);
            $this->view->cantidad=$lpolldetails; 
        }
        catch(Exception $ex)
        {
              print $ex->getMessage();
        }                               
    }

     public function graphicttotalAction()
    {
        
           $this->_helper->layout()->disableLayout();
           $where['eid'] = $this->sesion->eid;         
           $where['oid'] = $this->sesion->oid; 
           $where['escid'] = $this->sesion->escid;
           $where['subid'] = $this->sesion->subid;
           $uid = $this->sesion->uid;
           $pid = $this->sesion->pid;
           $where['perid'] = $this->_getParam("perid");
           $where['codigo'] = $this->_getParam("codigo");
           $where['courseid'] = $this->_getParam("courseid");
           $where['curid'] = $this->_getParam("curid");
           $turno = $this->_getParam("turno");
           $dcursos= new Api_Model_DbTable_Course();
           $nom=$dcursos->_getOne($where);
           $nombre=$nom['name'];
           $this->view->nom=$nombre;
           $this->view->turno=$turno;
           $this->view->escid=$where['escid'];
           $this->view->uid=$uid;
           $this->view->oid=$where['eid'];
           $this->view->eid=$where['oid'];
           $db_polltot = new Api_Model_DbTable_Poll();
           $lpolltot= $db_polltot->_getPollTotal($where);  
            // print_r($lpolltot);
            $this->view->cantidad=$lpolltot; 
                 
                
    }

        public function performanceAction(){
         
           $this->_helper->layout()->disableLayout();
           $where['eid'] = $this->sesion->eid;         
           $where['oid'] = $this->sesion->oid; 
           $where['escid'] = $this->sesion->escid;
           $where['perid'] = $this->_getParam("perid");
           $where['subid'] = $this->sesion->subid;
           $where['uid'] = $this->sesion->uid;
           $where['pid'] = $this->sesion->pid;
           $dbcursos = new Api_Model_DbTable_Coursexteacher();
           $lcursos = $dbcursos->_getFilter($where);
           $s1=0;
           $s2=0;
           $s3=0;
           $s4=0;
           foreach ($lcursos as $curso) {
                 $courseid=$curso['courseid'];
                 $curid=$curso['curid'];
                 $turno=$curso['turno'];
                 $where['codigo']="curid:".$curso['curid']."-cursoid:".$curso['courseid']."-turno:".$curso['turno'];
                 $db_polltot = new Api_Model_DbTable_Poll();
                 $lpolltot= $db_polltot->_getPollTotal($where);   
                foreach ($lpolltot   as $cantidad) {
                    $orden=$cantidad['position'];
                    if($orden=='1'){
                        $siempre=$cantidad['cantidad'];
                        $s1=$s1+$siempre;
                    }
                    if($orden=='2'){
                        $siempre=$cantidad['cantidad'];
                        $s2=$s2+$siempre;
                    }
                    if($orden=='3'){
                        $siempre=$cantidad['cantidad'];
                        $s3=$s3+$siempre;
                    }
                    if($orden=='4'){
                        $siempre=$cantidad['cantidad'];
                        $s4=$s4+$siempre;
                    }
                }
           }
           $this->view->s1=$s1;
           $this->view->s2=$s2;
           $this->view->s3=$s3;
           $this->view->s4=$s4;       
    }
    


}
