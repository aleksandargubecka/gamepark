<?php

// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

defined('APPLICATION_UPLOADS_DIR')
|| define('APPLICATION_UPLOADS_DIR', realpath(APPLICATION_PATH . '/../public/uploads'));

// Ensure library/ is on include_path
//set_include_path(implode(PATH_SEPARATOR, array(
//    realpath(APPLICATION_PATH . '/../library'),
//    get_include_path(),
//)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);


/**
 * Custom routes
 */
$FrontController = Zend_Controller_Front::getInstance();
$Router = $FrontController->getRouter();

$Router->addRoute("contact", new
    Zend_Controller_Router_Route
    (
        "contact", array
        (
            "controller" => "Page",
            "action"     => "contact",
        )
    )
);

$Router->addRoute("activities", new
    Zend_Controller_Router_Route
    (
        "activities", array
        (
            "controller" => "Activity",
            "action"     => "index",
        )
    )
);

$Router->addRoute("admin/reservations/:page", new
    Zend_Controller_Router_Route
    (
        "admin/reservations/:page", array
        (
            "controller" => "Admin",
            "action"     => "reservations",
        )
    )
);

$Router->addRoute("activities/:page", new
    Zend_Controller_Router_Route
    (
        "activities/:page", array
        (
            "controller" => "Activity",
            "action"     => "index",
        )
    )
);
$Router->addRoute("search/:term", new
    Zend_Controller_Router_Route
    (
        "search/:term", array
        (
            "controller" => "Activity",
            "action"     => "index",
        )
    )
);

$Router->addRoute("activity/:id", new
    Zend_Controller_Router_Route
    (
        "activity/:id", array
        (
            "controller" => "Activity",
            "action"     => "single",
        )
    )
);

$Router->addRoute("admin/activities/add", new
    Zend_Controller_Router_Route
    (
        "admin/activities/add", array
        (
            "controller" => "Activity",
            "action"     => "create",
        )
    )
);

$Router->addRoute("admin/activities/edit/:id", new
    Zend_Controller_Router_Route
    (
        "admin/activities/edit/:id", array
        (
            "controller" => "Activity",
            "action"     => "edit",
        )
    )
);

$Router->addRoute("admin/activities/delete/:id", new
    Zend_Controller_Router_Route
    (
        "admin/activities/delete/:id", array
        (
            "controller" => "Activity",
            "action"     => "delete",
        )
    )
);

$Router->addRoute("admin/auth/promote/:id/:role", new
    Zend_Controller_Router_Route
    (
        "admin/auth/promote/:id/:role", array
        (
            "controller" => "Auth",
            "action"     => "promote",
        )
    )
);

$Router->addRoute("admin/auth/promote/:id/:role", new
    Zend_Controller_Router_Route
    (
        "admin/auth/promote/:id/:role", array
        (
            "controller" => "Auth",
            "action"     => "promote",
        )
    )
);

$application->bootstrap()
    ->run();