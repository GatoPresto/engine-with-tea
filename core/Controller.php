<?php
namespace Core;

/**
 * Controller is the base class for classes containing controller logic.
 */
class Controller {
	public $aroundAction = NULL;
	public $beforeAction = NULL;
	public $afterAction = NULL;
	public $actionName = NULL;

	function __construct($actionName) {
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
	}

	/**
	 * Filter method for after, before, around action.
	 */
	private function filter($args) {
		if ( !is_array($args) ) {
			method_exists($this, $args) or die('Filter method is not exists');
			$this->$args();
		} else {
			( isset($args[0]) && method_exists($this, $args[0]) ) or die('Filter method is not exists');

			// only and except situation
			if ( isset($args['only']) ) {
				if ( !is_array($args['only']) && $this->actionName != $args['only'] )
					return;
				elseif ( is_array($args['only']) && !in_array($this->actionName, $args['only']) )
					return;
			} elseif ( isset($args['except']) ) {
				if ( !is_array($args['except']) && $this->actionName == $args['except'] )
					return;
				elseif ( is_array($args['except']) && in_array($this->actionName, $args['except']) )
					 return;
			}

			if ( !isset($args[1]) )
				call_user_func(array($this, $args[0]));
			else
				call_user_func(array($this, $args[0]), $args[1]);
		}
	}
}
?>