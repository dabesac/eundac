<?php

class Rfacultad_ConditionController extends Zend_Controller_Action {

    public function init()
    {
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        if (!$login->rol['module']=="rfacultad"){
            $this->_helper->redirector('index','index','default');
        }
        $this->sesion = $login; 
            }

    public function indexAction()
    {
        try
        {
            $eid=$this->sesion->eid; 
            $oid=$this->sesion->oid;
            $facid=$this->sesion->faculty->facid;
            $subid=$this->sesion->subid;
            $perid=$this->sesion->period->perid;
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['facid']=$facid;
            $where['state']="A";
            $where['subid']=$subid;
            $this->view->perid=$perid;
            $this->view->subid=$subid;
            $this->view->facid=$facid;

            $esp = new Api_Model_DbTable_Speciality();
            if($where['subid']<>'1901'){
                $data['eid']=$where['eid'];
                $data['oid']=$where['oid'];
                $data['subid']=$where['subid'];
                $data['state']='A';
               $gesp = $esp->_getFilter($data); 
            }

           else{
                if($where['facid']=='5' || $where['facid']=='2'){
                $data['eid']=$where['eid'];
                $data['oid']=$where['oid'];
                $data['facid']=$where['facid'];
                $data['state']='A';
               $gesp = $esp->_getFilter($data); 
                }
                else{
                $gesp = $esp->_getspeciality($where); 
                }
            }
            if ($gesp ) $this->view->getEsp=$gesp;
            $form=new Rfacultad_Form_Getcond();
            $form->send->setLabel("Buscar");
            $this->view->form=$form;
        }  
        catch (Exception $ex)
        {
            print "Error: Cargar Periodos".$ex->getMessage();
        }

    }

    public function getstudentAction()
    {
        try
        {
            $this->_helper->getHelper('layout')->disableLayout();
            $perid=$this->sesion->period->perid;
            $eid=$this->sesion->eid; 
            $oid=$this->sesion->oid;
            $name = trim(strtoupper($this->_getParam('name')));
            $ap = trim(strtoupper($this->_getParam('ap')));        
            $am = trim(strtoupper($this->_getParam('am')));        
            $code = $this->_getParam('uid');        
            $escid = $this->_getParam('escid');   
            $cond = $this->_getParam('cond');
            $this->view->name=$name;
            $this->view->ap=$ap;
            $this->view->am=$am;
            $this->view->code=$code;
            $this->view->escid=$escid; 
            $this->view->perid= $perid;
            $where['oid']=$oid;
            $where['eid']=$eid;
            $where['escid']=$escid;
            $where['perid']=$perid;
            $where['name']=$name;
            $where['ap']=$ap;
            $where['am']=$am;
            $where['uid']=$code;


           $bdu = new Api_Model_DbTable_Conditionstudent();        
            if ($cond=='C')
            {
                $str = " and (p.last_name0 like '%$ap%' and p.last_name1 like '%$am%' and ca.uid like '$code%' and upper(p.first_name) like '%$name%' )";            
                $datos= $bdu->_getUsercCondition($eid,$oid,$str,$escid,$perid);
            }
            if ($cond=='S')
            {
                $datos= $bdu->_getUsersCondition($where);
            }
            $this->view->bdu=$bdu;
            $this->view->condicion=$cond;          
            $this->view->datos=$datos;                       
        }  
        catch (Exception $ex)
        {
            print "Error: Al listar Alumno".$ex->getMessage();
        }
    }

     public function detailAction()
    {
        try
        {
            $this->_helper->getHelper('layout')->disableLayout();
            $uid = base64_decode($this->_getParam('uid'));
            $pid = base64_decode($this->_getParam('pid'));
            $escid = base64_decode($this->_getParam('escid'));
            $subid = base64_decode($this->_getParam('subid'));
            $condi = base64_decode($this->_getParam('condi'));
            $listacursos = ($this->_getParam('listacursos'));
            $form=new Rfacultad_Form_Condition();
            $perid=$this->sesion->period->perid;
            $eid=$this->sesion->eid; 
            $oid=$this->sesion->oid;
            $uidreg = $this->sesion->uid;
            $this->view->perid=$perid;
            $this->view->eid=$eid;
            $this->view->oid=$oid; 
            $this->view->condi=$condi; 
            $this->view->escid=$escid;
            $this->view->subid=$subid; 
            $this->view->uid=$uid; 
            $this->view->pid=$pid; 

            $where_person = array('eid'=>$eid, 'pid' => $pid);
            $persondb = new Api_Model_DbTable_Person();
            $data_person = $persondb->_getOne($where_person);
            $this->view->data_person=$data_person;

            $where['eid']=$this->sesion->eid;
            $where['oid']=$this->sesion->oid;
            $where['uid']=$uid;
            $where['perid']=$perid; 
            $where['escid']=$escid; 
            $where['pid']=$pid;
            $where['subid']=$subid;
            $dbcurricula=new Api_Model_DbTable_Studentxcurricula();
            $datcur=$dbcurricula->_getOne($where);
            $where['curid']=$datcur['curid'];
            $this->view->curid=$where['curid'];

            $request = array( 'uid'   => base64_encode($where['uid']), 
                              'pid'   => base64_encode($where['pid']), 
                              'escid' => base64_encode($where['escid']),
                              'subid' => base64_encode($where['subid']),
                              'eid'   => base64_encode($where['eid']),
                              'oid'   => base64_encode($where['oid']),
                              'perid' => base64_encode($where['perid']),
                              'curid' => base64_encode($where['curid'] ) );

            $server = new Eundac_Connect_Api('delete_course', $request);
            $data = $server->connectAuth();
            $this->view->cursos = $data;

            $conditiondb = new Api_Model_DbTable_Conditionstudent();
            $where_condition = array(
                                    'eid' => $where['eid'],
                                    'oid' => $where['oid'],
                                    'uid' => $where['uid'],
                                    'pid' => $where['pid'],
                                    'escid' => $where['escid'],
                                    'subid' => $where['subid'],
                                    'perid' => $where['perid']);
            $attrib = null;
            $order = null;
            $condition_student = $conditiondb->_getFilter($where_condition, $attrib, $order);
            $cursodb = new Api_Model_DbTable_Course();
            /*  $i=0;
            foreach ($condition_student as $conditions) {
                if($conditions['courseid']){
                    $wherecurse = array(
                                        'eid' => $where['eid'],
                                        'oid' => $where['oid'],
                                        'curid' => $where['curid'],
                                        'escid' => $where['escid'],
                                        'subid' => $where['subid'],
                                        'courseid' => $conditions['courseid']);
                    $datacourse[$i] =$cursodb->_getOne($wherecurse);

                }
                $i++;
            }*/
            $this->view->conditionsStudent = $condition_student;
            //$this->view->datacourse =$datacourse;

            /*if ($this->getRequest()->isPost())
            {
                $formData = $this->getRequest()->getPost(); 
                $condi = $formData['condi'];
                $formData['perid']=$perid;
                $formData['eid']=$eid;
                $formData['oid']=$oid;
                unset($formData['condi']);
                if (!($condi=='S' &&  $formData['semester']=="" && $formData['credits']=="" && $formData['num_registration']==""))
                {
                    $formData1= $formData;
                    $bdcondiciones = new Api_Model_DbTable_Condition();  
                    if ($condi == 'C' || $condi == 'S')
                    {
                        if ($formData['semester']=="") unset($formData['semester']);
                        if ($formData['credits']=="") unset($formData['credits']);
                        if ($formData['num_registration']=="") unset($formData['num_registration']);
                        $formData['register']=$uidreg; 
                        unset($formData['guardar']);  
                        unset($formData['listacursos']);            
                        $dato=$bdcondiciones->_guardar($formData);
                      if($listacursos){
                        if($dato){                      
                        $Data['cnid']=$dato['cnid'];
                        $Data['eid']=$eid;
                        $Data['oid']=$oid;
                        $Data['escid']=$dato['escid'];  
                        $Data['subid']=$dato['subid'];
                        $Data['perid']=$dato['perid'];;
                        $Data['uid']=$dato['uid'];
                        $Data['pid']=$dato['pid'];
                         $condicion = new Api_Model_DbTable_Studentcondition();
                         for ($i=0; $i < count($listacursos) ; $i++) { 
                        $Data['courseid']=$listacursos[$i];
                        $dato=$condicion->_guardar($Data);
                         }
                        }
                        }                    
                    }
                $this->view->g_reg = "Se guardo Correctamente";
                }
            }
            else
            {
                $where['eid']=$eid;
                $where['oid']=$oid;
                $where['pid']=$pid;
                $where['escid']=$escid;
                $where['uid']=$uid;
                $where['perid']=$perid;
                $where['subid']=$subid;                
                if ($condi=='C')
                {
                    $bdalumno = new Api_Model_DbTable_Condition();        
                    $datos= $bdalumno->_getFilter($where);
                    $this->view->datos=$datos;
                }
            }
            $this->view->form=$form;*/
        }
        catch(Exception $ex )
        {
            print ("Error Controlador Mostrar Datos en detalles: ".$ex->getMessage());
        } 
    }

    public function guardarAction(){
        try{
            $this->_helper->getHelper('layout')->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $uidreg = $this->sesion->uid;
            $conditiondb = new Api_Model_DbTable_Conditionstudent();
            if ($this->getRequest()->isPost()){
                $formData = $this->getRequest()->getPost(); 
                

                if($formData['curso']!= 'ns'){
                    $data = array(
                            'eid'       => $eid,
                            'oid'       => $oid,
                            'uid'       => $formData['uid'],
                            'pid'       => $formData['pid'],
                            'escid'     => $formData['escid'],
                            'subid'     => $formData['subid'],
                            'perid'     => $formData['perid'],
                            'type'      => 'CO',
                            'courseid'  => base64_decode($formData['curso']),
                            'curid'     => $formData['curid'],
                            'createduid' => $uidreg,
                            'doc_authorize' => $formData['resolution']);
                    $savedata = $conditiondb->_save($data);
                }
                if($formData['dcurso']!= 'ns'){
                    $data = array(
                            'eid'       => $eid,
                            'oid'       => $oid,
                            'uid'       => $formData['uid'],
                            'pid'       => $formData['pid'],
                            'escid'     => $formData['escid'],
                            'subid'     => $formData['subid'],
                            'perid'     => $formData['perid'],
                            'type'      => 'LE',
                            'courseid'  => base64_decode($formData['dcurso']),
                            'curid'     => $formData['curid'],
                            'createduid' => $uidreg,
                            'doc_authorize' => $formData['resolution']);
                    $savedata = $conditiondb->_save($data);
                }
                if($formData['credit']!= '0'){
                    $data = array(
                            'eid'       => $eid,
                            'oid'       => $oid,
                            'uid'       => $formData['uid'],
                            'pid'       => $formData['pid'],
                            'escid'     => $formData['escid'],
                            'subid'     => $formData['subid'],
                            'perid'     => $formData['perid'],
                            'type'      => 'CR',
                            'amount'  => $formData['credit'],
                            'createduid' => $uidreg,
                            'doc_authorize' => $formData['resolution']);
                    $savedata = $conditiondb->_save($data);
                }
                if($formData['nsem']!= '0'){
                    $data = array(
                            'eid'       => $eid,
                            'oid'       => $oid,
                            'uid'       => $formData['uid'],
                            'pid'       => $formData['pid'],
                            'escid'     => $formData['escid'],
                            'subid'     => $formData['subid'],
                            'perid'     => $formData['perid'],
                            'type'      => 'SE',
                            'amount'  => $formData['nsem'],
                            'createduid' => $uidreg,
                            'doc_authorize' => $formData['resolution']);
                    $savedata = $conditiondb->_save($data);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  
                }
            $where_condition = array(
                                    'eid' => $eid,
                                    'oid' => $oid,
                                    'uid' => $formData['uid'],
                                    'pid' => $formData['pid'],
                                    'escid' => $formData['escid'],
                                    'subid' => $formData['subid'],
                                    'perid' => $formData['perid']);
            $attrib = null;
            $order = null;
            $condition_student = $conditiondb->_getFilter($where_condition, $attrib, $order);
            $cursodb = new Api_Model_DbTable_Course();
            $i=0;
            $datacurso=null;
            foreach ($condition_student as $data) {
                if($data['courseid']){
                    $wherecourse=array('eid'=>$eid,'oid'=>$oid,'courseid'=>$data['courseid'],'curid'=>$formData['curid'],'escid'=>$formData['escid'],'subid'=>$formData['subid']);
                    $datacurso[$i]= $cursodb->_getOne($wherecourse);
                }
                $i++;
            }
            $this->view->conditionsStudent = $condition_student;
            $this->view->datacurso=$datacurso;
            }
        } catch(Exeption $e){
            print("Error al Guardar datos: ").$e->getMessage();
        }
    }

   public function listcourseAction()
      {
        try{
            $this->_helper->getHelper('layout')->disableLayout();
            $where['eid']=$this->sesion->eid;
            $where['oid']=$this->sesion->oid;
            $where['uid']=$this->_getParam("uid");
            $this->view->uid=$where['uid']; 
            $where['perid']=$this->_getParam("perid");
            $this->view->perid=$where['perid']; 
            $where['escid']=$this->_getParam("escid");
            $this->view->escid=$where['escid']; 
            $where['pid']=$this->_getParam("pid");
            $this->view->pid=$where['pid']; 
            $where['subid']=$this->_getParam("subid");
            $this->view->sedid=$where['subid']; 
            $dbcurricula=new Api_Model_DbTable_Studentxcurricula();
            $datcur=$dbcurricula->_getOne($where);
            $where['curid']=$datcur['curid'];
            $this->view->curid=$where['curid']; 

            $request = array( 'uid'   => base64_encode($where['uid']), 
                              'pid'   => base64_encode($where['pid']), 
                              'escid' => base64_encode($where['escid']),
                              'subid' => base64_encode($where['subid']),
                              'eid'   => base64_encode($where['eid']),
                              'oid'   => base64_encode($where['oid']),
                              'perid' => base64_encode($where['perid']),
                              'curid' => base64_encode($where['curid'] ) );

            $server = new Eundac_Connect_Api('delete_course', $request);
            $data = $server->connectAuth();
            $this->view->cursos = $data;
          }   
                  
         catch (Exception $ex)
          {
            print "Error : listar course".$ex->getMessage();
          }
      }


          public function deleteAction()
      {
        try{
            $this->_helper->getHelper('layout')->disableLayout();
            $where['eid']=$this->sesion->eid;
            $where['oid']=$this->sesion->oid;
            if ($this->getRequest()->isPost()){
                $formData = $this->getRequest()->getPost();
                $where['uid']=$formData["uid"];
                $where['perid']=$formData["perid"];
                $where['escid']=$formData["escid"];
                $where['pid']=$formData["pid"];
                $where['subid']=$formData["subid"];
                $where['type']=$formData["type"];
                $condiciondb = new Api_Model_DbTable_Conditionstudent();
                $condiciondb->_delete($where);
            }
                    
          }   
                  
         catch (Exception $ex)
          {
            print "Error : eliminar".$ex->getMessage();
          }
      }

}