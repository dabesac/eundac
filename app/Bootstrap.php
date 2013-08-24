<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initViewHelpers() {
            $this->bootstrap('layout');
            $layout = $this->getResource('layout');
            $view = $layout->getView();
            $view->doctype('XHTML1_STRICT');
            $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
            
            $view->headLink()->prependStylesheet('/external/bootstrap/css/bootstrap.min.css')
            ->headLink()->appendStylesheet('/external/bootstrap/css/bootstrap-theme.min.css')
            ->headLink()->appendStylesheet('/external/jquery/themes/redmond/jquery-ui.css');
            //->headLink()->appendStylesheet('/css/style.css');
            
            $view->headScript()->prependFile('/external/jquery/jquery-1.9.1.js')
            ->headScript()->appendFile('/external/jquery/additional-methods.min.js')
            ->headScript()->appendFile('/external/jquery/jquery.validate.min.js')
            ->headScript()->appendFile('/external/jquery/messages_es.js')
            ->headScript()->appendFile('/external/jquery/ui/minified/i18n/jquery.ui.datepicker-es.min.js')
            ->headScript()->appendFile('/external/jquery/ui/minified/jquery-ui.custom.min.js')
            ->headScript()->appendFile('/external/bootstrap/js/bootstrap.min.js')
            ->headScript()->appendFile('/external/jquery/external/base64/jquery.base64.min.js');
            
            
            $view->headTitle()->setSeparator(' - ');
            $view->headTitle('e-UNDAC - Sistema Academico UNDAC 2.0');
            Zend_Session::start();
            Zend_Layout::startMvc(APPLICATION_PATH . '/layouts/scripts');
            $view = Zend_Layout::getMvcInstance()->getView();
            $viewRenderer = Zend_controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
            $viewRenderer->setView($view);
            $moneda = new Zend_Locale('es_PE');
            Zend_Registry::set('Zend_Locale', $moneda);
            return;
        }

    protected function _initDbAdaptersToRegistry()
	{
		$this->bootstrap('multidb');
        $resource = $this->getPluginResource('multidb');
        $resource->init();
        $Adapter1 = $resource->getDb('db1');
        $Adapter2 = $resource->getDb('db2');
        $Adapter3 = $resource->getDb('db3');
        Zend_Registry::set('Adaptador1', $Adapter1);
        Zend_Registry::set('Adaptador2',$Adapter2);
        Zend_Registry::set('Adaptador3',$Adapter3);
	}

	
            
}

