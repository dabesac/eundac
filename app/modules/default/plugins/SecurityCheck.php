<?php

class Default_Plugin_SecurityCheck extends Zend_Controller_Plugin_Abstract{

    /**
     * @var Zend_Acl object
     *
     */
    private $_acl = null;
    const MODULE_NO_AUTH='default';
    private $_controller;
    private $_module;
    private $_action;
    private $_role;
    private $_parent;
    public function __construct(){
        $this->_iniAcl();
    }

    public function preDispatch (Zend_Controller_Request_Abstract $request){

        $this->_module= $this->getRequest()->getModuleName();
        $this->_controller = $this->getRequest()->getControllerName();
        $this->_action= $this->getRequest()->getActionName();

        $resource_tmp = "{$this->_module}/{$this->_controller}/{$this->_action}";

        $auth= Zend_Auth::getInstance();

        if ($auth->hasIdentity()) {
            if (!$this->_acl->isAllowed($this->_role,$resource_tmp)) {
                $request->setModuleName('default')
                        ->setControllerName('error')
                        ->setActionName('error');
            }
        }else{
            if ($this->_module=='default' && $this->_controller=='index') {
                if ($this->_action=='recoverpassword' || $this->_action=='showtoken' || $this->_action=='verificationtoken' ||
                    $this->_action=='showpassword' || $this->_action=='passwordsave') {

                    $request->setModuleName($this->_module)
                            ->setControllerName($this->_controller)
                            ->setActionName($this->_action);
                }
                else{
                    $request->setModuleName('default')
                            ->setControllerName('index')
                            ->setActionName('index');                
                }
            }
            else{
                $request->setModuleName('default')  
                        ->setControllerName('index')
                        ->setActionName('index');                
            }
        }
    }

    /**
     * @param Zend_Acl $acl
     * @return boolean
     */

    public function _iniAcl(){
        $this->_acl = new Zend_Acl();
        $auth= Zend_Auth::getInstance();

        if ($auth->hasIdentity()) {
            $this->_role= $auth->getStorage()->read()->rid;
            $this->_parent = $auth->getStorage()->read()->rol['parent'];
            $eid= $auth->getStorage()->read()->eid;
            $oid= $auth->getStorage()->read()->oid;
            $this->_acl->addRole(new Zend_Acl_Role($this->_role));
            $logout = "default/index/salir";
            $error = "default/error/error";
            $msg = "error/msg/msg";
            $this->_acl->add(new Zend_Acl_Resource($logout));
            $this->_acl->add(new Zend_Acl_Resource($error));
            $this->_acl->add(new Zend_Acl_Resource($msg));
            $this->_acl->allow($this->_role, $logout);
            $this->_acl->allow($this->_role, $error);
            $this->_acl->allow($this->_role, $msg);

            if (!isset($this->_parent)) {
                $permissionTable = new Default_Model_DbTable_Premission();
                $permission = $permissionTable->_getResource_Role($eid,$oid,$this->_role);
            }else{
                $permissionTable = new Default_Model_DbTable_Premission();
                $permission = $permissionTable->_getResource_Role_Parent($eid,$oid,$this->_role,$this->_parent);
            }
            if ($permission) {
                foreach ($permission as $key => $access ) {
                    $resourceToken = "{$access['module']}/{$access['controller']}/{$access['action']}";
                    $this->_acl->add(new Zend_Acl_Resource($resourceToken));
                    if ($access['permission'] == 'allow') {
                       $this->_acl->allow($this->_role, $resourceToken);
                    }
                    if ($access['permission'] == 'deny') {
                       $this->_acl->deny($this->_role, $resourceToken);
                    }
                }
            }
        }
    }
}