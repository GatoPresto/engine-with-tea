<?php
session_start();

// root path
defined('ROOT_PATH') or define('ROOT_PATH', dirname(dirname(__FILE__)));

// base url
preg_match('/([\/\w \.-]*)\//', $_SERVER['PHP_SELF'], $baseUrlMatch);
defined('BASE_URL') or define('BASE_URL', $baseUrlMatch[1]);

// instantiate the loader
$loader = require 'vendor/autoload.php';

// register the base directories for the namespace prefix
$loader->addPsr4('Core\\', 'core/');
$loader->addPsr4('Controller\\', 'app/controllers/');
$loader->addPsr4('Model\\', 'app/models/');

// instantiate HttpRequest
$httpRequest = new Core\HttpRequest;

// use Symfony Yaml
use Symfony\Component\Yaml\Yaml;

// get config
$configPath = ROOT_PATH.'/config/routes.yaml';
if ( file_exists($configPath) )
	$config = Yaml::parse(file_get_contents(ROOT_PATH.'/config/routes.yaml'));
else
	$config = array();

// router
$router = new Core\Router($config, BASE_URL);
$router->route();
?>