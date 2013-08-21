<?php

class Rfacultad_ConditionController extends Zend_Controller_Action {

    public function init()
    {

     
    }

    public function indexAction()
    {
        try
        {

            $eid="20154605046";
            $oid="1";
            $facid="4";
            $subid="1901";
            $perid="13A";
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['facid']=$facid;
            $where['state']="A";
            $where['subid']=$subid;
            $this->view->perid=$perid;
            $this->view->subid=$subid;
            $this->view->facid=$facid;
            $esp = new Api_Model_DbTable_Speciality();
            $gesp = $esp->_getFilter($where);
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
            $perid="13A";//$this->sesion->perid;
            $eid = "20154605046";//$this->sesion->eid;        
            $oid = "1";//$this->sesion->oid; 
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
            // $this->view->listacursos=$listacursos; 
            $form=new Rfacultad_Form_Condition();
            $perid="13A";//$this->sesion->perid;
            $eid = "20154605046";//$this->sesion->eid;        
            $oid = "1";//$this->sesion->oid;
            $uidreg = " 04056889RF";// $this->sesion->uid;
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
                if (!($condi=='S' &&  $formData['nsemestre']=="" && $formData['ncreditos']=="" && $formData['vmatricula']==""))
                {
                    $formData1= $formData;
                    $bdcondiciones = new Api_Model_DbTable_Condition();  
                    if ($condi == 'C' || $condi == 'S')
                    {

                        if ($formData['nsemestre']=="") unset($formData['nsemestre']);
                        if ($formData['ncreditos']=="") unset($formData['ncreditos']);
                        if ($formData['vmatricula']=="") unset($formData['vmatricula']);
                    //     $formData['uid_reg']=$uidreg;
                    //     $formData['f_reg']=date('Y-m-d h:m:s');   
                    //     unset($formData['guardar']);  
                    //     unset($formData['listacursos']);             
                    //     $dato=$bdcondiciones->_guardar($formData);
                    //     if($listacursos){
                    //     if($dato){                      
                    //     $Data['conid']=$dato['conid'];
                    //     $Data['eid']=$eid;
                    //     $Data['oid']=$oid;
                    //     $Data['escid']=$escid;
                    //     $Data['sedid']=$sedid;
                    //     $Data['perid']=$perid;
                    //     $Data['uid']=$uid;
                    //     $Data['pid']=$pid;
                    //      $condicion = new Admin_Model_DbTable_Condicionalumnotemporal();
                    //      for ($i=0; $i < count($listacursos) ; $i++) { 
                    //     $Data['cursoid']=$listacursos[$i];
                    //     $dato=$condicion->_guardar($Data);
                    //      }

                    //     }

                    //     }                     
                    }
                $this->view->g_reg = "Se guardo Correctamente";
                }
            }
            else
            {
                $this->view->uid=$uid;
                $this->view->pid=$pid;
                $this->view->escid=$escid;
                $this->view->sedid=$sedid;

                $this->view->condicion=$condi;
                $rid='AL';

                $where['eid']=$eid;
                $where['oid']=$oid;
                $where['pid']=$pid;
                $where['escid']=$escid;
                $where['uid']=$uid;
                $where['perid']=$perid;
                $where['subid']=$subid;

                // $form->uid->setValue($uid);
                // $form->pid->setValue($pid);
                // $form->escid->setValue($escid);
                // $form->sedid->setValue($sedid);
                // $form->condi->setValue($condi);
                
                if ($condi=='C')
                {
                    $bdalumno = new Api_Model_DbTable_Condition();        
                    $datos= $bdalumno->_getFilter($where);
                    $this->view->datos=$datos;
                    $dataform = array();
                    $dataform['uid'] = $datos['uid'];
                    $dataform['pid'] = $datos['pid'];
                    $dataform['escid'] = $datos['escid'];
                    $dataform['subid'] = $datos['subid'];
                    $dataform['condi'] = $condi;                    
                    $dataform['doc_authorize'] = $datos['doc_authorize'];
                    $dataform['nsemestre'] = $datos['nsemestre'];
                    $dataform['ncreditos'] = $datos['ncreditos'];
                    $dataform['vmatricula'] = $datos['vmatricula'];
                    $dataform['comments'] = $datos['comments'];
                //     // $form->populate($dataform);
                }
            }
            $this->view->form=$form;
        }
        catch(Exception $ex )
        {
            print ("Error Controlador Mostrar Datos: ".$ex->getMessage());
        } 
    }


          public function listcourseAction()
      {
        try{
                     $this->_helper->getHelper('layout')->disableLayout();
                     $where['eid']="20154605046";//$this->sesion->eid;
                     $where['oid']='1';//$this->sesion->oid;
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
            print "Error : eliminar".$ex->getMessage();
          }
      }
}