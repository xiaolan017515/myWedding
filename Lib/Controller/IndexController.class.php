<?php
class IndexController{
	public function index(){
		header("location:../404.html");
	}
	public function login(){
		$_SESSION['id'] = 1;
		header('location:index.html');
	}
	public function test(){
		//echo $_SESSION['id']; 
		if($_SESSION['id'] === 1){
			$content = file_get_contents('Lib/Tml/hpsl.jpg');
			header("Content-Type: image/jpeg;text/html; charset=utf-8");
			echo $content;
		}
	}
}