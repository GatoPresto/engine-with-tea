<?php
namespace Core;

class Router {
	const GET_FORMAT = 'get';
	public $routeKey = 'r';
	public $controller = 'static';
	public $action = 'index';

	public function routing() {
		$GLOBALS['httpRequest']->normalizeRequest();

		if (self::GET_FORMAT == 'get')
			$route = $GLOBALS['httpRequest']->getQuery($this->routeKey);
		else
			$route = $GLOBALS['httpRequest']->getPost($this->routeKey);

		$route = strtolower($route);
		
		if ( empty($route) ) {
			header('Location: ?'.$this->routeKey.'='.$this->controller.'/'.$this->action);
		} else {
			$pattern = '/^([\w]+)\/([\w]+)$/iu';
			if (!preg_match($pattern, $route, $matches))
				die('The route is not found');
		}

		$this->controller = 'Controller\\'.ucfirst($matches[1]).'Controller';
		$this->action = $matches[2];

		if ( class_exists($this->controller) ) {
			$a = new $this->controller;
		}else
			die('The controller is not found.');
		if ( method_exists($a, $this->action) )
			call_user_func(array($a, $this->action));
		else
			die('The action is not found.');
	}
}
?>