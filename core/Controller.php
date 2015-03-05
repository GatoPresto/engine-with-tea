<?php
namespace Core;

/**
 * Controller is the base class for classes containing controller logic.
 */
class Controller {
    protected $twig;
    protected $aroundAction = NULL;
    protected $beforeAction = NULL;
    protected $afterAction = NULL;
    protected $actionName = NULL;

	function __construct($actionName) {
        // twig
        $twigLoader = new \Twig_Loader_Filesystem('app/views/');
        $this->twig = new \Twig_Environment($twigLoader);

		$this->actionName = $actionName;
		method_exists($this, $actionName) or die('The action is not found.');

		if ( !is_null($this->aroundAction) ) {
			$this->filter($this->aroundAction);
			$this->$actionName();
			$this->filter($this->aroundAction);
		} else {
			if ( !is_null($this->beforeAction) )
				$this->filter($this->beforeAction);
			$this->$actionName();
			if ( !is_null($this->afterAction) )
				$this->filter($this->afterAction);
		}

        if ( $_SERVER['REQUEST_METHOD'] == 'GET' )
            $this->render();
	}

	/**
	 * Filter method for after, before, around action.
	 */
	private function filter($args) {
		if ( !is_array($args) ) {
			method_exists($this, $args) or die('Filter method is not exists');
			$this->$args();
		} else {
            $aux = function($f) {
                ( isset($f[0]) && method_exists($this, $f[0]) ) or die('Filter method is not exists');

                // only and except situation
                if ( isset($f['only']) ) {
                    if ( is_string($f['only']) && $this->actionName != $f['only'] )
                        return;
                    elseif ( is_array($f['only']) && !in_array($this->actionName, $f['only']) )
                        return;
                } elseif ( isset($f['except']) ) {
                    if ( is_string($f['except']) && $this->actionName == $f['except'] )
                        return;
                    elseif ( is_array($f['except']) && in_array($this->actionName, $f['except']) )
                        return;
                }

                if ( !isset($f[1]) )
                    call_user_func(array($this, $f[0]));
                else
                    call_user_func(array($this, $f[0]), $f[1]);
            };

            if ( isset($args[0]) && is_string($args[0]) ) {
                $aux($args);
            } elseif ( isset($args[0]) && is_array($args[0]) ) {
                foreach ( $args as $key => $f ) {
                    $aux($f);
                }
            }
		}
	}

    private function render() {
        $this->twig->addGlobal('global', $GLOBALS);
        echo $this->twig->render("{$GLOBALS['route']['controller']}/{$GLOBALS['route']['action']}.twig");
    }
}
?>