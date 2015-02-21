<?php
session_start();

// root path
defined('ROOT_PATH') or define('ROOT_PATH', dirname(dirname(__FILE__)));

// instantiate the loader
require_once(dirname(__FILE__).'/Autoloader.php');
$loader = new Core\Psr4AutoloaderClass;

// register the autoloader
$loader->register();

// register the base directories for the namespace prefix
$loader->addNamespace('Core', ROOT_PATH.'/core');
$loader->addNamespace('Controller', ROOT_PATH.'/app/controllers');
$loader->addNamespace('Model', ROOT_PATH.'/app/models');

// instantiate HttpRequest
$httpRequest = new Core\HttpRequest;

// router
$router = new Core\Router;
$router->routing();
?>