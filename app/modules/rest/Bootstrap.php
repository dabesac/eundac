<?php

class Rest_Bootstrap extends Zend_Application_Module_Bootstrap 
{
    
    protected function _initAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Rest_',
            'basePath'  => APPLICATION_PATH .'/modules/rest',
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
        return $autoloader;
    }

    
}

