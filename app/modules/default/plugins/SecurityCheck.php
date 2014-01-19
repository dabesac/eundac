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
           if(!$this->_acl->isAllowed($this->_role, $resource_tmp)){  
                $request->setModuleName('default')
                        ->setControllerName('error') 
                        ->setActionName('error');
            }
        }
        else{
             $request->setModuleName('default')
                        ->setControllerName('index') 
                        ->setActionName('index');
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
            $eid= $auth->getStorage()->read()->eid;
            $oid= $auth->getStorage()->read()->oid;
            $this->_acl->addRole(new Zend_Acl_Role($this->_role));

            $params = array('eid'=>$eid,'oid'=>$oid);
            $resourceTable = new Default_Model_DbTable_Resource();
            $resources = $resourceTable->_getFilter($params);
            
            $premissionsTable = new Default_Model_DbTable_Premission();
            $premissions = $premissionsTable->_getResource_Role($eid,$oid,$this->_role);
            
            if ($resources) {
                    foreach ($resources as $key => $resource) {
                    $resourceToken = "{$resource['module']}/{$resource['controller']}/{$resource['action']}";
                    $this->_acl->add(new Zend_Acl_Resource($resourceToken));
                    foreach ($premissions as $key => $premission) {

                        if ($premission['permission'] == 'allow') {
                           $this->_acl->allow($this->_role, $resourceToken);
                        }
                        else{
                           $this->_acl->deny($this->_role, $resourceToken);
                        }
                    }
                }
            }
        }
    }
}