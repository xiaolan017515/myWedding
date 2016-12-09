<?php
/**
*
*@descripe 客户控制器 
*/
header('Content-Type: application/json');

class MessageController  extends WeixinController{

	private $conn;//数据库链接
	private $back_info;
	private $success;

	public function __construct(){

        $this->conn =  db::getConn();

		$this->back_info = array(
			'code'=>1,
			'msg'=>'data is null'
		);
		$this->success = array(
            'code'=>0,
            'msg'=>'ok'
        );
	}
	
	public function index(){
		$this->valid();
	} 

	public function getMessage(){	
		//查询自身的信息
		$sql = "select * from message";
		// $sql = sprintf($sql,$id);
		$data = $this->conn->select($sql);
		
		if (isset($data)) {
			$back_info = $this->success;
		}else{
			$back_info = $this->back_info;
		}
		$back_info['info'] = isset($data) ? $data : array();
		// var_dump($back_info);
		echo jsonEncode($back_info);
	}

	public function setMessage(){
		
		$json = isset($_POST) ? $_POST : NULL;
		$back_info['msg'] = 'post为空';
        $data = array(
            'userName'=>$json['userName'],
            'date'=>date('Y-m-d H:i:s'),
            'content'=>$json['content']
        );
        $sql = insertString($data,'message');
        if ( !is_null($json)){
        	$back_info['code'] = '0';
        	$back_info['msg'] = 'ok';
        	$this->conn->add($sql);
        }else{
        	$back_info['code'] = '1';
        }
        echo jsonEncode($back_info);
	}

    public function setUserJoin(){
        
        $json = isset($_POST) ? $_POST : NULL;
        $back_info['msg'] = 'post为空';
        $data = array(
            'userName'=>$json['userName'],
            'date'=>date('Y-m-d H:i:s'),
            'phone'=>$json['phone'],
            'people'=>$json['people']
        );
        $sql = insertString($data,'userjoin');
        if ( !is_null($json)){
            $back_info['code'] = '0';
            $back_info['msg'] = 'ok';
            $this->conn->add($sql);
        }else{
            $back_info['code'] = '1';
        }
        echo jsonEncode($back_info);
    }

}
