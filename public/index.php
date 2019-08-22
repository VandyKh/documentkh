<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
    
// Define path to public directory
defined('PUBLIC_PATH') || define('PUBLIC_PATH', realpath(dirname(__FILE__)));
defined('SYSTEM_SES') || define('SYSTEM_SES', "authcustomer");
defined('SYSTEM_SES_FRONT') || define('SYSTEM_SES_FRONT', "authdocfront");
defined('FONTSIZE_REPORT') || define('FONTSIZE_REPORT', "16px");
defined('RECEIPT_TYPE') || define('RECEIPT_TYPE', "0");//1 5star,2 phnom mease,3 normal


// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();