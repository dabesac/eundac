<?php
class Register_ValidationController extends Zend_Controller_Action
{
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
      $this->_helper->redirector("addcourse");
    }

    public function addcourseAction() 
    {
        $usuario = null;
        $eid= $this->sesion->eid;
        $oid= $this->sesion->oid;
        $temp = ($this->_getParam("temp"));
        print_r($temp);
        $form=new Register_Form_Search;
        $form->buscar->setLabel("Buscar");
        $this->view->form=$form;  

        $perid='14C';
        $this->view->perid = $perid;
        $this->view->temp = $temp;
        $rid='AL';
        $uid = $this->_getParam("uid");
        $db=new  Api_Model_DbTable_Periods();
        $where['eid']=$eid;
        $where['oid']=$oid;
        $d=$db->_getFilter($where);
        $dbusuario = new Api_Model_DbTable_Users();//ADMIN
        $state='A';
        $data['eid']=$eid;
        $data['oid']=$oid;
        $data['uid']=$uid;
        $data['state']=$state;
        if($uid<>""){
          $usuario=  $dbusuario->_getFilter($data);  
        }
        //print_r($usuario[0]['uid']);
        $this->view->usuario = $usuario;          
  
    }

    public function lcontainerAction(){
      try {
            $this->_helper->layout()->disableLayout();
            //$perid = ($this->_getParam("perid"));
            $escid = ($this->_getParam("escid"));
            $subid = ($this->_getParam("subid"));
            $uid = ($this->_getParam("uid"));
            $pid = ($this->_getParam("pid"));
            $nota = ($this->_getParam("nota"));
            $perid = ($this->_getParam("perid"));
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $userregistra= $this->sesion->uid;
            $temp = ($this->_getParam("temp"));
            // echo $perid;
            // $perid='12C';
            $this->view->nota = $nota;
            $this->view->uid = $uid;
            $this->view->perid = $perid;
            $this->view->pid = $pid;
            $this->view->user = $userregistra;
            $this->view->temp = $temp;
            $this->view->eid = $eid;
            $this->view->oid = $oid;
            $this->view->subid = $subid;
        
 
            $curiculas = new Api_Model_DbTable_Curricula();
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $cur=$curiculas->_getCurriculasXSchoolXstateAT($where);
            $this->view->curriculas=$cur;

            $dblistarconvalidacion = new Api_Model_DbTable_Registrationxcourse();
            $data['perid']=$perid;
            $data['uid']=$uid;
            $data['escid']=$escid;
            $data['subid']=$subid;
            $data['eid']=$eid;
            $data['oid']=$oid;

            $convalidados = $dblistarconvalidacion->_getFilter($data);  
            $this->view->cursosconvalidados = $convalidados;
            // print_r($convalidados);

            
        }  catch (Exception $ex){
            print "Error: Selecciona rescuelas ".$ex->getMessage();
        }
    }

    public function coursexcurriculaAction() 
    {
        $this->_helper->layout()->disableLayout();
        $eid= $this->sesion->eid;
        $oid= $this->sesion->oid;
        $curid= $this->_getParam("curid");
        $escid= $this->_getParam("escid");
        $subid= $this->_getParam("subid");
        $uid= $this->_getParam("uid");
        $pid= $this->_getParam("pid");
        $nota= $this->_getParam("nota");
      	$dbcurso = new Api_Model_DbTable_Course();       //admin
      	$curso=  $dbcurso->_getCoursesXCurriculaXShool($eid,$oid,$curid,$escid);
	
      	$this->view->cursito = $curso;
      	$this->view->eid = $eid;
      $this->view->oid = $oid;
      $this->view->subid = $subid;
   }

   public function  saveAction() 
    {
       
       $eid= $this->sesion->eid;
       $oid= $this->sesion->oid;
       $uid = $this->_getParam("uid");
       $escid = $this->_getParam("escid");
       $subid = $this->_getParam("subid");
       $perid = $this->_getParam("perid");
       $curid = $this->_getParam("curid");
       $nota = $this->_getParam("nota");
       $semid = $this->_getParam("semid");
       $pid = $this->_getParam("pid");
       $reso = $this->_getParam("reso");
       $courseid = $this->_getParam("courseid");

      // $turno = $this->_getParam("turno");
       $credi = $this->_getParam("credi");
       $uidreg = $this->_getParam("uidreg");  
     //  echo "aki vienen datos para guardar";//break;
       $temp = $this->_getParam("temp");
      $this->view->eid = $eid;
      $this->view->oid = $oid;
      $this->view->subid = $subid;
       if($temp<>'1')
      {
        $this->_helper->layout()->disableLayout();
        $this->view->temp = $temp;
      }
      $requisitos = new Api_Model_DbTable_Course ();
      $dat['eid']=$eid;
      $dat['oid']=$oid;
      $dat['subid']=$subid;
      $dat['escid']=$escid;
      $dat['courseid']=$courseid;
      $dat['curid']=$curid;
      $req = $requisitos ->_getOne($dat);

      $inforequisitos = $req['req_1']." | ".$req['req_2']." | ".$req['req_3'];

      $dbveces = new Api_Model_DbTable_Course();
      $where['escid']=$escid;
      $where['uid']=$uid;
      $where['curid']=$curid;
      $where['courseid']=$courseid;
      $vecesllevadas=  $dbveces->_getCoursesXStudentXV($where);
      // print_r($vecesllevadas);
      if($temp=='1')
      {
        $cursoapto[0]['apto']==0;
        //break;      
      }
      else
      {
        //print_r($where);

       $dbcursopen = new Api_Model_DbTable_Course();       
       $cursoapto=  $dbcursopen->_getCourseLlevo($where);
       //print_r($cursoapto);
      }
      if($cursoapto[0]['apto']==1)
        {
            ?>

            <script>
            //var veces = "<?php $vecesllevadas[0]['veces'];?>";
            alert("El curso ya ha sido llevado y aprobado");
            //alert(veces);
            </script>
           
            <td style="background: #ccc; color; #000;" ><h4><center><?php echo "El curso ya ha sido llevado y aprobado";
            echo " | #veces llevadas ".$vecesllevadas[0]['veces'];?></center></h4></td>
           
            <?php
        }
           else
        {
           if($cursoapto[0]['apto']==0)
           {
            // echo "puede llevar";
            // echo "llevo #veces ".$vecesllevadas[0]['veces'];
            $regid=$uid.$perid;
           $turno='A';
           $dbmatricula = new Api_Model_DbTable_Registration ();
            $mat['eid']=$eid;
            $mat['oid']=$oid;
            $mat['escid']=$escid;
            $mat['subid']=$subid;
            $mat['regid']=$regid;
            $mat['pid']=$pid;
            $mat['uid']=$uid;
            $mat['perid']=$perid;
          //  print_r($mat);


          
                             
               if($dbmatricula->_getOne($mat))
               {
                 //print_r($dbmatricula);
                 //echo "tiene matricula";
                      $dbperiodocurso = new Api_Model_DbTable_PeriodsCourses();
                      $periodo['eid']=$eid;
                      $periodo['oid']=$oid;
                      $periodo['courseid']=$courseid;
                      $periodo['escid']=$escid;
                      $periodo['perid']=$perid;
                      $periodo['turno']=$turno;
                      $periodo['subid']=$subid;
                      $periodo['curid']=$curid;


                      if($dbperiodocurso->_getOne($periodo))
                        {     
                                  //echo "el periodo esta creado";
                                $data['perid']= $perid;
                                $data['curid']= $curid;
                                $data['escid']= $escid;
                                $data['courseid']= $courseid;
                                $data['turno']= 'A';
                                $data['eid']= $eid;
                                $data['oid']= $oid;
                                $data['subid']= $subid;
                                $data['state_record']= 'A';
                                $data['type_rate']= 'O';
                                $data['register']=$this;
                                $data['semid']= $semid;
                                $data['state']= 'A'; 

                                   $pk['perid']=$perid;
                                   $pk['escid']=$escid;
                                   $pk['curid']=$curid;
                                   $pk['turno']=$turno;
                                   $pk['subid']=$subid;
                                   $pk['eid']=$eid;
                                   $pk['oid']=$oid;
                                   $pk['courseid']=$courseid;
                    

                                
                                $dbperiodocurso = new Api_Model_DbTable_PeriodsCourses();
                                $dbperiodocurso->_update($data,$pk);
                           

                                          $da2['courseid']= $courseid;
                                          $da2['escid']= $escid;
                                          $da2['perid']= $perid;
                                          $da2['uid']= $uid;
                                          $da2['pid']= $pid;
                                          $da2['regid']= $regid;
                                          $da2['turno']= 'A';
                                          $da2['eid']= $eid;
                                          $da2['oid']= $oid;
                                          $da2['subid']= $subid;
                                         // $data2['total_creditos']= $credi;//ojo
                                          $da2['notafinal']= $nota;
                                          $da2['register']= $uidreg;
                                          $da2['receipt']= "N";
                                          $da2['document_auth']= $reso;
                                          $da2['curid']= $curid;
                                          $da2['state']= 'M';
                                          $da2['approved']= $uidreg;
// print_r($datas2);
                                        

                                  $dbmatriculacurso = new Api_Model_DbTable_Registrationxcourse();
                                  

                                  $escuela = $dbmatriculacurso->_save($da2);

                                     $da3['eid']=$eid;
                                     $da3['oid']=$oid;
                                     $da3['escid']=$escid;
                                     $da3['subid']=$subid;
                                     $da3['courseid']=$courseid;
                                     $da3['curid']=$curid;
                                     $da3['turno']='A';
                                     $da3['perid']=$perid;
                                     $da3['uid']='DOCCONV01';
                                     $da3['pid']='CONV01';

                      
                                $dbdocentes = new Api_Model_DbTable_Coursexteacher();

                                if($dbdocentes->_getOne($da3))
                                  {

                                  }

                                  else
                                  {
                                  $da4['eid']= $eid;
                                  $da4['oid']= $oid;
                                  $da4['escid']= $escid;
                                  $da4['subid']= $subid;
                                  $da4['courseid']= $courseid;
                                  $da4['turno']= 'A';
                                  $da4['curid']= $curid;
                                  $da4['perid']= $perid;
                                  $da4['uid']= 'DOCCONV01';
                                  $da4['pid']= 'CONV01';
                                  $da4['semid']= $semid;
                                  $da4['is_main']= 'S';
                                  $da4['state']= 'A';

                   
                                 $dbdocente = new Api_Model_DbTable_Coursexteacher();
                                 $docente = $dbdocente->_save($da4);
                                   }

                        }

                       else
                       {
                           // echo "NO TIENE PERIODO Y SE CREA";
                                $da5['perid']= $perid;
                                $da5['curid']= $curid;
                                $da5['escid']= $escid;
                                $da5['courseid']= $courseid;
                                $da5['turno']= 'A';
                                $da5['eid']= $eid;
                                $da5['oid']= $oid;
                                $da5['subid']= $subid;
                                $da5['state_record']= 'A';
                                $da5['type_rate']= 'O';
                                $da5['register']=$uidreg;
                                $da5['receipt']= 'N';
                                $da5['resolution']= 'NULL';
                                $da5['semid']= $semid;
                                $da5['closure_date']= date("Y-m-d");
                                $da5['state']= 'A';


                                $dbperiodocurso = new Api_Model_DbTable_PeriodsCourses();

                                if($dbperiodocurso->_save($da5))
                                {
                                          $da6['cursoid']= $curso;
                                          $da6['escid']= $escid;
                                          $da6['perid']= $perid;
                                          $da6['uid']= $uid;
                                          $da6['pid']= $pid;
                                          $da6['regid']= $regid;
                                          $da6['turno']= 'A';
                                          $da6['eid']= $eid;
                                          $da6['oid']= $oid;
                                          $da6['subid']= $subid;
                                         // $data2['total_creditos']= $credi;//ojo
                                          $da6['notafinal']= $nota;
                                          $da6['register']= $uidreg;
                                          $da6['receipt']= "N";
                                          $da6['document_auth']= $reso;
                                          $da6['curid']= $curid;
                                          $da6['state']= 'M';
                                          $da6['approved']= $uidreg;


                                           $dbmatriculacurso = new Api_Model_DbTable_Registrationxcourse();
                                        $escuela = $dbmatriculacurso->_save($da6);
                                         
      
                                  $da7['eid']= $eid;
                                  $da7['oid']= $oid;
                                  $da7['escid']= $escid;
                                  $da7['subid']= $subid;
                                  $da7['courseid']= $courseid;
                                  $da7['turno']= 'A';
                                  $da7['curid']= $curid;
                                  $da7['perid']= $perid;
                                  $da7['uid']= 'DOCCONV01';
                                  $da7['pid']= 'CONV01';
                                  $da7['semid']= $semid;


                                $dbdocentes = new Api_Model_DbTable_Coursexteacher();
                     

                                if($dbdocentes->_getOne($da7))
                                {
                                    //echo "hay profe";
                                  }
                                  else
                                  {
                                    //echo "no hay profe y se crea";
                                  $da8['eid']= $eid;
                                  $da8['oid']= $oid;
                                  $da8['escid']= $escid;
                                  $da8['subid']= $subid;
                                  $da8['courseid']= $courseid;
                                  $da8['turno']= 'A';
                                  $da8['curid']= $curid;
                                  $da8['perid']= $perid;
                                  $da8['uid']= 'DOCCONV01';
                                  $da8['pid']= 'CONV01';
                                  $da8['semid']= $semid;
                                  $da8['is_main']= 'S';
                                  $da8['state']= 'A';


                   
                                 $dbdocente = new Api_Model_DbTable_Coursexteacher();
                                 $docente = $dbdocente->_save($da8);
                                  }


                                  } 
                        } 
              }

              else
              {
                $da9['regid']= $regid;
                $da9['pid']= $pid;
                $da9['escid']= $escid;
                $da9['uid']= $uid;
                $da9['perid']= $perid;
                $da9['eid']= $eid;
                $da9['oid']= $oid;
                $da9['subid']= $subid;
                $da9['semid']='0';
                $da9['credits']= $credi;
                $da9['register']= $uidreg;
                $da9['state']= "M";
                $da9['document_auth']= $reso;
                $da9['date_register']= date("Y-m-d");
                $da9['created']= date("Y-m-d");
                $da9['count']= 0;

              $dbmatricula = new Api_Model_DbTable_Registration();
              $dbmatricula->_save($da9);

             $dbperiodocurso = new Api_Model_DbTable_PeriodsCourses();
                $period['eid']=$eid;
                $period['oid']=$oid;
                $period['courseid']=$courseid;
                $period['escid']=$escid;
                $period['perid']=$perid;
                $period['escid']=$escid;
                $period['turno']=$turno;
                $period['subid']=$subid;
                $period['curid']=$curid;

                   $period1['perid']=$perid;
                   $period1['curid']=$curid;
                   $period1['escid']=$escid;
                   $period1['turno']=$turno;
                   $period1['courseid']=$courseid;
                   $period1['eid']=$eid;
                   $period1['oid']=$oid;



             if($dbperiodocurso->_getOne($period)){
              if($dbperiodocurso->_getFilter($period1))
                       {
                                  $da10['courseid']= $courseid;
                                  $da10['escid']= $escid;
                                  $da10['perid']= $perid;
                                  $da10['uid']= $uid;
                                  $da10['pid']= $pid;
                                  $da10['regid']= $regid;
                                  $da10['turno']= 'A';
                                  $da10['eid']= $eid;
                                  $da10['oid']= $oid;
                                  $da10['subid']= $subid;
                                 //$data2['total_creditos']= $credi;//ojo
                                  $da10['notafinal']= $nota;
                                  $da10['register']= $uidreg;
                                  $da10['receipt']= "N";
                                  $da10['document_auth']= $reso;
                                  $da10['curid']= $curid;
                                  $da10['state']= 'M';
                                  $da10['approved']= $uidreg;

                                   
                                $dbmatriculacurso = new Api_Model_DbTable_Registrationxcourse();
                                $escuela = $dbmatriculacurso->_save($da10);
                           
                                     $da11['eid']=$eid;
                                     $da11['oid']=$oid;
                                     $da11['escid']=$escid;
                                     $da11['subid']=$subid;
                                     $da11['courseid']=$courseid;
                                     $da11['curid']=$curid;
                                     $da11['turno']='A';
                                     $da11['perid']=$perid;
                                     $da11['uid']='DOCCONV01';
                                     $da11['pid']='CONV01';
                                     $dbdocentes = new Api_Model_DbTable_Coursexteacher();

                                if($dbdocentes->_getOne($da11))
                                 {

                                  }
                                  else
                                  {
                                  $da12['eid']= $eid;
                                  $da12['oid']= $oid;
                                  $da12['escid']= $escid;
                                  $da12['subid']= $subid;
                                  $da12['courseid']= $courseid;
                                  $da12['turno']= 'A';
                                  $da12['curid']= $curid;
                                  $da12['perid']= $perid;
                                  $da12['uid']= 'DOCCONV01';
                                  $da12['pid']= 'CONV01';
                                  $da12['semid']= $semid;
                                  $da12['is_main']= 'S';
                                  $da12['state']= 'A';

                   
                                 $dbdocente = new Api_Model_DbTable_Coursexteacher();
                                 $docente = $dbdocente->_save($da12);
                                  }
                                  

                        }
                        
                      } //ojoooooooooooooooooooooooooooooo
                     else
                        {
                                $da13['perid']= $perid;
                                $da13['curid']= $curid;
                                $da13['escid']= $escid;
                                $da13['courseid']= $courseid;
                                $da13['turno']= 'A';
                                $da13['eid']= $eid;
                                $da13['oid']= $oid;
                                $da13['subid']= $subid;
                                $da13['state_record']= 'A';
                                $da13['type_rate']= 'O';
                                $da13['register']=$uidreg;
                                $da13['receipt']= 'N';
                                $da13['resolution']= 'NULL';
                                $da13['semid']= $semid;
                                $da13['closure_date']= date("Y-m-d");
                                $da13['state']= 'A';


                         $dbperiodocurso = new Api_Model_DbTable_PeriodsCourses();

                         if($dbperiodocurso->_save($da13))
                                {

                                  $da14['cursoid']= $curso;
                                  $da14['escid']= $escid;
                                  $da14['perid']= $perid;
                                  $da14['uid']= $uid;
                                  $da14['pid']= $pid;
                                  $da14['regid']= $regid;
                                  $da14['turno']= 'A';
                                  $da14['eid']= $eid;
                                  $da14['oid']= $oid;
                                  $da14['subid']= $subid;
                                  //$data2['total_creditos']= $credi;//ojo
                                  $da14['notafinal']= $nota;
                                  $da14['register']= $uidreg;
                                  $da14['receipt']= "N";
                                  $da14['document_auth']= $reso;
                                  $da14['curid']= $curid;
                                  $da14['state']= 'M';
                                  $da14['approved']= $uidreg;


                                $dbmatriculacurso = new Api_Model_DbTable_Registrationxcourse ();
                                $escuela = $dbmatriculacurso->_save($da14);
                                 
                               // echo "para guardar en docentes";

                                     $da15['eid']=$eid;
                                     $da15['oid']=$oid;
                                     $da15['escid']=$escid;
                                     $da15['subid']=$subid;
                                     $da15['courseid']=$courseid;
                                     $da15['curid']=$curid;
                                     $da15['turno']='A';
                                     $da15['perid']=$perid;
                                     $da15['uid']='DOCCONV01';
                                     $da15['pid']='CONV01';
                                     $dbdocentes = new Api_Model_DbTable_Coursexteacher();
                                     
                                if($dbdocentes->_getOne($da15))
                                {

                                  }
                                  else
                                  {
                                  $da16['eid']= $eid;
                                  $da16['oid']= $oid;
                                  $da16['escid']= $escid;
                                  $da16['subid']= $subid;
                                  $da16['courseid']= $courseid;
                                  $da16['turno']= 'A';
                                  $da16['curid']= $curid;
                                  $da16['perid']= $perid;
                                  $da16['uid']= 'DOCCONV01';
                                  $da16['pid']= 'CONV01';
                                  $da16['semid']= $semid;
                                  $da16['is_main']= 'S';
                                  $da16['state']= 'A';

                   
                                 $dbdocente = new Api_Model_DbTable_Coursexteacher();
                                 $docente = $dbdocente->_save($da16);
                                  }

                             }

                       }

              }

            $datm['perid']=$perid;
            $datm['uid']=$uid;
            $datm['escid']=$escid;
            $datm['subid']=$subid;
            $datm['eid']=$eid;
            $datm['oid']=$oid;
            $dblistarconvalidacion = new Api_Model_DbTable_Registrationxcourse();
            $convalidados = $dblistarconvalidacion->_getFilter($datm); 
            //print_r($convalidados); 
            $this->view->cursosconvalidados = $convalidados;

            
        
             }
           else
           {
             ?>

            <script>
            alert("No puede llevar el curso ya que no cumplio con el Prerequisito Necesario");
            </script>

             <td  style="background: #ccc; color: #ff0000;"><h4><center><?php echo "No puede llevar el curso ya que no cumplio con el Prerequisito ".$inforequisitos;
             echo " | #veces llevadas: ".$vecesllevadas[0]['veces']; ?>
             </center></h4></td>

            <script>
                
                
                if(confirm("Esta seguro de continuar \nAgregando el Curso a pesar de no haber aprobado el prerequisito"))

                document.location.href="/register/directd/student/temp/1/uid/<?php echo $uid?>/escid/<?php echo $escid?>/subid/<?php echo $subid?>/perid/<?php echo $perid?>/curid/<?php echo $curid?>/nota/<?php echo $nota?>/semid/<?php echo $semid?>/pid/<?php echo $pid ?>/reso/<?php echo $reso?>/courseid/<?php echo $courseid?>/credi/<?php echo $credi?>/uidreg/<?php echo $uidreg?>";
    
                else
                window.close();
                
              </script>

             <td> <div id="agregar"></div></td>
             <?php
           }
         }

  }
  public function printAction() 
          {
            // $this->_helper->layout()->disableLayout();
                
             $eid= $this->sesion->eid;
             $oid= $this->sesion->oid;
            
             $uid = $this->_getParam("uid");
             $pid = $this->_getParam("pid");
             $escid = $this->_getParam("escid");
             $subid = $this->_getParam("subid");
             $perid = $this->_getParam("perid");
             //$curid = $this->_getParam("curid");
             //echo $perid;
              
                  $this->view->uid = $uid;
                  $this->view->pid = $pid;
                  $this->view->perid = $perid;
                  $this->view->pid = $pid;
                  $this->view->escid = $escid;
                  $this->view->subid = $subid;
                  $this->view->eid = $eid;
                  $this->view->oid = $oid;
              $print['eid']=$eid;
              $print['oid']=$oid;
              $print['perid']=$perid;
              $print['uid']=$uid;
              $print['escid']=$escid;
              $print['subid']=$subid;
              

              $dblistarconvalidacion = new Api_Model_DbTable_Registrationxcourse();
              $convalidados = $dblistarconvalidacion->_getFilter($print);  
                      
              $this->view->cursosconvalidados = $convalidados;

          }

  public function updateAction(){
      try {
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $escid = ($this->_getParam("escid"));
            $subid = ($this->_getParam("subid"));
            $uid = ($this->_getParam("uid"));
            $pid = ($this->_getParam("pid"));
            $perid = ($this->_getParam("perid"));
            $regid = ($this->_getParam("regid"));
            $courseid = ($this->_getParam("courseid"));
            $curid = ($this->_getParam("curid"));
            $turno = ($this->_getParam("turno"));
            $this->view->uid=$uid;
            $this->view->curid=$curid;
            $this->view->courseid=$courseid;
            $d['eid']=$eid;
            $d['oid']=$oid;
            $d['escid']=$escid;
            $d['subid']=$subid;
            $d['uid']=$uid;
            $d['pid']=$pid;
            $d['perid']=$perid;
            $d['regid']=$regid;
            $d['courseid']=$courseid;
            $d['curid']=$curid;
            $d['turno']=$turno;
            $dblista = new Api_Model_DbTable_Registrationxcourse();
            $lista = $dblista ->_getOne($d);
            $form=new Register_Form_Changenotes;
            $form->populate($lista);
            $this->view->form=$form;
            if ($this->getRequest()->isPost()) {
              $frmdata=$this->getRequest()->getPost();
                if ($form->isValid($frmdata)) {
                  unset($frmdata['Guardar']);
                   $frmdata['modified']=$this->sesion->uid;
                               
                    $reg_= new Api_Model_DbTable_Registrationxcourse();
                    $reg_->_updatenoteregister($frmdata,$d);
                    $this->_redirect("/register/validation");
                }
                    else
                {
                    echo "Ingrese nuevamente por favor";
                }
      }



            

          }catch (Exception $e) {
            print "Error index Registration ".$e->$getMessage();
        }          

     }


	
}