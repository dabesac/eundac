<?php

class PasswordController extends Zend_Controller_Action{

	public function init(){
		$sesion  = Zend_Auth::getInstance();
      if(!$sesion->hasIdentity() ){
        $this->_helper->redirector('index',"index",'default');
      }
      $login = $sesion->getStorage()->read();
      
      $this->sesion = $login;
		
	}

	public function changeAction(){
		try{
            //$this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;        
            $oid=$this->sesion->oid; 
            $uid=$this->sesion->uid; 
            $pid=$this->sesion->pid;
		       	$rid=$this->sesion->rid;  
            $escid=$this->sesion->escid; 
            $subid=$this->sesion->subid;
            $fm=new Admin_Form_Keychange();
            $this->view->fm=$fm;
            $fm->guardar->setLabel("Guardar");
			
            $antclave = new Api_Model_DbTable_Users();
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['uid']=$uid;
            $where['escid']=$escid;

            $dantclave = $antclave->_getFilter($where);
            $pass=$dantclave[0]['password'];
             
            
             if ($this->getRequest()->isPost())
            {
                $formData = $this->getRequest()->getPost();
       
                unset($formData["guardar"]);

                if(md5($formData["acla"])==$pass)
                {

                    if (($formData["ncla"]==($formData["rcla"])) && ($formData["ncla"]<>"" || $formData["rcla"]<>""))

                    {
                      
                      $data['password']=md5($formData["ncla"]);
                      $pk['uid']=$uid;
                      $pk['eid']=$eid;
                      $pk['oid']=$oid;
                      $pk['pid']=$pid;
                      $pk['escid']=$escid;
                      $pk['subid']=$subid;
                      $bdu = new Api_Model_DbTable_Users();
                    //print($uid.$pid.$eid.$oid.$escid.$pass);
                     $datos = $bdu->_update($data,$pk);           
                     if ($datos)
                     {
                        $this->view->mensaje="Contraseña guadada correctamente";
                           
                        
                     }
                    }
                    else
                    {
                      $this->view->mensaje="Contraseñas no coinciden";

                    }


                }
                else
                { $this->view->mensaje="La contraseña anterior es incorrecta";


                }

            }

          }
        catch (Exception $ex) 
            {
                print "Error al momento de modificar la clave de Usuario: ".$ex->getMessage();
            }
    }

}
