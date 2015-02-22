<?php
session_start();

// root path
defined('ROOT_PATH') or define('ROOT_PATH', dirname(dirname(__FILE__)));

// instantiate the loader
$loader = require 'vendor/autoload.php';

// register the base directories for the namespace prefix
$loader->addPsr4('Core\\', 'core/');
$loader->addPsr4('Controller\\', 'app/controllers/');
$loader->addPsr4('Model\\', 'app/models/');

// instantiate HttpRequest
$httpRequest = new Core\HttpRequest;

// router
$router = new Core\Router;
$router->routing();
?>