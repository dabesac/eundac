<?php

class Admin_PasswordController extends Zend_Controller_Action 
{
    public function init() 
    {

    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	 
    	$this->sesion = $login;

		$this->eid=$login->eid;
      	$this->oid=$login->eid;
    }
    
    public function indexAction() 
    {
        $this->_helper->redirector("search");
        //echo "sdasdsad";
    }

    public function searchAction()
    {
        try
        {
          	$eid = $this->sesion->eid;
          	$oid = $this->sesion->oid;
            $fm=new Admin_Form_Password();
            $this->view->fm=$fm;
        }
        catch (Exception $ex) 
        {
          print $ex->getMessage();
        }
    }

    public function listAction() 
    {
        try 
        {   
             $this->_helper->layout()->disableLayout();
             $eid = $this->sesion->eid;
             $oid = $this->sesion->oid;
             $rid=$this->_getParam('rol');
             $uid=$this->_getParam('uid');
             $nom=$this->_getParam('nom');
             $nombre=$this->_getParam('nombre');
             $data['eid']= $eid;
             $data['oid']= $oid;
             $data['uid']= $uid;

            if ($rid=='docente')
            {
              $rid='DC';
            }
            if ($rid=='alumno')
            {
              $rid='AL';
            }
             if ($rid=='otros')
            {                             
              $rid='';
            }


            $nom=mb_strtoupper($nom, 'UTF-8');
            $bdu = new Api_Model_DbTable_Users ();
            if ($rid<>'') {
                if ($uid=='' && $nom<>'')
                {
                    $datos = $bdu->_getUsersXNombre(strtoupper($nom),$rid,$eid,$oid);


                }
                if ($uid<>'' && $nom=='')
                {
                    
                    $data['rid']= $rid;
                    // print_r($data);
                    $datos = $bdu->_getUserXRidXUid($data);
                                        
                }
                $this->view->datos=$datos;
            }
            else{

                //echo ("Debe seleccionar Docente o Alumno");
                if ($uid=='' && $nom<>'')
                {
                    $datos = $bdu->_getUsuarioXNombreXsinRol(strtoupper($nom),$eid,$oid);
                    
                } 

                if ($uid<>'' && $nom=='')
                {
                    $datos = $bdu->_getUserXUid($data);
                }
                $this->view->datos=$datos;                

            }
        }
        catch (Exception $ex) 
        {
          print "Error Buscando Usuario de acuerdo a su código o nombre: ".$ex->getMessage();
        }
    }

    public function keychangeAction() 
    {
        try 
        {   
            $this->_helper->layout()->disableLayout();
            $eid = base64_decode($this->_getParam("eid"));
            $oid = base64_decode($this->_getParam("oid"));
            $rid = base64_decode($this->_getParam("rid"));
            $uid = base64_decode($this->_getParam("uid"));
            $escid = base64_decode($this->_getParam("escid"));
            $pid = base64_decode($this->_getParam("pid"));
            $subid = base64_decode($this->_getParam("subid"));

            $where = array('eid'=>$eid,'oid'=>$oid,'rid'=>$rid,'uid'=>$uid,'escid'=>$escid,'pid'=>$pid,'subid'=>$subid);
           
            $this->view->where=$where;
            
            $fm=new Admin_Form_Keychange();
            $bdu = new Api_Model_DbTable_Users();
            $fm->guardar->setLabel("Guardar");
            $this->view->fm=$fm;

            if ($this->getRequest()->isPost()){
                $formData = $this->getRequest()->getPost();
                unset($formData["enviar"]);
                // if ($formData['acla']) {
                //     $module = $this->sesion->rol['module'];
                //     $passant=md5($formData["acla"]);
                //     $eid=$formData['eid'];
                //     $oid=$formData['oid'];
                //     $pid=$formData['pid'];
                //     $uid=$formData['uid'];
                //     $where=array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid);
                //     $datauser=$bdu->_getFilter($where);
                //     $datauser=$datauser[0];
                //     if ($datauser['password']==$passant) {
                //         if (($formData["ncla"]<>"" && $formData["rcla"]<>"")) {
                //             if (($formData["ncla"]==$formData["rcla"])) {
                //                 $password=md5($formData["ncla"]);
                //                 $data['password']=$password;
                //                 $pk['eid']=$eid;
                //                 $pk['oid']=$oid;
                //                 $pk['uid']=$uid;
                //                 $pk['pid']=$pid;
                //                 $pk['escid']=$formData['escid'];
                //                 $pk['subid']=$formData['subid'];                                
                //                 $bdu->_update($data,$pk);
                //                     // $where_=array();
                //                     // $where_ = array("username"=>$uid);
                //                     // $datac = array("password"=> $password);
                //                     // $campus = new Api_Model_DbTable_Campususer();
                //                     // $campus->_update($where_,$datac);
                    
                //                 $this->_helper->redirector('index','index',$module);
                //             }
                //             else{
                //                 $this->_helper->redirector('index','index',$module);
                //             }
                //         }
                //         else{
                //             $this->_helper->redirector('index','index',$module);
                //         }   
                //     }
                //     else{
                //         $this->_helper->redirector('index','index',$module);
                //     }

                // }
                // else{
                    if (($formData["ncla"]<>"" && $formData["rcla"]<>"")) {

                        if (($formData["ncla"]==$formData["rcla"])) {
                            $password=md5($formData["ncla"]);

                            $data['password']=$password;
                            $data['change_password']="T";
                            $pk['eid']=$eid;
                            $pk['oid']=$oid;
                            $pk['uid']=$uid;
                            $pk['pid']=$pid;
                            $pk['escid']=$escid;
                            $pk['subid']=$subid;
                            
                            $bdu = new Api_Model_DbTable_Users();
                            $veri=$bdu->_update($data,$pk);
                                // $where_=array();
                                // $where_ = array("username"=>$uid);
                                // $datac = array("password"=> $password);
                                // $campus = new Api_Model_DbTable_Campususer();
                                // $campus->_update($where_,$datac);
                            if ($veri) {
                                $this->view->mensaje=3;                                
                            }
                            
                        }
                        else{
                            $this->view->mensaje=2;
                        }
                    }
                    else{
                        $this->view->mensaje=1;
                    }
                // }
            }
        }
        catch (Exception $ex) 
        {
            print "Error al momento de modificar la clave de Usuario: ".$ex->getMessage();
        }
    }
}