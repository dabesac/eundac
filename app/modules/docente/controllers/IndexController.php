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
    
    public function indexAction(){
    try{
        //DataBases
        $periodsxCourseDb              = new Api_Model_DbTable_PeriodsCourses();
        $coursesxTeacherDb             = new Api_Model_DbTable_Coursexteacher();
        $courseDb                      = new Api_Model_DbTable_Course();
        $syllabusDb                    = new Api_Model_DbTable_Syllabus();
        $syllabusUnitsDb               = new Api_Model_DbTable_Syllabusunits();
        $syllabusUnitsContentDb        = new Api_Model_DbTable_Syllabusunitcontent();
        $syllabusUnitsContentControlDb = new Api_Model_DbTable_ControlActivity();
        $registerxCourseDB             = new Api_Model_DbTable_Registrationxcourse();
        $specialityDB                  = new Api_Model_DbTable_Speciality();
        //___________________________________________________________

        $eid   = $this->sesion->eid;
        $oid   = $this->sesion->oid;
        $pid   = $this->sesion->pid;
        $uid   = $this->sesion->uid;
        $rid   = $this->sesion->rid;
        $subid = $this->sesion->subid;
        $escid = $this->sesion->escid;
        $perid = $this->sesion->period->perid;

        //Bloquear Ingenieria
        $facultyBloqued = $escid[0];
        if ($facultyBloqued == 4) {
            $this->view->facultyBloqued = $facultyBloqued;
        }

        //Enviar el periodo
        $this->view->perid = $perid;

        $where = array( 'eid'   => $eid,
                        'oid'   => $oid,
                        'uid'   => $uid,
                        'pid'   => $pid,
                        'perid' => $perid,
                        'is_main' => 'S' );

        $attrib = array('courseid', 'curid', 'turno', 'escid', 'subid', 'perid');
        $courses = $coursesxTeacherDb->_getFilter($where, $attrib);
        $c = 0;
        foreach ($courses as $course) {
            //Nombre de Cursos
            $where = array('eid'      => $eid, 
                           'oid'      => $oid,
                           'curid'    => $course['curid'],
                           'courseid' => $course['courseid'] );
            $attrib = array('name', 'type');
            $coursesName[$c] = $courseDb->_getFilter($where, $attrib);

            //Escuela a la que Pertence el curso
            $where = array( 'eid' => $eid,
                            'oid' => $oid,
                            'escid' => $course['escid'],
                            'subid' => $course['subid'] );
            $attrib = array('name');
            $coursesSpecialityName[$c] = $specialityDB->_getFilter($where, $attrib);

            //Estado de Acta
            $where = array( 'eid'      => $eid, 
                            'oid'      => $oid,
                            'curid'    => $course['curid'],
                            'courseid' => $course['courseid'],
                            'turno'    => $course['turno'],
                            'escid'    => $course['escid'],
                            'subid'    => $course['subid'],
                            'perid'    => $perid );

            $attrib = array('state_record');
            $stateRecord = $periodsxCourseDb->_getFilter($where, $attrib);
            $coursesStateRecord[$c] = $stateRecord[0]['state_record'];

            //Syllabus
            $attrib = array('units');
            $coursesSyllabus = $syllabusDb->_getFilter($where, $attrib);
            $totalUnits = $coursesSyllabus[0]['units'];
            if ($totalUnits) {
                $attrib = array('unit');
                $syllabusUnits = $syllabusUnitsDb->_getFilter($where, $attrib);
                if ($syllabusUnits) {
                    $units = count($syllabusUnits);
                    $porcentajeSyllabus[$c] = (100 * $units)/$totalUnits;
                }else{
                    $porcentajeSyllabus[$c] = 0;
                }
            }else{
                $porcentajeSyllabus[$c] = 0;
            }

            //Avance de Clases
            if ($porcentajeSyllabus[$c] == 100) {
                $attrib = array('session');
                $contentsSyllabus = $syllabusUnitsContentDb->_getFilter($where, $attrib);
                $totalContents = count($contentsSyllabus);
                $contentControl = $syllabusUnitsContentControlDb->_getFilter($where, $attrib);
                if ($contentControl) {
                    $contents = count($contentControl);
                }else{
                    $contents = 0;
                }
                $progressSessions[$c]['totalContents'] = $totalContents;
                $progressSessions[$c]['contents'] = $contents;
                $progressSessions[$c]['porcentaje'] = intval((100 * $contents)/$totalContents);
                $progressSessions[$c]['porcentaje'] = $progressSessions[$c]['porcentaje']. '%';
            }else{
               $progressSessions[$c]['porcentaje'] = 'FS';
            }

            //Avance de Notas
            if ($porcentajeSyllabus[$c] == 100) {
                $where = array( 'eid'      => $eid,
                                'oid'      => $oid, 
                                'courseid' => $course['courseid'],
                                'curid'    => $course['curid'],
                                'escid'    => $course['escid'],
                                'subid'    => $course['subid'],
                                'turno'    => $course['turno'],
                                'perid'    => $perid,
                                'state'    => 'M' );
                if ($coursesName[$c][0]['type'] == 'O') {
                    $attrib = array('uid', 'promedio1');
                    $allStudents = $registerxCourseDB->_getFilter($where, $attrib);
                    $students = count($allStudents);
                    $notasRellenadas = 0;
                    if ($allStudents) {
                        foreach ($allStudents as $student) {
                            if ($student['promedio1']) {
                                $notasRellenadas++; 
                            }
                        }
                    }
                    $progressNotas[$c]['totalStudents'] = $students;
                    $progressNotas[$c]['notasRellenadas'] = $notasRellenadas;
                    $progressNotas[$c]['porcentaje'] = intval((100 * $notasRellenadas) / $students);
                }
            }else{
                $progressNotas[$c]['porcentaje'] = 'FS';
            }
            
            $c++;
        }//Final del Foreach Principal

        $this->view->courses               = $courses;
        $this->view->coursesName           = $coursesName;
        $this->view->coursesSpecialityName = $coursesSpecialityName;

        //Enviando Porcentaje de Syllabus
        $this->view->porcentajeSyllabus = $porcentajeSyllabus;

        //Enviando Porcentaje de Sesiones
        $this->view->progressSessions = $progressSessions;

        //Enviando Porcentaje de Llenado de Notas
        $this->view->progressNotas = $progressNotas;

        //Slider de Noticias // DataBases
        $newsDb = new Api_Model_DbTable_News();
        $newsRolDb = new Api_Model_DbTable_NewsRol();
        $news = $newsDb->_getLastNews();


        if ($rid == 'DR') {
            $rol = 'DC';
        }else{
            $rol = $rid;
        }

        $c = 0;
        foreach ($news as $new) {
            $where = array( 'eid'   => $eid,
                            'oid'   => $oid,
                            'newid' => $new['newid'] );

            $attrib = array('newid', 'rid');
            $newsRol = $newsRolDb->_getFilter($where, $attrib);
            $existe = 'Si';
            if ($newsRol) {
                if ($newsRol[0]['rid'] == $rol) {
                    $newsFilter[$c]['newid']       = $new['newid'];
                    $newsFilter[$c]['title']       = $new['title'];
                    $newsFilter[$c]['description'] = $new['description'];
                    $newsFilter[$c]['img']         = $new['img'];
                    $newsFilter[$c]['type']        = $new['type'];
                    $newsFilter[$c]['created']     = $new['created'];
                    $c++;
                }
            }else{
                $newsFilter[$c]['newid']       = $new['newid'];
                $newsFilter[$c]['title']       = $new['title'];
                $newsFilter[$c]['description'] = $new['description'];
                $newsFilter[$c]['img']         = $new['img'];
                $newsFilter[$c]['type']        = $new['type'];
                $newsFilter[$c]['created']     = $new['created'];
                $c++;
            }
            if ($c == 4) {
                break;
            }
        }  
        $this->view->news = $newsFilter;

        //Avacen Academico Solo Para Directores
        $this->view->rid = $rid;
        if ($rid == 'DR') {
            //DataBases
            $personDb = new Api_Model_DbTable_Person();
            $reportAcademicDb = new Api_Model_DbTable_Addreportacadadm();
            //____________________________________________________________________

            $this->view->rid = $rid;

            $where = array( 'eid'            => $eid,
                            'oid'            => $oid,
                            'subid'          => $subid,
                            'left(escid, 3)' => $escid,
                            'perid'          => $perid,
                            'is_main'        => 'S' );

            $attrib = array('uid', 'pid', 'escid', 'subid', 'courseid', 'curid', 'turno');
            $orders = array('uid');

            $courses = $coursesxTeacherDb->_getFilter($where, $attrib, $orders);
            //print_r($teachers);

            $teacherUid         = 0;
            $c                  = 0;
            $coursesEmpty       = 0;
            $totalTeachers      = 0;
            $totalEmptySyllabus = 0;
            $countERA           = 0;
            $teachersEmptySyllabus[0]['uid'] = '';
            $teachersEmptySyllabus[0]['name'] = '';
            //Total de Profesores
            foreach ($courses as $course) {
                if ($course['uid'] != $teacherUid) {
                    $teacherUid = $course['uid'];

                    $where = array( 'eid' => $eid,
                                    'oid' => $oid,
                                    'subid' => $course['subid'],
                                    'escid' => $course['escid'],
                                    'uid' => $course['uid'],
                                    'pid' => $course['pid'],
                                    'perid' => $perid );

                    $attrib = array('state');
                   
                    //Reporte Academico
                    $reportAcademic = $reportAcademicDb->_getFilter($where, $attrib);
                    if (!$reportAcademic) {
                        $where = array( 'eid' => $eid,
                                        'pid' => $course['pid'] );

                        $person = $personDb->_getFilter($where);
                        $teachersEmptyReport[$countERA]['name'] = $person[0]['last_name0'].' '.$person[0]['last_name1'].' '.$person[0]['first_name'];
                        $countERA++;
                    }
                    $totalTeachers++;
                }
                $where = array( 'eid'      => $eid,
                                'oid'      => $oid,
                                'perid'    => $perid,
                                'escid'    => $course['escid'],
                                'subid'    => $course['subid'],
                                'courseid' => $course['courseid'],
                                'curid'    => $course['curid'],
                                'turno'    => $course['turno'] );

                //Syllabus
                $syllabus = $syllabusUnitsContentDb->_getFilter($where);
                if (!$syllabus) {
                    $existe = 'No';
                    foreach ($teachersEmptySyllabus as $teacher) {
                        if ($course['uid'] == $teacher['uid']) {
                            $existe = 'Si';
                        }
                    }
                    if ($existe == 'No') {
                        $where = array( 'eid' => $eid,
                                        'pid' => $course['pid'] );

                        $person = $personDb->_getFilter($where);
                        $teachersEmptySyllabus[$c]['uid'] = $course['uid'];
                        $teachersEmptySyllabus[$c]['name'] = $person[0]['last_name0'].' '.$person[0]['last_name1'].' '.$person[0]['first_name'];

                        $totalEmptySyllabus++;
                        $c++;
                    }
                    $where = array( 'eid' => $eid,
                                    'oid' => $oid,
                                    'courseid' => $course['courseid'],
                                    'curid' => $course['curid'] );

                    $attrib = array('name');
                    $courseName = $courseDb->_getFilter($where);
                    $coursesEmptySyllabus[$course['uid']][$coursesEmpty]['name']  = $courseName[0]['name'];
                    $coursesEmptySyllabus[$course['uid']][$coursesEmpty]['turno'] = $course['turno'];
                    $coursesEmpty++;
                }else{
                    $coursesEmpty = 0;
                }

                

            }

            $this->view->totalTeachers      = $totalTeachers;
            //Enviando Datos de Silabos
            $this->view->emptySyllabus      = $totalEmptySyllabus;
            $this->view->totalEmptySyllabus = $totalTeachers - $totalEmptySyllabus;

            $this->view->teachersEmptySyllabus = $teachersEmptySyllabus;
            $this->view->coursesEmptySyllabus = $coursesEmptySyllabus;

            //Enviando Datos de Reporte
            $this->view->emptyReport = $countERA;
            $this->view->totalEmptyReport = $totalTeachers - $countERA;
            $this->view->teachersEmptyReport = $teachersEmptyReport;
        }

        //Grafica para Encuesta
        //DataBases
        $pollDb                      = new Api_Model_DbTable_Poll();
        $pollQuestionsDb             = new Api_Model_DbTable_PollQuestion();
        $pollAlternativesDb = new Api_Model_DbTable_PollAlternatives();
        $pollResultsDb = new Api_Model_DbTable_PollResults();

        //Preguntas
        $where = array( 'eid' => $eid,
                        'oid' => $oid,
                        'perid' => '13B');
        $attrib = array('pollid');
        $poll = $pollDb->_getFilter($where);
        
        $where = array( 'eid' => $eid,
                        'oid' => $oid,
                        'pollid' => $poll[0]['pollid']);
        $attrib = array('qid', 'question');
        $order = array('qid');
        $pollQuestions = $pollQuestionsDb->_getFilter($where, $attrib, $order);

        $this->view->pollQuestions = $pollQuestions;

        $where = array( 'eid' => $eid,
                        'oid' => $oid,
                        'uid' => $uid,
                        'pid' => $pid,
                        'perid' => '13B');

        $coursesBefore = $coursesxTeacherDb->_getFilter($where);

        $c = 0;
        foreach ($coursesBefore as $course) {
            //Nombre de los Cursos
            $where = array( 'eid'      => $eid,
                            'oid'      => $oid,
                            'courseid' => $course['courseid'],
                            'curid'    => $course['curid'] );
            $attrib = array('name');
            $courseName = $courseDb->_getFilter($where, $attrib);
            $encuestaCoursesName[$c] = $courseName[0]['name'];
            $c++;
        }   
        $this->view->coursesBefore       = $coursesBefore;
        $this->view->encuestaCoursesName = $encuestaCoursesName;

        $answers[0] = '-';
        $c = 0;
        $altC = 0;
        foreach ($pollQuestions as $question) {
            $where = array( 'eid' => $eid,
                            'oid' => $oid,
                            'qid' => $question['qid'] );
            $attrib = array('alternative', 'atlid');
            $alternativesQuestion[$question['qid']] = $pollAlternativesDb->_getFilter($where, $attrib);
            $existe = 0;
            foreach ($alternativesQuestion[$question['qid']] as $alternativeQuestion) {
                foreach ($answers as $answer) {
                    if ($alternativeQuestion['alternative'] == $answer) {
                        $existe = 1;
                    }
                }
                if ($existe == 0) {
                    $answers[$c] = $alternativeQuestion['alternative'];
                    $c++;
                }
            }
        }

        $cNames = 0;
        foreach ($answers as $answer) {
            $answersName[$cNames] = $answer;
            $c = 0;
            foreach ($pollQuestions as $question) {
                foreach ($alternativesQuestion[$question['qid']] as $alternative) {
                    if ($answer == $alternative['alternative']) {
                        $answersAlternatives[$answer][$c] = $alternative['atlid'];
                        $c++;
                    }
                }
            }
            $cNames++;
        }

        foreach ($coursesBefore as $course) {
            $numPregunta = 0;
            foreach ($pollQuestions as $question) {
                $where = array( 'eid'   => $eid,
                                'oid'   => $oid,
                                'code'  => 'curid:'.$course['curid'].'-courseid:'.$course['courseid'].'-turno:'.$course['turno'],
                                'escid' => $course['escid'],
                                'subid' => $course['subid'],
                                'qid'   => $question['qid'] );
                
                $attrib = array('altid');
                $pollResults = $pollResultsDb->_getFilter($where, $attrib);
                foreach ($answers as $answer) {
                    $resultTotal[$question['qid']][$answer] = 0;
                    foreach ($pollResults as $result) {
                        foreach ($answersAlternatives[$answer] as $alternative) {
                            if ($result['altid'] == $alternative) {
                                $resultTotal[$question['qid']][$answer] = $resultTotal[$question['qid']][$answer] + 1;
                                $existeAlt = 0;
                            }else{
                                $existeAlt = 1;
                            }
                        }
                    }
                    if ($resultTotal[$question['qid']][$answer] == 0) {
                        $resultTotal[$question['qid']][$answer] = -1;
                    }
                    $resultTotalxAnswer[$course['courseid']][$course['escid']][$answer][$numPregunta] = $resultTotal[$question['qid']][$answer];
                }
                $numPregunta++;
            }
        }

        //print_r($resultTotalxAnswer);

        $numeroCurso = 0;
        foreach ($coursesBefore as $course) {
            $c = 0;
            foreach ($answers as $answer) {
                //print_r($answer);
                $datosEncuesta[$course['courseid']][$course['escid']][$c]['name'] = $answer;
                $datosEncuesta[$course['courseid']][$course['escid']][$c]['data'] = $resultTotalxAnswer[$course['courseid']][$course['escid']][$answer];
                $c++;
            }
            $dataEncuesta[$numeroCurso] = Zend_Json::encode($datosEncuesta[$course['courseid']][$course['escid']]);
            $numeroCurso++;
        }

        $this->view->dataEncuesta = $dataEncuesta;

        }catch (Exception $e) {

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
                   
            $perid = $this->_getParam('perid','13B');
            $where = array(
                        'eid' => $this->sesion->eid,
                        'oid'   =>$this->sesion->oid,
                        'perid' =>$perid,
                        'escid' =>$this->sesion->escid,
                        'subid' =>$this->sesion->subid,
                        'pid'   =>$this->sesion->pid
                    );
            $tb_course_teacher = new Api_Model_DbTable_Coursexteacher();
            $tb_course= new Api_Model_DbTable_Course();
            $dat_courses = $tb_course_teacher->_getFilter($where);

            foreach ($dat_courses as $key => $course) {
                $where1 = array(
                        'eid' =>    $this->sesion->eid,
                        'oid' =>    $this->sesion->oid,
                        'courseid' => $course['courseid'],
                        'subid'=>$this->sesion->subid,
                        'curid' =>$course['curid'],
                        'escid' =>$course['escid'],
                    );
                $name_course = $tb_course->_getOne($where1);
                $courses[$key] = $name_course['name'];  
            }
            
            $this->view->courses= Zend_Json::encode($courses);
           

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
                 $where['codigo']="curid:".$curso['curid']."-courseid:".$curso['courseid']."-turno:".$curso['turno'];
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
