<?php
class SaleController{
	private $conn;
	
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

	public function checkSale($openid = NULL){
		$sql = "select id from sale where openid = '%s' ";
		$sql = sprintf($sql,$openid);
		$data = $this->conn->find($sql);
		if(!isset($data['id'])){
			$data['id'] = 0;
		}
		return $data;
	}

	/**
	*@return json 数组 
	*@descripe 前端获取销售员的客户列表
	*
	*/

	public function getCustom(){
		$sale_id = isset($_SESSION['id']) ? $_SESSION['id'] : NULL;
		$back_info = $this->back_info; 
		if(!is_null($sale_id)){
			$back_info = $this->success;
			$data = $this->getCustomInfo($sale_id);
			$data = isset($data['custom']) ? $data['custom'] : array();
			$back_info['list'] = $data; 
		}
		echo jsonEncode($data);
	}

	/**
	*
	*@descripe 客户的详细情况
	*/
	public function getCustomInfo($sale_id){

		//客户列表
		$sql = "select a.nickname,a.id,a.name,a.phone,a.type,a.addr,a.marry,a.idcard from sale b left join custom a on b.scene = a.scene where b.id = %d ";
		$sql = sprintf($sql,$sale_id);
		$data['custom'] = $this->conn->select($sql);
		
		//总接待客户人数
		$data['count'] = count($data['custom']);

		//总成交人数
		$total = 0;
		foreach ($data['custom'] as $key => $value) {
			if($value['type'] == 109){
				$total++;
			}
		}
		$data['total'] = $total;

		//今日接待客户人数
		$today = date('Y-m-d');
		$next_day = date('Y-m-d',strtotime($today) + 86400);
		$today = strtotime($today);
		$next_day = strtotime($next_day);

		$sql = "select a.id,a.name,a.phone,a.type from sale b left join custom a on b.scene = a.scene where b.id = %d and unix_timestamp(a.time) between %d and %d ";
		$sql = sprintf($sql,$sale_id,$today,$next_day);
		$data['today_custom'] = $this->conn->select($sql);
		$data['today_count'] = count($data['today_custom']);

		//今天成交人数
		$sql = "select a.id,a.name,a.phone,a.type from sale b left join custom a on b.scene = a.scene where b.id = %d and unix_timestamp(a.pass_date) between %d and %d ";
		$sql = sprintf($sql,$sale_id,$today,$next_day);
		$data['today_custom'] = $this->conn->select($sql);
		$data['today_total'] = count($data['today_custom']);


		return $data;
	}


	/**
	*@return json 数组 
	*@descripe 发送消息给客户
	*
	*/

	public function sendMsg(){
		
		$data = isset($_POST['data']) ? $_POST['data'] : NULL;
		$type = isset($_POST['type']) ? $_POST['type'] : NULL;
		$sale_id =  isset($_SESSION['id']) ? $_SESSION['id'] : NULL;
		$back_info['status'] = false;
		$back_info['msg'] = 'data NULL';
		writeLog($type.$sale_id.var_export($data,true));
		if ( (!is_null($sale_id)) && (!is_null($data)) && (!is_null($type)) ){
			$data['time'] = date('Y-m-d H:i:s',time());
			$data['source'] = 2;
			$sql = "select custom.id from custom ,sale where sale.scene = custom.scene and sale.id = %d and custom.type = %d ";
			$sql = sprintf($sql,$sale_id,$type);
			$custom_list = $this->conn->select($sql);

			if(count($custom_list)){
				foreach ($custom_list as $key => $value) {
					$data['customid'] = $value['id'];
					$sql = insertString($data,'custom_msg');
					$status = $this->conn->add($sql);
				}
			}
				$back_info['status'] = $status ? true : false;
				$back_info['msg'] = $status ? 'ok' : 'error sql';
				$back_info['error_code'] = $status ? 0 : -3;
		}
		echo jsonEncode($back_info);
	}
	
	/**
	*
	*@descripe 回复消息给客户
	*/
	public function replyMsg(){
		$data = isset($_POST['data']) ? $_POST['data'] : NULL;
		$sale_id =  isset($_SESSION['id']) ? $_SESSION['id'] : NULL;
		$back_info = $this->back_info;
		
		if ( (!is_null($sale_id)) && (!is_null($data)) ){
			$data['time'] = date('Y-m-d H:i:s',time());
			$data['source'] = 2;
			$sql = insertString($data,'custom_msg');
			$status = $this->conn->add($sql);
			$back_info['status'] = $status ? true : false;
			$back_info['msg'] = $status ? 'ok' : 'error sql';
			$back_info['error_code'] = $status ? 0 : -3;
		}
		echo jsonEncode($back_info);
	} 

	/**
	*@descripe 获取销售员的所有消息
	*/

	public function getMsg(){
		$sale_id =  isset($_SESSION['id']) ? $_SESSION['id'] : NULL;
		$back_info = $this->back_info;
		if (!is_null($sale_id)){
			$back_info = $this->success;
			$sql = "select title,content,time,source from sale_msg where saleid = %d and customid = 0 order by time desc  ";
			$sql = sprintf($sql,$sale_id);
			$data = $this->conn->select($sql);
			$back_info['list'] = isset($data) ? $data : array();
		}
		echo jsonEncode($back_info);
	}


	/**
	*@descripe 获取客户的消息
	*/

	public function getCustomMsg(){
		$sale_id =  isset($_SESSION['id']) ? $_SESSION['id'] : NULL;
		$back_info = $this->back_info;
		if (!is_null($sale_id)){
			$back_info = $this->success;
			
			//咨询看房
			$sql = "select a.id as msg_id,a.is_look,a.customid,a.title,a.content,a.time,b.name,b.phone from sale_msg a inner join custom b on a.customid = b.id where a.saleid = %d and  a.source = 4 and  display = 1  order by a.is_look,a.time desc  ";
			$sql = sprintf($sql,$sale_id);
			$data['consult'] = $this->conn->select($sql);//咨询
			$back_info['consult'] = isset($data['consult']) ? $data['consult'] : array();  
   			
   			//预约看房
   			$sql = "select a.id as msg_id,a.is_look,a.customid,a.title,a.content,a.time,b.name from sale_msg a inner join custom b on a.customid = b.id where a.saleid = %d and  a.source = 5  and display = 1 order by a.is_look, a.time desc  ";
			$sql = sprintf($sql,$sale_id);
			$data['bespeak'] = $this->conn->select($sql);//预约看房
			$back_info['bespeak'] = isset($data['bespeak']) ? $data['bespeak'] : array();
		}
		echo jsonEncode($back_info);
	}


	/*
	*@descripe 获取销售员个人信息
	*/

	public function getSale(){
		$sale_id = isset($_SESSION['id']) ? $_SESSION['id'] : NULL;
		$back_info = $this->back_info;
		if(!is_null($data) ){
			$back_info = $this->success;
			$back_info['info'] = $this->getSaleInfo($sale_id);
		}
		echo jsonEncode($back_info);
	}

	/**
	*@descripe 销售员详细信息
	*/
	public function getSaleInfo($id){

		//获取销售员个人信息
		$sql = "select nickname,id,name,phone,source,idcard from sale where id = %d ";
		$sql = sprintf($sql,$id);
		$data = $this->conn->find($sql);
		$type = isset($data['source']) ? $data['source'] : NULL;

		//获取销售员头像信息
		$sql = "select path,name from img where belong_id = %d and belong_role = '%s' and source = '%s' order by time desc ";
		$sql = sprintf($sql,$id,'2','%');
		$img = $this->conn->find($sql);
		$data['img_path'] = $img['path'];

		//获取销售员评价记录
		$data['score'] = $this->getAvgScore($id,'2');
		$data['score_list'] = $this->getScoreList($id,'2');
		$data['score_count'] = count($data['score_list']);

		//获取销售员的二维码图像路径
		$controller =  ucwords($type).'Controller';
		$handl = new $controller();
		$data['qcrode_path'] = $handl->createQrcode($id);

		return $data;
	}


	/*
	*@descripe 更新销售员个人信息
	*/

	public function updateSale(){
		
		$preg = new PregController();

		$data = isset($_POST['data']) ? $_POST['data'] : NULL;
		
		$sale_id = isset($_POST['id']) ? intval($_POST['id']) : NULL;
		$back_info = $this->back_info;
		
		$data_status = true;
		
		if(isset($data['phone'])){
			$data_status = $preg->phone($data['phone']);
			if(!$data_status){
				$back_info['msg'] = 'error data';
				$back_info['error_code'] = -11;//电话格式出错
			}
		}

		if(isset($data['idcard'])){
			$data_status = $preg->idCard($data['idcard']);
			if(!$data_status){
				$back_info['msg'] = 'error data';
				$back_info['error_code'] = -12;//身份证格式出错
			}
		}


		if ( (!is_null($data))  && (!is_null($sale_id)) && $data_status ){
			$sql = updateString($data,'sale',array('id'=>$sale_id));
			$status = $this->conn->update($sql);
			$back_info['status'] = $status ? true : false;
			$back_info['msg'] = $status ? 'ok' : 'error sql';
			$back_info['error_code'] = $status ? 0 : -3; 
		}
		echo jsonEncode($back_info);
	}


	/**
	*@param int 销售员Id
	*@param int 销售员所属的角色类型
	*@return double 平均分
	*@descripe 查询销售员的评价平均分
	*/

	public function getAvgScore($belong_id,$belong_role){
		$sql = "select AVG(score) as score from score where belong_role = '%s' and belong_id = %d ";
		$sql = sprintf($sql,$belong_role,$belong_id);
		$data = $this->conn->find($sql);
		return $data['score'];
	}


	/**
	*@param int 销售员Id
	*@param int 销售员所属的角色类型
	*@return array
	*@descripe 查询销售员的评价列表
	*/

	public function getScoreList($belong_id,$belong_role){
		$sql = "select  customid,score from score where belong_role = '%s' and belong_id = %d ";
		$sql = sprintf($sql,$belong_role,$belong_id);
		$data = $this->conn->select($sql);
		return $data;
	}

	
	/**
	*
	*@descripe  群发消息到指定分组
	*/

	public function pustMsg(){
		$type = isset($_POST['type']) ? $_POST['type'] : NULL;
		$data = isset($_POST['data']) ? $_POST['data'] : NULL;
		$sale_id = isset($_SESSION['id']) ? $_SESSION['id'] : NULL;

		$back_info = $this->back_info;

		if( (!is_null($type)) && (!is_null($sale_id)) && (!is_null($data))){
			$this->conn->autocommit(false);
			$data['time'] = date('Y-m-d',time());
			$data['source'] = 2;
			$data['title'] = '来自销售的消息';

			$sql = "select a.id from sale b left join custom a on b.scene = a.scene where b.id = %d ";
			$sql = sprintf($sql,$sale_id);
			
			$custom = $this->conn->select($sql);
			
			$add_status = true;
			foreach ($custom as $key => $value) {
				$data['customid'] = $value['id'];
				$sql = insertString($data,'custom_msg');
				$result = $this->conn->add($sql);
				if(!$result) $add_status = false;
			}	
			if($add_status){
				$this->conn->commit();
				$back_info = $this->success;
			}else{
				$back_info = array(
					'status'=>false,
					'error_code'=>-3,
					'msg'=>'sql error',
				);
				$this->conn->rollback();
			}
			$this->conn->autocommit(true);
		} 
		echo  jsonEncode($back_info);
	}


	/**
	*
	*@descripe 修改消息的阅读状态
	*/

	public function lookMsg(){
		$msg_id = isset($_POST['msg_id']) ? $_POST['msg_id'] :NULL;
		$back_info = $this->back_info;
		if(!is_null($msg_id)){
			$sql = "update sale_msg set is_look = 1 where id = %d ";
			$sql = sprintf($sql,$msg_id);
			$status = $this->conn->update($sql);
			$back_info['status'] = $status ? true : false;
			$back_info['msg'] = $status ? 'ok' : 'error sql';
			$back_info['error_code'] = $status ? 0 : -3; 
		}	
		echo jsonEncode($back_info);
	}

	//销售一年的销售详情
    public function yaerDetail(){
        $sale_id = isset($_SESSION['id']) ? $_SESSION['id'] : '1';
        $data = array();
        if(!is_null($sale_id)){

        	//获取销售员个人信息
			$sql = "select id,name,phone,source,idcard from sale where id = %d ";
			$sql = sprintf($sql,$sale_id);
			$data['info'] = $this->conn->find($sql);
        	
        	//获取销售员的接待情况
        	$today = $this->getCustomInfo($sale_id);
        	$data['today'] = array(
        		'today_count'=>$today['today_count'],
        		'today_total'=>$today['today_total']
        	);
        	$data['yeartotal'] = $today['total'];
        	//每个月的情况
        	$y = date('Y');
			$start = $y.'-01-01';
			$start = strtotime($start);
			for($i = 0;$i < 12;$i++){
				$end = strtotime("+1 Month",$start);
				$sql = "select a.id,a.name,a.phone,a.type from sale b left join custom a on b.scene = a.scene where b.id = %d and unix_timestamp(a.pass_date) between %d and %d ";
				$sql = sprintf($sql,$sale_id,$start,$end);
				$data['year']["$i"] = $this->conn->select($sql);
				$data['year']["$i"] = count($data['year']["$i"]);
				$start = $end;
			}
        }
        echo jsonEncode($data);
    }
}
