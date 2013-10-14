<?php
class Rfacultad_ValidationController extends Zend_Controller_Action
{
	public function init()
	{
		$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		//$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	if (!$login->rol['module']=="rfacultad"){
    		//$this->_helper->redirector('index','index','default');
    	}
    	$this->sesion = $login;
	}

    public function indexAction() 
    {
      $this->_helper->redirector("addcourse");
    }

    public function addcourseAction() 
    {

        $eid= $this->sesion->eid;
        $oid= $this->sesion->oid;
        $temp = ($this->_getParam("temp"));
    
        $form=new Rfacultad_Form_Search;
        $form->buscar->setLabel("Buscar");
        $this->view->form=$form;  
        $perid='13C';
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
            ($convalidados);//break;
            $this->view->cursosconvalidados = $convalidados;

            
        }  catch (Exception $ex){
            print "Error: Selecciona rescuelas ".$ex->getMessage();
        }
    }

    public function coursexcurriculaAction() 
    {
       
        $this->_helper->layout()->disableLayout();
        $eid= $this->sesion->eid;
        $oid= $this->sesion->oid;
         // $eid= '123456';
         // $oid= "1";
        $curid= $this->_getParam("curid");
        $escid= $this->_getParam("escid");
        $subid= $this->_getParam("subid");
        $uid= $this->_getParam("uid");
        $pid= $this->_getParam("pid");
        $nota= $this->_getParam("nota");
        //$this->view->perid = $perid;

      $dbcurso = new Api_Model_DbTable_Course();       //admin
      $curso=  $dbcurso->_getCoursesXCurriculaXShool($eid,$oid,$curid,$escid);
      //print_r($curso);//break;
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
       $curid = $this->_getParam("curricula");
       $nota = $this->_getParam("nota");
       $semid = $this->_getParam("semid");
       $pid = $this->_getParam("pid");
       $reso = $this->_getParam("reso");
       $courseid = $this->_getParam("curso");
      // $turno = $this->_getParam("turno");
       $credi = $this->_getParam("credi");
       $uidreg = $this->_getParam("uidreg");  
     //  echo "aki vienen datos para guardar";//break;
       $temp = $this->_getParam("temp");
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
      if($temp=='1')
      {
        $cursoapto[0]['apto']==0;
        //break;      
      }
      else
      {

       $dbcursopen = new Api_Model_DbTable_Course();       //admin
       $cursoapto=  $dbcursopen->_getCourseLlevo($where);
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
     




    }






	
}