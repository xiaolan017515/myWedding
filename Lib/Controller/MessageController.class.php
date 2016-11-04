<?php
/**
*
*@descripe 客户控制器 
*/
header('Content-Type: application/json');
include_once "WeixinController.class.php";
class MessageController  extends WeixinController{

	private $conn;//数据库链接
	private $back_info;
	private $success;
	private $appid;
    private $secret;
    private $access_token;
    private $token;
    private $token_path;
	private $dir;
	public function __construct(){
		$this->token = 'zhouxiaojian';
        $this->appid = 'wx71a04e2172b98489';
        $this->secret = 'd32110f83c0fa4bebe85f1695d682e8d';
        $this->conn =  db::getConn();
	    $this->dir = __DIR__.'/../Public/keyan';
        
        if(!is_dir($this->dir)){
            mkdir($this->dir,0755,true);
        }

        $this->getToken();

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
            'userName'=>'test',
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

	    /**
    *@param  null
    *@return  null
    *@descripe 取得access_token
    */
    public function getToken(){
        if(!is_dir($this->dir.'/token')){
            writeLog('getToken'.$this->dir.'/token');
            mkdir($this->dir.'/token',0755,true);
        }
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->secret}";
        $token = $this->readToken($this->dir.'/token');
        $time = time();
        if(!is_null($token)){
            if($token['time'] < $time || is_null($token['access_token'])){
                $data = getCurl($url);
                $data = json_decode($data,true);
                $this->access_token = $data['access_token'];
                $this->writeToken($this->dir.'/token',$this->access_token,$time);
            }else{
                $this->access_token = $token['access_token'];
            }
        }
        return $this->access_token;
    }

	    /**
    *@param null
    *@return echo_str
    *@descripe 微信配置 验证token
    */

    public function valid(){
        $echo_str = $_GET["echostr"];
        if($this->checkSignature($this->token)){
            echo $echo_str;
            exit;
        }
    }

}
