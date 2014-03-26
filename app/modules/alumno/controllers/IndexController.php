<?php

class Alumno_IndexController extends Zend_Controller_Action {

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
            $where['uid']=$this->sesion->uid;
            $where['eid']=$this->sesion->eid;
            $where['oid']=$this->sesion->oid;
            $where['pid']=$this->sesion->pid;
            $where['escid']=$this->sesion->escid;
            $where['subid']=$this->sesion->subid;
            $this->view->escid = $where['escid'];
            $this->view->uid = $where['uid'];
            $this->view->oid = $where['oid'];
            $this->view->eid = $where['eid'];
            $dbcurricula=new Api_Model_DbTable_Studentxcurricula();
            $datcur=$dbcurricula->_getOne($where);
            $where['curid']=$datcur['curid'];
            $this->view->curid = $where['curid'];
            $dbcursos=new Api_Model_DbTable_Course();
            $datcursos=$dbcursos->_getCountCoursesxSemester($where);
            $cur=$dbcursos->_getCountCoursesxApproved($where);


            $data_all = array();
            $data_rel = array();
            if ($cur) {
                foreach ($datcursos as $key => $courses) {
                        $data_course[$key] = $courses['cantidad_cursos'];
                        $data_all[0][$courses['semid']-1]=$courses['cantidad_cursos'];
                        $semestre[$key]=$courses['semid'];

                }
                foreach ($cur as $key => $value) {
                        $data_all[1][$value['semid']-1]= $value['cantidad_cursos'];
                        $semestre_t[$key] = $value['semid'];
                }

                foreach ($data_all[0] as $key => $value) {
                    if (!array_key_exists($key,$data_all[1])) {
                        $data_all[1][$key]=0;
                    }
                }
                for ($i=0; $i  < count($data_all[0]); $i++) { 
                    $data_rel[$i]=$data_all[1][$i];
                }
            }
           
            $courses_x_sem = array(
                    '0'=>array(
                            'name'=>'Cursos por Semestre',
                            'data'=>$data_all[0]
                            ),
                    '1'=>array(
                            'name'=>'Cantidad de Cursos Llevados',
                            'data'=>$data_rel
                            ),
            );
            $data = Zend_Json::encode($courses_x_sem);
            $this->view->datos = $data;

            $where['perid'] = '13B';//$this->sesion->period->perid;
            $tb_assistence = new Api_Model_DbTable_StudentAssistance();
            $dat_assist =$tb_assistence->_assistence($where);


            $dat_assist_all = array();
            $data_courses = array();
            $data_assistences = array();
            
            foreach ($dat_assist as $key => $assistence) {
                $data_courses[$key]= $assistence['name'];
                $assist = 0;
                $late = 0;
                $short = 0;
                $retired =0;
                for ($i=1; $i < 35 ; $i++) { 
                    if ($assistence['a_sesion_'.$i] == 'A') {
                        $assist ++;
                    }
                    if ($assistence['a_sesion_'.$i] == 'T') {
                        $late ++;
                    }
                    
                    if ($assistence['a_sesion_'.$i] == 'F') {
                        $short ++;
                    }
                    if($assistence['a_sesion_'.$i] == 'R'){
                        $retired ++;
                    }

                }
                    $data_assistences['asistio'][$key] = $assist ;
                    $data_assistences['late'][$key] = $late;
                    $data_assistences['short'][$key] = $short;
                    $data_assistences['retired'][$key] =  $retired;
            }

            $result  = array(
                            '0' =>  array(
                                        'name' => 'Asistio',
                                        'data'  =>$data_assistences['asistio']
                                    ),
                            '1' =>  array(
                                        'name'  =>  'Tarde',
                                        'data'  =>$data_assistences['late']
                                ),
                            '2' =>  array(
                                        'name'  =>'Falto',
                                        'data'  => $data_assistences['short']
                                ),
                            '3' =>  array(
                                        'name'  =>'Retirado',
                                        'data'  => $data_assistences['retired']
                                ),
                         );

            $result = Zend_Json::encode($result);
            $data_courses = Zend_Json::encode($data_courses);
            
            $this->view->courses = $data_courses;
            $this->view->dat_assist = $result;
            //print_r($data_courses);exit();
            $perid = $this->sesion->period->perid;
            $this->view->perid = $perid;
            //news Alumno
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;

            $newDb = new Api_Model_DbTable_News();
            $newsRolDb = new Api_Model_DbTable_NewsRol();

            $news = $newDb->_getLastNews();

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
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }


    }

    public function _verifyprofile(){
        try {
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $pid = $this->sesion->pid;
            $uid = $this->sesion->uid;
            $escid = $this->sesion->escid;
            $subid = $this->sesion->subid;

            $where_stadis = array(
                'eid' => $eid, 'oid' => $oid, 'pid' => $pid, 'uid' => $uid, 'escid' => $escid, 'subid' => $subid);
            $stadis= new Api_Model_DbTable_Statistics();
            $data_stadis = $stadis->_getOne($where_stadis);

            $where_rel = array('eid' => $eid, 'pid' => $pid);
            $rel = new Api_Model_DbTable_Relationship();
            $data_rel = $rel->_getFilter($where_rel, $attrib=null, $orders=null);

            $acad = new Api_Model_DbTable_Academicrecord();
            $data_acad = $acad->_getFilter($where_rel, $attrib=null, $orders=null);

            $jobs = new Api_Model_DbTable_Jobs();
            $data_jobs = $jobs->_getFilter($where_rel, $attrib=null, $orders=null);

            $inter = new Api_Model_DbTable_Interes();
            $data_inter = $inter->_getFilter($where_rel, $attrib=null, $orders=null);

            if (!$data_acad || !$data_stadis || !$data_rel || !$data_inter || !$data_jobs) {
                return "1";
            }else{
                return "0";
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function _validaren()
    {
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $escid = $this->sesion->escid;
        $uid = $this->sesion->uid;
        $pid=$this->sesion->pid;
        $perid=$this->sesion->period->perid;

        $where = array('eid'=>$eid,'oid'=>$oid,);
        $encuestas = new  Api_Model_DbTable_Polll();
        $encuesta=$encuestas->_getEncuestaActiva($where);

        if ($encuesta) 
        {
               $pollid=$encuesta['pollid'];  
               $where1 = array('eid'=>$eid,'oid'=>$oid,'pollid'=>$pollid);
               $dbpreguntas = new Api_Model_DbTable_PollQuestion();
               $lista=$dbpreguntas->_getPreguntasXencuesta($where1);

                if ($lista)
                {
                   $resp = new Api_Model_DbTable_Results();
                   foreach ($lista as $lista_)
                   {
                      $qid=$lista_['qid'];
                                     
                      $where2 = array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid,'qid'=>$qid);
                      $paso=$resp->_getAlumnoPasoEncuestaT($where2);
                      if($paso)
                      {
                        return "true";
                      }

                   }
   
               }
        }
        return "false";
    }

    public function graphicsperformanceAction()
    {
        try
        {
            $this->_helper->layout()->disableLayout();         
            $where['uid']=$this->sesion->uid;
            $where['eid']=$this->sesion->eid;
            $where['oid']=$this->sesion->oid;
            $where['pid']=$this->sesion->pid;
            $where['escid']=$this->sesion->escid;
            $where['subid']=$this->sesion->subid;
            $this->view->escid = $where['escid'];
            $this->view->uid = $where['uid'];
            $this->view->oid = $where['oid'];
            $this->view->eid = $where['eid'];
            $dbcurricula=new Api_Model_DbTable_Studentxcurricula();
            $datcur=$dbcurricula->_getOne($where);
            $where['curid']=$datcur['curid'];
            $this->view->curid = $where['curid'];
            $dbcursos=new Api_Model_DbTable_Course();
            $datcursos=$dbcursos->_getCountCoursesxSemester($where);
            // print_r($datcursos);
            $this->view->data=$datcursos;
            $cur=$dbcursos->_getCountCoursesxApproved($where);
            // print_r($cur);
            $this->view->cursos=$cur;
        }
        catch(Exception $ex)
        {
              print $ex->getMessage();
        }                  
                
    }


    public function assistanceAction()
    {
        try {
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $perid=$this->sesion->period->perid;
            $uid=$this->sesion->uid;
            $pid=$this->sesion->pid;
            $regid = $uid.$perid;

            $where = null;
            $where = array(
                'eid'=>$eid,'oid'=>$oid,
                'escid'=>$escid,'subid'=>$subid,
                'perid'=>$perid,'uid'=>$uid,
                'pid'=>$pid,'regid'=>$regid,
                );
            $base_assistance_student = new Api_Model_DbTable_StudentAssistance();
            $assistance_student = $base_assistance_student->_getFilter($where);
            
            if ($assistance_student) {
                $base_course = new Api_Model_DbTable_Course();
                foreach ($assistance_student as $key => $value) {
                    $where['courseid']=$value['coursoid'];
                    $where['curid']=$value['curid'];
                    $name = $base_course->_getOne($where);
                    $assistance_student[$key]['name']=$name['name'];
                }
            }
            
            $this->view->assitance=$assistance_student;

        } catch (Exception $e) {
            print "Error Asistencia Alumnno".$e->gegtMessage();
        }
    }

        public function graphicsassistanceAction()
    {
        try
        {
            $this->_helper->layout()->disableLayout();         
            $wheres['uid']=$this->sesion->uid;
            $wheres['eid']=$this->sesion->eid;
            $wheres['oid']=$this->sesion->oid;
            $wheres['pid']=$this->sesion->pid;
            $wheres['escid']=$this->sesion->escid;
            $wheres['subid']=$this->sesion->subid;
            $wheres['perid']=$this->sesion->period->perid;
            $this->view->escid = $wheres['escid'];
            $this->view->uid = $wheres['uid'];
            $this->view->oid = $wheres['oid'];
            $this->view->eid = $wheres['eid'];
            $lcursos = new Api_Model_DbTable_StudentAssistance();
            $listacurso =$lcursos->_assistence($wheres);
            // print_r($listacurso);
            $j=0;
            $a=1;
            
            foreach ($listacurso as $cursomas){
            	
                $where[$j]['eid']=$wheres["eid"];
                $where[$j]['oid']=$wheres["oid"];
                $where[$j]['escid']=$wheres["escid"];
                $where[$j]['subid']=$wheres["subid"];
                $where[$j]['perid']=$wheres["perid"];
                $where[$j]['courseid']=$cursomas["coursoid"];
                $where[$j]['name']=$cursomas["name"];
                $where[$j]['curid']=$cursomas["curid"];
                $where[$j]['turno']=$cursomas["turno"];
                $periods = new Api_Model_DbTable_PeriodsCourses();
                $state =$periods->_getOne($where[$j]);
                // print_r($state);
                $var=$cursomas['state'];
                $this->view->var=$var;       
                $x=0;
                $x0=0;
                $x1=0;
                for ($i=1; $i < 35 ; $i++) { 
                        $assis=$cursomas["a_sesion_".$i];
                    if ($assis=='A' ) {
                        $x++;
                    }
                    if ( $assis=='T') {
                        $x0++;
                    }
                     if ($assis=='F') {
                        $x1++;
                    }
                }
                $where[$j]['asistio']=$x;
                $where[$j]['tarde']=$x0;
                $where[$j]['falto']=$x1;

                if ($x1>=6) {
                $where[$j]['coment']='R';
                    }
                else{
                $where[$j]['coment']='N';
                }
                   $j++;
        }


        $this->view->assistence=$where; 

        }
        catch(Exception $ex)
        {
              print $ex->getMessage();
        }                  
                
    }


    public function encuestaAction()
    {
        $this->_helper->layout()->disableLayout();        
        $eid = $this->sesion->eid;  
        $oid = $this->sesion->oid;      
        $escid = $this->sesion->escid;       
        $uid = $this->sesion->uid;      
        $pid=$this->sesion->pid;
        $perid= $this->sesion->period->perid;
        $subid = $this->sesion->subid;
        $rid = $this->sesion->rid;

        $registerDb = new Api_Model_DbTable_Registration();
        $where = array('eid'=>$eid, 
                        'oid'=>$oid, 
                        'pid'=>$pid, 
                        'uid'=>$uid, 
                        'escid'=>$escid, 
                        'subid'=>$subid, 
                        'perid'=>'13B');
        $attrib = array('state');
        $register = $registerDb->_getFilter($where, $attrib);

        if ($register[0]['state'] == 'M' && $rid == 'AL') {
            $data['eid']=$eid;
            $data['oid']=$oid;
            $where = array('eid'=>$eid,'oid'=>$oid,);
            $encuestas = new  Api_Model_DbTable_Polll();
            $encuesta=$encuestas->_getEncuestaActiva($where);
            //print_r($encuesta);
            if($encuesta)
            {
                $this->view->encuesta=$encuesta;
                $pollid=$encuesta['pollid'];
                $where1 = array('eid'=>$eid,'oid'=>$oid,'pollid'=>$pollid);
                $order = array('position  ASC');

                //print_r($where1);
                $dbpreguntas = new Api_Model_DbTable_PollQuestion();
                $preguntas=$dbpreguntas->_getPreguntasXencuesta($where1,$order);
                //print_r($preguntas);
                $this->view->preguntas=$preguntas;

                $cursos = new Api_Model_DbTable_Registrationxcourse();
                $where2=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'uid'=>$uid,'pid'=>$pid,'perid'=>$perid);
                //print_r($where2);
                $curs=$cursos->_getFilter($where2);
                //print_r($curs);

                $c= new Api_Model_DbTable_Course();
                $cur=array();
                if($curs)
                {

                    foreach ($curs as $curso) 
                    {
                        $data['eid']=$eid;
                        $data['oid']=$oid;
                        $data['escid']=$escid;
                        $data['subid']=$curso['subid'];
                        $data['courseid']=$curso['courseid'];
                        $data['curid']=$curso['curid'];
                        
                        //print_r($data);

                        $c_=$c->_getOne($data);
                        //print_r($c_);
                        $c_['turno']=$curso['turno'];
                        $cur[]=$c_;
                        //print_r($cur);
                    }
                }
                //print_r($curso);
                if(!$cur)
                {
                    $this->_redirect('/profile/public/student');
                }
            }       
        }else{
            $this->_redirect('/profile/public/student');
        }
        $this->view->cursos=$cur;
     
    }


    public function guardarAction()
    {
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $escid = $this->sesion->escid;
        $uid = $this->sesion->uid;
        $pid=$this->sesion->pid;
        $perid= $this->sesion->period->perid;        
        $subid= $this->sesion->subid;
        $respuestas = $_POST;

        //print_r($respuestas);
        foreach ($respuestas as $respuesta)
        {
            $respuesta_ = new Api_Model_DbTable_Results();
            $tmp1 = split(";--;",$respuesta);
            $tmp = split("-",$tmp1[0]);

            $qid = $tmp[0];
            $altid = $tmp[1];

            $data['oid']="$oid" ;
            $data['eid']=$eid ;
            $data['qid']="$qid";
            $data['altid']="$altid" ;
            $data['subid']="$subid" ;
            $data['pid']="$pid" ;           
            $data['escid']="$escid" ;
            $data['uid']="$uid" ;
            $data['created']=date('Y-m-d') ;
            $data['code']=  $tmp1[1];

            //print_r($data);
            if($respuesta_->_save($data))
            {
                
            }

        }
        
        $this->_redirect("/");

    }

}
