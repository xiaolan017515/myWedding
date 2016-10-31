<?php
/**
*
*@descripe 客户控制器 
*/
class CustomController{

	private $conn;//数据库链接
	private $back_info;
	private $success;
	public function __construct(){
		$this->conn =  db::getConn();
		$this->back_info = array(
			'status'=>false,
			'msg'=>'data is null',
			'error_code'=>-1
		);
		$this->success = array(
            'status'=>true,
            'msg'=>'ok',
            'error_code'=>0
        );
	}
	
	public function index(){

	} 

	public function getOneCustom(){	
		$back_info = $this->back_info;
		$back_info = $this->success;

		//查询自身的信息
		$sql = "select * from user";
		// $sql = sprintf($sql,$id);
		$data = $this->conn->select($sql);
		$back_info['info'] = isset($data) ? $data : array();
		// var_dump($back_info);
		echo jsonEncode($back_info);
	}

}
