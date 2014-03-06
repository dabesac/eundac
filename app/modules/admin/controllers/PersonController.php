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
 			$fm=new Admin_Form_Personnew();
 			$this->view->fm=$fm;
 			if ($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();
                
                if ($fm->isValid($frmdata))
                {                    
                    $this->view->pid=$frmdata['pid'];
                    unset($frmdata['Guardar']);
                    trim($frmdata['last_name0']);
                    trim($frmdata['last_name1']);
                    trim($frmdata['first_name']);
                    strtoupper($frmdata['last_name0']);
                    strtoupper($frmdata['last_name1']);
                    $frmdata['eid']=$eid;
                    $frmdata['location']='-';
                    $frmdata['created']=date('Y-m-d h:m:s');
                    $frmdata['register']=$register;                  
                    $reg_= new Api_Model_DbTable_Person();                          
                    if ($reg_->_save($frmdata)) {
                        $this->view->valor=1;
                    }
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