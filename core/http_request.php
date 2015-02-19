<?php
class HttpRequestCore {
	/*
	 * Normalizes the data request.
	 * This method remove slashes in query data, if get_magic_quotes_gpc () returns true.
	 */
	public function normalizeRequest() {
		// нормализация запроса.
		if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
			if(isset($_GET))
				$_GET=$this->stripSlashes($_GET);
			if(isset($_POST))
				$_POST=$this->stripSlashes($_POST);
			if(isset($_REQUEST))
				$_REQUEST=$this->stripSlashes($_REQUEST);
			if(isset($_COOKIE))
				$_COOKIE=$this->stripSlashes($_COOKIE);
		}
	}

	// Remove slashes
	public function stripSlashes(&$data) {
		return is_array($data) ? array_map(array($this,'stripSlashes'),$data) : stripslashes($data);
	}

	/*
	 * Return the value of GET parameter's name.
	 * If there is no value, then POST.
	 * Otherwise, default value $defaultValue.
	 * GET takes precedence.
	 */
	public function getParam($name, $defaultValue=null) {
		return isset($_GET[$name]) ? $_GET[$name] : (isset($_POST[$name]) ? $_POST[$name] : $defaultValue);
	}

	// only GET
	public function getQuery($name, $defaultValue=null) {
		return isset($_GET[$name]) ? $_GET[$name] : $defaultValue;
	}

	// only POST
	public function getPost($name, $defaultValue=null) {
		return isset($_POST[$name]) ? $_POST[$name] : $defaultValue;
	}

	private $_scriptUrl;
	private $_baseUrl;

	// return absolute url
	public function getBaseUrl($absolute=false) {
		if($this->_baseUrl===null)
			$this->_baseUrl=rtrim(dirname($this->getScriptUrl()),'\\/');
		return $absolute ? $this->getHostInfo() . $this->_baseUrl : $this->_baseUrl;
	}

	// return host
	public function getHostInfo() {
		$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https" : "http";
		return $protocol . "://" . $_SERVER['HTTP_HOST'];
	}

	// return script
	public function getScriptUrl() {
		if($this->_scriptUrl===null)
		{
			$scriptName=basename($_SERVER['SCRIPT_FILENAME']);
			if(basename($_SERVER['SCRIPT_NAME'])===$scriptName)
				$this->_scriptUrl=$_SERVER['SCRIPT_NAME'];
			else if(basename($_SERVER['PHP_SELF'])===$scriptName)
				$this->_scriptUrl=$_SERVER['PHP_SELF'];
			else if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME'])===$scriptName)
				$this->_scriptUrl=$_SERVER['ORIG_SCRIPT_NAME'];
			else if(($pos=strpos($_SERVER['PHP_SELF'],'/'.$scriptName))!==false)
				$this->_scriptUrl=substr($_SERVER['SCRIPT_NAME'],0,$pos).'/'.$scriptName;
			else if(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'],$_SERVER['DOCUMENT_ROOT'])===0)
				$this->_scriptUrl=str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
			else
				throw new Exception('CHttpRequest is unable to determine the entry script URL.');
		}
		return $this->_scriptUrl;
	}
}