<?php
class Admin_PersonController extends Zend_Controller_Action{

	public function init(){
 		$sesion  = Zend_Auth::getInstance();
 		if(!$sesion->hasIdentity() ){
 			$this->_helper->redirector('index',"index",'default');
 		}
 		$login = $sesion->getStorage()->read();
 		$this->sesion = $login;
 	}

 	public function indexAction(){
 		try {
 			$fm=new Admin_Form_Buscar();
			$this->view->fm=$fm;
 		} catch (Exception $e) {
 			print "Error: Person".$e->getMessage();
 		}
 	}

 	public function getpersonAction(){
 		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$pid=$this->_getParam('pid');
			if ($pid) {
				$where=array('eid'=>$eid,'pid'=>$pid);
				$dbperson=new Api_Model_DbTable_Person();
				$dataperson[0]=$dbperson->_getOne($where);
				// print_r($dataperson);exit();
				$this->view->dataperson=$dataperson;
			}
			$name = $this->_getParam('name');
       		if($name){
        		$eid = $this->sesion->eid;
        		$name = trim(strtoupper($name));
        		$name = mb_strtoupper($name,'UTF-8');
        		$bdp=new Api_Model_DbTable_Person();
        		$dataperson=$bdp->_getPersonxname($name,$eid);
				// print_r($dataperson);exit();        		           
            	$this->view->dataperson=$dataperson; 
        	}			
 		} catch (Exception $e) {
 			print "Error: get Person".$e->getMessage();
        }
    }

    public function newAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $register=$this->sesion->uid;
            $user=$this->_getParam('u');
            $rcentral=$this->_getParam('r');
            $fm=new Admin_Form_Personnew();
            $this->view->fm=$fm;
            if ($this->getRequest()->isPost()){   

                $frmdata=$this->getRequest()->getPost();

                if ($fm->isValid($frmdata)){                    
                    
                    $this->view->pid=$frmdata['pid'];
                    unset($frmdata['Guardar']);
                    trim($frmdata['last_name0']);
                    trim($frmdata['last_name1']);
                    trim($frmdata['first_name']);
                    $frmdata['last_name0']=strtoupper($frmdata['last_name0']);
                    $frmdata['last_name1']=strtoupper($frmdata['last_name1']);
                    $frmdata['first_name']=ucwords($frmdata['first_name']);
                    $frmdata['eid']=$eid;
                    $frmdata['location']='-';
                    $frmdata['created']=date('Y-m-d h:m:s');
                    $frmdata['register']=$register;
                    $frmdata['birthday']=date('Y-m-d',strtotime($frmdata['birthday']));
                    $reg_= new Api_Model_DbTable_Person();
                    if ($frmdata['user']=='A') {
                        $this->view->user=2;
                    }
                    if ($frmdata['rcentral']=='A') {
                        $this->view->rcentral=3;
                    }
                    unset($frmdata['user']);
                    unset($frmdata['rcentral']);

                    if ($reg_->_save($frmdata)) {
                        $this->view->valor=1;
                    }
                }
            }
            else{
                if ($user=='A') {
                    $this->view->user=$user;                    
                }
                if ($rcentral=='A') {
                    $this->view->rcentral=$rcentral;                    
                }
            }
            
        } catch (Exception $e) {
            print "Error: new Person".$e->getMessage();
        }
    }

    public function updatepersonAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $modified=$this->sesion->uid;
            $pid=base64_decode($this->_getParam('pid'));
            $where=array('eid'=>$eid,'pid'=>$pid);
            $dper=new Api_Model_DbTable_Person();
            $data=$dper->_getOne($where);
            $fm=new Admin_Form_Personnew();
            $fm->populate($data);
            $fm->pid->setAttrib('readonly',true);
            if ($this->getRequest()->isPost()) {
                $frmdata=$this->getRequest()->getPost();
                $frmdata['pid']=base64_decode($frmdata['pid']);
                if ($fm->isValid($frmdata)) {
                    $this->view->pid=$frmdata['pid'];
                    unset($frmdata['Actualizar']);
                    trim($frmdata['last_name0']);
                    trim($frmdata['last_name1']);
                    trim($frmdata['first_name']);
                    trim($frmdata['address']);
                    $frmdata['updated']=date('Y-m-d h:m:s'); 
                    $frmdata['modified']=$modified;
                    $frmdata['birthday']=date('Y-m-d',strtotime($frmdata['birthday']));
                    $pk['eid']=$eid;
                    $pk['pid']=$pid;                   
                    $reg_= new Api_Model_DbTable_Person();
                    if ($reg_->_update($frmdata,$pk)) {
                        $this->view->clave=3;
                    }
                    // $this->_redirect("/admin/person/");
                }
                else
                {
                    echo "Ingrese nuevamente por favor";
                }
            }
            else{
                $this->view->pid=$pid;
            }         
            $this->view->fm=$fm;

        } catch (Exception $e) {
            print "Error: update Person ".$e->getMessage();
        }
    }

}