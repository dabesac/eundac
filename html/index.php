<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../app'));


// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../lib'),
    get_include_path(),
)));

// Seteando libreria
defined('APPLICATION_LIBRARY')
    || define('APPLICATION_LIBRARY', realpath(dirname(__FILE__) . '/../lib'));
/** Zend_Application */

require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/conf/application.ini'
);


// 

// require_once 'facebook-platform/src/facebook.php';
// $appapikey = '142642259216807';
// $appsecret = '3642fa257b414eaab6c69d4bb99a26d8';
// $appcallbackurl = 'http://intranet.undac.edu.pe';


// $facebook = array(
//     'appapikey' => $appapikey,
//     'appsecret' => $appsecret,
//     'appcallbackurl' => $appcallbackurl
// );

// $registry = Zend_Registry::getInstance();
// $registry->set('facebook',$facebook);

// 
// $facebook = array(
    // 'appapikey' => $appapikey,
    // 'appsecret' => $appsecret,
    // 'appcallbackurl' => $appcallbackurl
// );

//$registry = Zend_Registry::getInstance();
//$registry->set('facebook',$facebook);

$application->bootstrap()
            ->run();    
