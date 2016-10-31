<?php
	@session_start();
	//error_reporting(0);
	header("Content-type:text/html;charset=utf-8");
	define('c_path','Lib/Controller/');
	// date_default_timezone_set('Asia/Shanghai');
	// require_once('Lib/Config/config.php');
	require_once('Lib/Driver/db.driver.php');
	require_once('Lib/Driver/errorHandle.php');
	require_once('Lib/Common/common.php');
	require_once('Lib/Common/WXBizMsgCrypt.php');
	function load($class_name){
		require_once(c_path.$class_name.'.class.php');
	}
	spl_autoload_register('load');