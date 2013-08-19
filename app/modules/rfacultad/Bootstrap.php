<?php

class Rfacultad_Bootstrap extends Zend_Application_Module_Bootstrap 
{
    
   protected function _initAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Rfacultad_',
            'basePath'  => APPLICATION_PATH .'/modules/rfacultad',
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

