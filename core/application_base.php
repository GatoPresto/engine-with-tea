<?php
//root path
defined('_root_path') or define('_root_path', dirname(dirname(__FILE__)));

class OwnClassLoader {
	public static function load($className, $path=false) {
		$to = [
			'core' => '/core/',
			'controller' => '/app/controllers/',
			'model' => '/app/models/',
		];

		$splited = preg_split("/(?<=[a-z])(?=[A-Z])/", $className);
		$path = _root_path.$to[strtolower(end($splited))];
		array_pop($splited);
		$path .= strtolower(implode('_', $splited)).'.php';

		if ( file_exists($path) )
			require_once $path;
		else
			throw new Exception('Class not found');
	}
}

spl_autoload_register(array('OwnClassLoader','load'));
?>