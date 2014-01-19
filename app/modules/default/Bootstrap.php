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

    public function _initACL()
    {           
        
    }
    
}

