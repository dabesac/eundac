<?php

class Profile_Bootstrap extends Zend_Application_Module_Bootstrap 
{
    
   protected function _initAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Bienestar_',
            'basePath'  => APPLICATION_PATH .'/modules/profile',
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

