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

// use Symfony Yaml
use Symfony\Component\Yaml\Yaml;

// instantiate HttpRequest
$httpRequest = new Core\HttpRequest;

// get active record config
$activeRecordConfigPath = ROOT_PATH.'/config/database.yaml';
$activeRecordConfig = file_exists($activeRecordConfigPath) ? Yaml::parse(file_get_contents($activeRecordConfigPath)) : array();

// init active record
if ( isset($activeRecordConfig['connections']) ) {
    ActiveRecord\Config::initialize(function($cfg) use ($activeRecordConfig) {
        $cfg->set_model_directory('app/models/');
        $cfg->set_connections($activeRecordConfig['connections']);
        if ( isset($activeRecordConfig['default']) )
            $cfg->set_default_connection($activeRecordConfig['default']);
    });
}

// get routes config
$routesConfigPath = ROOT_PATH.'/config/routes.yaml';
$routesConfig = file_exists($routesConfigPath) ? Yaml::parse(file_get_contents($routesConfigPath)) : array();

// router
$router = new Core\Router($routesConfig, BASE_URL);
$router->route();
?>