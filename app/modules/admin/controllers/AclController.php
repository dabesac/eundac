<?php
class Admin_AclController extends Zend_Controller_Action{

	public function init(){
       	$sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
		$this->eid=$login->eid;
		$this->oid=$login->oid;
	}

	public function indexAction() 
    {   
        try 
        {
            $eid=$this->eid;
            $oid=$this->oid;
            $dbrol=new Api_Model_DbTable_Module();
            $where=array("eid"=>$eid,"oid"=>$oid);
            $order=array("name");
            $modules=$dbrol->_getAll($where,$order);
            $this->view->modules=$modules;

            //echo md5('1234');
        } 
        catch (Exception $ex) 
        {
            print "Error listando al Crear Roles: ".$ex->getMessage();
        }

    }
    
    public function newAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $form= new Admin_Form_Module();
            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();
                if ($form->isValid($data)) {
                    $data['eid']=$this->sesion->eid;
                    $data['oid']=$this->sesion->oid;
                    $data['mid']= strtotime(date('Y-m-d h:m:s'));
                    $data['state']='A';
                    unset($data['save']);
                    $tb_module = new Api_Model_DbTable_Module(); 
                    if ($tb_module->_save($data)) {
                         $this->_helper->_redirector('index');
                    }
                }
            }
            $this->view->form=$form;
        }catch(exception $e){
            print ("Error : ").$e->getMessage();
        }
    }

    public function editAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            
            $form= new Admin_Form_Module();
            $state = new Zend_Form_Element_Select('state');
            $state->setAttrib("class","form-control");
            $state->setAttrib("required","true");
            $state->setRequired(true)->addErrorMessage('Campo Obligatorio');
            $state->removeDecorator("HtmlTag")->removeDecorator("Label");
            $state->addMultiOption('A','Activo');
            $state->addMultiOption('B','Desactivo');
            $form->addElement($state);
            $t_mid = new Zend_Form_Element_Hidden('mid');
            $form->addElement($t_mid);
            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();
                if ($form->isValid($data)) {
                    $p_key = array(
                                    'eid'=>$this->sesion->eid,
                                    'oid'=>$this->sesion->oid,
                                    'mid'=>$data['mid'],
                                    );
                    unset($data['save']);
                    unset($data['mid']);
                    $tb_module = new Api_Model_DbTable_Module(); 
                    if ($tb_module->_update($data,$p_key)) {
                        $this->_response->setHeader('Content-Type', 'application/json');
                        $json = array('status'=> true);                   
                        $this->view->json = Zend_Json::encode($json);
                    }

                }else{
                    $this->view->form=$form;
                    $form->populate($data);
                }
            }
            else{
                $params = base64_decode($this->_getParam('mid'));
                $where =    array(
                                'eid' => $this->sesion->eid,
                                'oid' => $this->sesion->oid,
                                'mid' => trim($params),
                                );
                $tb_module = new Api_Model_DbTable_Module();
                $module = $tb_module->_getOne($where);
                $form->mid->setValue($params);
                $form->populate($module);
                $this->view->form=$form;

            }
        } 
        catch (Exception $ex) 
        {
            print "Error listando al Crear Roles: ".$ex->getMessage();
        }


    }

    public function listresourceAction(){
        $params = $this->getRequest()->getParams();
        if(count($params) > 3){

            $paramsdecode = array();
            
            foreach ( $params as $key => $value ){
                if($key!="module" && $key!="controller" && $key!="action"){
                    $paramsdecode[base64_decode($key)] = base64_decode($value);
                }
            }
            $params = $paramsdecode;
        }
        $where =    array(
                        'eid'   =>   $this->sesion->eid,
                       'oid'   =>   $this->sesion->oid,
                        'mid'   =>   trim($params['mid']),
                        );
        $tb_resourse =  new Api_Model_DbTable_Resource();
        $resources = $tb_resourse->_getFilter($where);
        $this->view->resources = $resources;
        $this->view->mid = trim($params['mid']);
    }

    public function newresourceAction()
    {
        $this->_helper->layout()->disableLayout();

        $form = new Admin_Form_Resource();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $data['eid']=$this->sesion->eid;
                $data['oid']=$this->sesion->oid;
                $data['state']= 'A';
                $data['created']=date('Y-m-d h:m:s');
                $tb_resourse =  new Api_Model_DbTable_Resource();
                if ($tb_resourse->_save($data)) {
                        $this->_response->setHeader('Content-Type', 'application/json');
                        $json = array('status'=> true);                   
                        $this->view->json = Zend_Json::encode($json);
                    }
            }
            else{
                $form->populate($data);
                $this->view->form=$form;
            }
        }else{
            $mid = base64_decode($this->_getParam('mid'));
            $form->mid->setValue($mid);
            $this->view->form=$form;
        }
    }
    public  function editresourceAction()
    {
        $this->_helper->layout()->disableLayout();
        $tb_resourse =  new Api_Model_DbTable_Resource();
        $form = new Admin_Form_Resource();
        $elemen_reid = new Zend_Form_Element_Hidden('reid');
        $form->addElement($elemen_reid);
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $p_key['eid']=$this->sesion->eid;
                $p_key['oid']=$this->sesion->oid;
                $p_key['mid']=$data['mid'];
                $p_key['reid']=$data['reid'];
                unset($data['mid']);
                unset($data['reid']);
                if ($tb_resourse->_update($data,$p_key)) {
                        $this->_response->setHeader('Content-Type', 'application/json');
                        $json = array('status'=> true);                   
                        $this->view->json = Zend_Json::encode($json);
                    }
            }
            else{
                $form->populate($data);
                $this->view->form=$form;
            }
        }else{
            $mid = base64_decode($this->_getParam('mid'));
            $reid = base64_decode($this->_getParam('reid'));
            $where =    array(
                            'eid'=>$this->sesion->eid,
                            'oid'   =>$this->sesion->oid,
                            'reid'  =>$reid );
            $resource = $tb_resourse->_getOne($where);
            $form->mid->setValue($mid);
            $form->reid->setValue($reid);
            $form->populate($resource);
            $this->view->form=$form;
        }
    }

    public function editpremissiosAction(){
        try {

            $this->_helper->layout()->disableLayout();


            $mid = trim(base64_decode($this->_getParam('mid')));
            $reid = trim(base64_decode($this->_getParam('reid')));
            $where  =   array(
                                'eid'=>$this->sesion->eid,
                                'oid'=>$this->sesion->oid,
                                'mid'=>$mid,
                                'reid'=>$reid,
                            );
            $where1 = array(
                            'eid'=>$this->sesion->eid,
                            'oid'=>$this->sesion->oid,
                            );
            $tb_rol = new Api_Model_DbTable_Rol();
            $tb_acl = new Api_Model_DbTable_Acl();

            $roles = $tb_rol->_getFilter($where1);
            $permissios = $tb_acl->_getFilter($where); 
            $count = count($roles);
            foreach ($roles as $key => $role) {
                if ($permissios) {
                        foreach ($permissios as $per) {
                        if ($role['rid'] == $per['rid']) {
                            $roles[$key]['permission']=$per['permission'];
                        }
                        else{
                            if ($count > $key+1) {
                                $roles[$key+1]['permission']='not';
                            }
                        }
                    }
                }else{
                    $roles[$key]['permission']='not';
                }
            }

            $this->view->roles =$roles;
            $this->view->mid = $mid;
            $this->view->reid = $reid;


            
        } catch (Exception $e) {
            
        }
    }
    
    public function allowresourceAction(){
        $this->_helper->layout()->disableLayout();
        $params = $this->getRequest()->getParams();
        if(count($params) > 3){

            $paramsdecode = array();
            
            foreach ( $params as $key => $value ){
                if($key!="module" && $key!="controller" && $key!="action"){
                    $paramsdecode[base64_decode($key)] = base64_decode($value);
                }
            }
            $params = $paramsdecode;
        }

        $rid    =   trim($params['rid']);
        $mid    =   trim($params['mid']);
        $reid    =   trim($params['reid']);
        $action    =   trim($params['action']);
        $permission    =   trim($params['permission']);

        try {
            $tb_acl =   new Api_Model_DbTable_Acl();
            if ((integer)$action == 1) {
            $data = array(
                        'eid'=>$this->sesion->eid,
                        'oid'=>$this->sesion->oid,
                        'mid'=>$mid,
                        'reid'=>$reid,
                        'rid'=>$rid,
                        'permission'=>$permission,
                        'state'=>'A'
                    );
                if ($tb_acl->_save($data)) {
                    $json = array(
                            'status' =>true,
                        );
                }
            }
            if ((integer)$action == 0) {
               $data = array(
                        'permission'=>$permission,
                );
                $p_key = array(
                        'eid'=>$this->sesion->eid,
                        'oid'=>$this->sesion->oid,
                        'mid'=>$mid,
                        'reid'=>$reid,
                        'rid'=>$rid,
                    );
                if ($tb_acl->_update($data,$p_key)) {
                    $json = array(
                            'status' =>true,
                        );
                }
            }
        } catch (Exception $e) {
            $json = array(
                            'status' =>true,
                            'error' =>$e->getMessage(),
                        );
        }

        $this->view->json =$json;
        $this->_response->setHeader('Content-Type', 'application/json');

    }
    

    public function deleteAction()
    
    {
        try{
            $eid=$this->eid;
            $oid=$this->oid;
            $rid=$this->_getParam("rid");
            $reid=$this->_getParam("reid");
            $mid=$this->_getParam("mid");
            $where=array("eid"=>$eid, "oid"=>$oid, "reid"=>$reid, "rid"=>$rid, "mid"=>$mid);
            //print_r($where);
            $dbdelacl=new Api_Model_DbTable_Acl();
            if($dbdelacl->_delete($where)){
                $this->_helper->_redirector("index");
            }else{
                print_r("Error al Eliminar ACL!!");
            }

        } 
        catch (Exception $ex) 
        {
            print "Error al guardar: ".$ex->getMessage();
        }
    }

    

	
}