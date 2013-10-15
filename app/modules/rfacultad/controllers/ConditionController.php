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


           $bdu = new Api_Model_DbTable_Condition();        
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

            if ($this->getRequest()->isPost())
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
            $this->view->form=$form;
        }
        catch(Exception $ex )
        {
            print ("Error Controlador Mostrar Datos en detalles: ".$ex->getMessage());
        } 
    }


          public function listcourseAction()
      {
        try{
                     $this->_helper->getHelper('layout')->disableLayout();
                     $where['eid']=$this->sesion->eid;
                     $where['oid']=$this->sesion->oid;
                     $where['uid']=$this->_getParam("uid");
                     $this->view->uid=$uid; 
                     $where['perid']=$this->_getParam("perid");
                     $this->view->perid=$perid; 
                     $where['escid']=$this->_getParam("escid");
                     $this->view->escid=$escid; 
                     $where['pid']=$this->_getParam("pid");
                     $this->view->pid=$pid; 
                     $where['subid']=$this->_getParam("subid");
                     $this->view->sedid=$sedid; 
                     $dbcurricula=new Api_Model_DbTable_Studentxcurricula();
                     $datcur=$dbcurricula->_getOne($where);
                     $where['curid']=$datcur['curid'];
                     $this->view->curid=$curid; 

                    require_once 'Zend/Loader.php';
                    Zend_Loader::loadClass('Zend_Rest_Client');
                     $base_url = 'http://localhost:8080/';
                     $endpoint = '/s1st3m4s/und4c/delete_course';
                     $data = array('uid' => $where['uid'], 'pid' => $where['pid'], 'escid' => $where['escid'],'subid' =>$where['subid'],'eid' =>$where['eid'],'oid' =>$where['oid'],'perid'=>$where['perid'],'curid'=>$where['curid']);
                     $client = new Zend_Rest_Client($base_url);
                     $httpClient = $client->getHttpClient();
                     $httpClient->setConfig(array("timeout" => 680));
                     $response = $client->restget($endpoint,$data);
                     $lista=$response->getBody();
                      // print_r($lista);
                     $data = Zend_Json::decode($lista);
                     $this->view->cursos=$data; 

                     // print_r($data);                 
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
                        $where['uid']=$this->_getParam("uid");
                        $where['perid']=$this->_getParam("perid");
                        $where['escid']=$this->_getParam("escid");
                        $where['cnid']=$this->_getParam("cnid");
                        $where['pid']=$this->_getParam("pid");
                        $where['subid']=$this->_getParam("subid");
                        $condicion = new Api_Model_DbTable_Studentcondition();
                        $condi= new Api_Model_DbTable_Condition();
                        $condicion->_delete($where);
                        $condi->_delete($where);
                        if ($condi && $condicion) { ?>
                               <script type="text/javascript">
                            window.location.reload();
                               </script>
                         <?php }
          }   
                  
         catch (Exception $ex)
          {
            print "Error : eliminar".$ex->getMessage();
          }
      }

}