<?php

class Default_Bootstrap extends Zend_Application_Module_Bootstrap 
{
    
   protected function _initAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Default_',
            'basePath'  => APPLICATION_PATH .'/modules/default',
            'resourceTypes' => array (
                'form' => array(
                    'path' => 'forms',
                    'namespace' => 'Form',
                ),
                'model' => array(
                    'path' => 'models',
                    'namespace' => 'Model',
                ),
            )
        ));

        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Default_Plugin_SecurityCheck()); 
        
        return $autoloader;
    }

    public function _initRouter(){
        
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $router->addRoute('login',
                    new Zend_Controller_Router_Route('login',
                            array(
                                'module'     => 'default',
                                'controller' => 'index',
                                'action'     => 'index'
                                )
                            )
            );
        $router->addRoute('logout',
                    new Zend_Controller_Router_Route('logout',
                            array(
                                'module'     => 'default',
                                'controller' => 'index',
                                'action'     => 'salir'
                                )
                            )
            );
        $router->addRoute('passwodorchange',
                    new Zend_Controller_Router_Route('passwodorchange',
                            array(
                                'module'     => 'default',
                                'controller' => 'password',
                                'action'     => 'change'
                                )
                            )
            );
    }
    
}

