<?php
	require_once('Lib/Common/start.php');
	header('Content-Type:text/html;charset=utf-8');
	$c = isset($_GET['C']) ? $_GET['C'] : 'Index';
	$m = isset($_GET['M']) ? $_GET['M'] : 'index';
	$controller = $c.'Controller';
	$handle = new $controller();
	$method = $handle->$m();