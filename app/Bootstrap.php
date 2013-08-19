<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initViewHelpers() {
            $this->bootstrap('layout');
            $layout = $this->getResource('layout');
            $view = $layout->getView();
            $view->doctype('XHTML1_STRICT');
            $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
            //$view->headMeta()->appendHttpEquiv('Cache-Control', 'no-cache');
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

