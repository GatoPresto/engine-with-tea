<?php
namespace Core;

/**
 * Controller is the base class for classes containing controller logic.
 */
class Controller {
    protected $twig;
    public $layout;
    public $view;
    protected $aroundAction;
    protected $beforeAction;
    protected $afterAction;
    protected $actionName;

	function __construct($actionName) {
        $this->actionName = $actionName;
        $this->template_init($this->layout, 'layouts/app.twig');
        $this->template_init($this->view, "{$GLOBALS['route']['controller']}/{$GLOBALS['route']['action']}.twig");
        $this->twig_init();

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
        $this->twig->addGlobal('self', $this);

        if ( $this->view )
            echo $this->twig->render($this->view);
        else
            echo $this->twig->render($this->layout);
    }

    private function twig_init() {
        $twigLoader = new \Twig_Loader_Filesystem('app/views/');
        $this->twig = new \Twig_Environment($twigLoader);
    }

    private function template_init(&$obj, $default = NULL) {
        if ( $obj === NULL ) {
            $obj = $default;
        } elseif ( is_array($obj) ) {
            $aux = function(&$obj, $v, $default) {
                // only and except situation
                if ( isset($v['only']) ) {
                    if ( is_string($v['only']) && $this->actionName != $v['only'] ) {
                        $obj = $default;
                        return;
                    } elseif ( is_array($v['only']) && !in_array($this->actionName, $v['only']) ) {
                        $obj = $default;
                        return;
                    }
                } elseif ( isset($v['except']) ) {
                    if ( is_string($v['except']) && $this->actionName == $v['except'] ) {
                        $obj = $default;
                        return;
                    } elseif ( is_array($v['except']) && in_array($this->actionName, $v['except']) ) {
                        $obj = $default;
                        return;
                    }
                }

                $obj = $v[0];
            };

            if ( isset($obj[0]) && is_string($obj[0]) ) {
                $aux($obj, $obj, $default);
            } elseif ( isset($obj[0]) && is_array($obj[0]) ) {
                foreach ( $obj as $key => $v ) {
                    $aux($obj, $v, $default);
                }
            } elseif ( isset($obj[0]) && is_bool($obj[0]) ) {
                $obj = ( $obj[0] ) ? $default : false;
            }
        }
    }
}
?>