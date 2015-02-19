<?php
$appPath = dirname(__FILE__).'/core/application.php';

try {
	require_once($appPath);
} catch (Exception $e) {
	echo $e->getMessage();
}
?>