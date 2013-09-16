<?php 
class Admin_UserController extends Zend_Controller_Action{

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
 			print "Error: User".$e->getMessage();
 		}
	}

	public function getuserAction(){
 		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$pid=$this->_getParam('pid');
			if ($pid) {
				$where=array('eid'=>$eid,'oid'=>$oid,'pid'=>$pid);
				$dbuser=new Api_Model_DbTable_Users();
				$datauser=$dbuser->_getUserXPid($where);
			}
			$name = $this->_getParam('name');
       		if($name){
        		$eid = $this->sesion->eid;
        		$oid = $this->sesion->oid;
        		$name = trim(strtoupper($name));
        		$name = mb_strtoupper($name,'UTF-8');
        		$dbuser=new Api_Model_DbTable_Users();
        		$datauser=$dbuser->_getUserXnameXsinRolAll($name,$eid,$oid);
				// print_r($datauser);exit();        		           
        	}
        	$uid = $this->_getParam('uid');
        	if ($uid) {
        		$where['uid'] = $uid;
        		$where['eid'] = $this->sesion->eid;
        		$where['oid'] = $this->sesion->oid;
        		$bduser = new Api_Model_DbTable_Users();
        		$datauser = $bduser->_getUserXUid($where);       					
        	}
            $c=0;
            $whered=array('eid'=>$eid,'oid'=>$oid);
            $wheres=array('eid'=>$eid,'oid'=>$oid);
            foreach ($datauser as $info) {
                $rid=$info['rid'];
                $subid=$info['subid'];
                $escid=$info['escid'];
                $wheres['subid']=$subid;
                $wheres['escid']=$escid;
                $whered['rid']=$rid;
                $dbrol=new Api_Model_DbTable_Rol();
                $inforol[$c]= $dbrol->_getOne($whered);
                $dbesc= new Api_Model_DbTable_Speciality();
                $infoesc[$c]= $dbesc->_getOne($wheres);
                $info[$c]=$inforol['rid'];
                $c++;
            }
            // print_r($datauser);
            $this->view->datauser=$datauser;
            $this->view->infoesc=$infoesc;
            $this->view->inforol=$inforol;			
 		} catch (Exception $e) {
 			print "Error: get User".$e->getMessage();
 		}
 	}

 	public function newAction(){
 		try {
 			$fm= new Admin_Form_Buscar();
 			$this->view->fm=$fm;
 			
 		} catch (Exception $e) {
 			print "Error: User new".$e->getMessage();
 		}
 	}

 	public function getusernewAction(){
 		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$pid=$this->_getParam('pid');
			if ($pid) {
				$where=array('eid'=>$eid,'oid'=>$oid,'pid'=>$pid);
				$dbuser=new Api_Model_DbTable_Users();
				$datauser=$dbuser->_getUserXPid($where);
                $c=0;
                $whered=array('eid'=>$eid,'oid'=>$oid);
                $wheres=array('eid'=>$eid,'oid'=>$oid);
                foreach ($datauser as $info) {
                    $rid=$info['rid'];
                    $subid=$info['subid'];
                    $escid=$info['escid'];
                    $wheres['subid']=$subid;
                    $wheres['escid']=$escid;
                    $whered['rid']=$rid;
                    $dbrol=new Api_Model_DbTable_Rol();
                    $inforol[$c]= $dbrol->_getOne($whered);
                    $dbesc= new Api_Model_DbTable_Speciality();
                    $infoesc[$c]= $dbesc->_getOne($wheres);
                    $info[$c]=$inforol['rid'];
                    $c++;
                }
				// print_r($datauser);
                $this->view->infoesc=$infoesc;
                $this->view->inforol=$inforol;
				$this->view->datauser=$datauser;
			}			
 		} catch (Exception $e) {
 			print "Error: get Person".$e->getMessage();
 		}
 	}
 	public function newuserAction(){
 	 		$this->_helper->layout()->disableLayout();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=base64_decode($this->_getParam('pid'));
            $fm= new Admin_Form_Usernew();
 	 		$this->view->fm=$fm;
            $escid=new Zend_Form_Element_Select('escid');
            $escid->removeDecorator('Label')->removeDecorator('HtmlTag');
            $escid->setAttrib('class','form-control');
            $escid->setRequired(true)->addErrorMessage('Este campo es requerido');
            $escid->setAttrib('title','Seleccione una escuela');
            $escid->addMultiOption("",'- Seleccione una Sede -');
            $this->view->escid=$escid;
            $register=$this->sesion->uid;
            if ($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();
                if ($fm->isValid($frmdata))
                {                    
                    unset($frmdata['Guardar']);
                    $rid=$frmdata['rid'];
                    $frmdata['eid']=$eid;
                    $frmdata['oid']=$oid;
                    $frmdata['pid']=$pid;
                    $where=array('eid'=>$eid,'oid'=>$oid,'rid'=>$rid);
                    $dbrol=new Api_Model_DbTable_Rol();
                    $datarol=$dbrol->_getOne($where);
                    $prefix=$datarol['prefix'];                
                    $frmdata['uid']=$pid.$prefix;                
                    $frmdata['created']=date('Y-m-d h:m:s');
                    $frmdata['register']=$register;
                    $frmdata['password']=md5($frmdata['uid']);                  
                    $reg_= new Api_Model_DbTable_Users();
                    $reg_->_save($frmdata);
                    $this->_redirect("/admin/user/new");                           
                }
                else
                {
                    echo "Ingrese nuevamente por favor";
                }
            }
 	}

 	public function filterspecialityAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $subid = $this->_getParam('subid');
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $where=array('eid'=>$eid,'oid'=>$oid,'subid'=>$subid);
            $attrib=array('escid','name','state');
            $dbesc = new Api_Model_DbTable_Speciality();
            $data = $dbesc->_getFilter($where);
            $this->view->data = $data;         
        }catch (Exception $ex){
            print "Error : get Filter".$ex->getMessage();
        }
    }


    public function updateuserAction(){
        try {
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid = base64_decode($this->_getParam('uid'));
            $pid = base64_decode($this->_getParam('pid'));
            $escid = base64_decode($this->_getParam('escid'));
            $subid = base64_decode($this->_getParam('subid'));
            $where=array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid,'escid'=>$escid,'subid'=>$subid);
            $attrib=array('uid','escid','subid','pid','rid','state','comments');
            $dbuser = new Api_Model_DbTable_Users();
            $fm= new Admin_Form_Usernew();
            $data = $dbuser->_getFilter($where,$attrib);
            $rid = $data[0]['rid'];
            $this->view->rid=$rid;
            $fm->populate($data[0]);
            $fm->rid->setAttrib('disable',true);
            $this->view->fm=$fm;
            if ($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();
                if ($fm->isValid($frmdata))
                {                    
                    unset($frmdata['Actualizar']);
                    trim($frmdata['comments']);
                    $pk['eid']=$eid;
                    $pk['oid']=$oid;
                    $pk['pid']=$pid;
                    $pk['escid']=$escid;
                    $pk['subid']=$subid;
                    $pk['uid']=$uid;                                               
                    $reg_= new Api_Model_DbTable_Users();
                    $reg_->_update($frmdata,$pk);
                    $this->_redirect("/admin/user");                           
                }
                else
                {
                    echo "Ingrese nuevamente por favor";
                }
            }


        } catch (Exception $e) {
            print "Error: update User".$ex->getMessage();
        }
    }
}    
