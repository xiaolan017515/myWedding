<?php
class AppController{
	private $conn;
	public function __construct(){
		$this->conn = db::getConn();
	}

	public function index(){

	}


	/**
	*@return json数组
	*@descripe APP端获取客户的个人信息
	*@example :{"id":"11","name":"\u75af\u5b50","idcard":"0","phone":"0","marr y":"0"}
	*/

	public function getCustom(){

		$custom_id = isset($_POST['json']) ? $_POST['json'] : NULL;  
		$data['result'] = 'error';
		$data['msg'] = 'id不存在';
		if (!is_null($custom_id)){
			
			//解析json 串 获取客户id
			$custom_id = jsonDecode($custom_id,NULL);
			$custom_id = $custom_id['in_id'];

			//查询客户基本信息
			$sql = "select id,sex,name,idcard,phone,marry from custom where id = %d ";
			$sql = sprintf($sql,$custom_id);
			writeLog($sql);
			$data['msg'] = $this->conn->find($sql);

			if(is_null($data['msg'])){
				$data['result'] =  'error';
				$data['msg'] =  '数据不存在或查询出错';
			}else{
				$data['result'] =  'ok';
			}
		}

		echo jsonEncode($data,NULL);
	}

	
	/**
	*@param null
	*@return null
	*@descripe App端获取销售员个人信息
	*/

	public function getSale(){
		
		$sale_id = isset($_POST['json']) ? $_POST['json'] : NULL;  
		
		$data['result'] = 'error';
		$data['msg'] = 'id不存在';
		
		if(!is_null($sale_id)){
			
			//解析json 串 获取销售id
			$sale_id = jsonDecode($sale_id,NULL);
			$sale_id = $sale_id['in_id'];
			
			//获取销售员个人信息
			$sql = "select id,name,phone,sex,idcard from sale where id = '%d' ";
			$sql = sprintf($sql,$sale_id);
			$data['msg'] = $this->conn->find($sql);

			if(is_null($data['msg'])){
				$data['result'] =  'error';
				$data['msg'] =  '数据不存在或查询出错';
			}else{
				$data['result'] =  'ok';
			}
		}
		echo jsonEncode($data,NULL);
	}


	/**
	*
	*
	*@descripe 查询楼盘信息
	*/

	public function getBuilding(){
		$build_id = isset($_POST['json']) ? $_POST['json'] : NULL;
		$data['result'] = 'error';
		$data['msg'] = 'id不存在';
		if (!is_null($build_id)){

			//解析json 串 获取楼盘Id
 			$build_id = jsonDecode($build_id,NULL);
 			$build_id = $build_id['in_id'];
 			//获取楼盘信息
			$sql = "select name,address as addr,phone from build where id = '%d' ";
			$sql = sprintf($sql,$build_id);
			$data['msg'] = $this->conn->find($sql);
			
			if (is_null($data['msg'])){
				$data['result'] =  'error';
				$data['msg'] =  '数据不存在或查询出错';
			}else{
				$data['result'] =  'ok';
			}
		}
		echo  jsonEncode($data,NULL);
	}


	/**
	*
	*@descripe 查询开发商的信息
	*/

	public function getBuilder(){
		$builder_id = isset($_POST['json']) ? $_POST['json'] : NULL;
		$data['result'] = 'error';
		$data['msg'] = 'id不存在';
		
		if (!is_null($builder_id)){

			//解析json 串 获取开发商id
			$builder_id = jsonDecode($builder_id,NULL);
			$builder_id = $builder_id['in_id'];		

			//查询开发商个人信息
			$sql = "select name,phone from product where id = '%d' ";
			$sql = sprintf($sql,$builder_id);
			$data['msg'] = $this->conn->find($sql);
			
			if (is_null($data['msg'])){
				$data['result'] =  'error';
				$data['msg'] =  '数据不存在或查询出错';
			}else{
				$data['result'] =  'ok';
			}
		}
		echo jsonEncode($data,NULL);
	}


	/**
	*
	*获取4S店信息
	*/

	public function get4s(){
		$s_id = isset($_POST['json']) ? $_POST['json'] : NULL;
		$data['result'] = 'error';
		$data['msg'] = 'id不存在';
		if (!is_null($s_id)){
			$s_id = jsonDecode($s_id,NULL);
			$s_id = $s_id['in_id'];

			//查询4S店的信息
			$sql = "select name,address as addr,phone from 4s where id = '%d' ";
			$sql = sprintf($sql,$s_id);
			$data['msg'] = $this->conn->find($sql);
			
			if (is_null($data['msg'])){
				$data['result'] =  'error';
				$data['msg'] =  '数据不存在或查询出错';
			}else{
				$data['result'] =  'ok';
			}
		}
		echo jsonEncode($data,NULL);
	}


	/**
	*
	*
	*@descripe 更新客户的贷款进度
	*/
	
	public function updateProcess(){
		
		$json = isset($_POST['json']) ? $_POST['json'] : NULL;
		$back_info['result'] = 'error';
		$back_info['msg'] = 'post为空';
		$status = -999;
		if ( !is_null($json) ){

			$json = jsonDecode($json,NULL);//App端	发送的数据
			$json = jsonDecode($json,NULL);

			$table = isset($json['loantype']) ? $json['loantype'] : NULL;//贷款来源
			$id = isset($json['in_id']) ? $json['in_id'] : NULL;//贷款表Id
			$serverid =  isset($json['serverid']) ? $json['serverid'] : NULL;//客户经理id
			$process = jsonDecode($json['process'],NULL);//贷款进度详情


			//判断贷款进度
			if(count($process) > 0){
				$key =	array_keys($process[0]);
				$status = intval($key[count($key) - 1]);//贷款状态码
				$msg = $json['process'];//贷款进度详情消息

				if($status == -1){
					if (isset($process[1])){
						$keys = array_keys($process[1]);
						$status = intval( $keys[count($keys) - 1 ] );
					}
				}


				if($status != -999 &&  (!is_null($table))  &&  (!is_null($serverid)) &&  (!is_null($id))  ){
					

					//获取贷款客户的openid,id
					$sql = "select a.source,a.openid,a.nickname, a.id,a.type,b.process from custom a,%s b where b.customid = a.id and b.id = '%d' ";
					$sql = sprintf($sql,$table,$id);
					$custom = $this->conn->find($sql);
					writeLog($sql);
					if(isset($custom['openid']) && isset($custom['id'])){
						$this->conn->autocommit(false);

						//取得贷款客户的基本信息，id，openid，客户类型,贷款进度
						$openid = $custom['openid'];
						$custom_id = $custom['id'];
						$custom_type =$custom['type']; 
						$custom_process = $custom['process'];
						$server = $custom['source'];//属于哪个服务号的
						$nickname = $custom['nickname'];
						//新增客户消息
						$msg_data['title'] = '来自银行的消息';
						$msg_data['content'] = $msg;
						$msg_data['source'] = 1;
						$msg_data['time'] = date('Y-m-d H:i:s');
						$msg_data['customid'] = $custom['id'];
						$msg_data['type'] = $status;
						$sql = insertString($msg_data,'custom_msg');
						$msg_insert = $this->conn->add($sql);

						$is_update = true;//是否更新数据
						//writeLog('status :'.$status);
						if($status == 5){
							writeLog('process_result:'.json_encode($process));
							$process_result = $process[0][$status]['result']; 
							
							if(strcmp($process_result,'pass') != 0){
								
								if(strcmp($process_result,'refuse') == 0){
									$sql = "update %s set process = '-2' where id = '%d' ";
									$sql = sprintf($sql,$table,$id);
									$this->conn->update($sql);
								}

								$is_update = false;
								
							}else{
								/*$msg_data['title'] = '来自系统的消息';
								$msg_data['content'] = '您的菜单将会更新,请重新进入公众号!';
								$msg_data['source'] = 0;
								$msg_data['time'] = date('Y-m-d H:i:s');
								$msg_data['customid'] = $custom['id'];
								$msg_data['type'] = $status;
								$sql = insertString($msg_data,'custom_msg');
								$msg_insert = $this->conn->add($sql);*/
								
								$sql = "update custom set pass_date = '%s' where id = %d ";
								$sql = sprintf($sql,date('Y-m-d H:i:s'),$custom_id);
								$this->conn->update($sql);

							}
						}

						//如果贷款进度为5 并且 审核状态不为PASS  则不更新贷款进度及分组
						if($is_update == true){

							//更新贷款进度,客户经理,和消息
							$sql = "update %s set serverid = '%d',msg = '%s',process = '%d' where id = '%d' ";
							$sql = sprintf($sql,$table,$serverid,$msg,$status,$id);
							$update_process = $this->conn->update($sql);//成功返回true

							$update_group = true;
							$update_type = true;

							//更新客户分组
							/*if($status == 5){
								$process_result = $process[0][$status]['result']; 
								if($process_result == 'pass'){
									$controller = ucwords($server.'Controller');
									$wx = new $controller();
									$update_group = $wx->updateUserGroup($openid,'109');
									$update_group = jsonDecode($update_group,NULL);
									writeLog($update_group);
									$update_group = $update_group['errcode']  ? false : true;//成功返回0
								}
							
							}*/
						
							//更新客户类型 
							
							if($custom_type == 0){
								$sql = "update custom set type = '%d' where id = '%d' ";
								$sql = sprintf($sql,109,$custom_id);
								$update_type = $this->conn->update($sql);//成功返回true
							}

							if( (!$update_group) || (!$update_process) || (!$update_type) || (!$msg_insert) ){
								if((!$update_group)){
									$msg = '更新客户分组出错!';
								}else if((!$update_process)){
									$msg = '更新客户贷款进度出错!';
								} else if( (!$update_type) ){
									$msg = '更新客户贷款类型出错!';
								}else if((!$msg_insert)){
									$msg = '推送客户信息出错!';
								}
								$status = -999;
								$this->conn->rollback();
							}else{
								$this->conn->commit();
							}
						}else{
							$this->conn->commit();
						}

						//贷款进度更新时 推送消息
						if($status != $custom_process && $status != -999){
							
							//查询客户经理的信息
							/*$custom_C =  new CustomController();
							$servar_data = $custom_C->queryServer($serverid);
							$servar_data = $servar_data['msg'];
							$servar_data['status'] = $status;
							$servar_data['content'] = $this->checkStatus($status,$process);*/
							$process_msg = $this->checkStatus($status,$process);
							$controller = ucwords($server.'Controller');
							$wx = new $controller();
							$access_token = $wx->getToken();
							$tel_data = array(
								'openid'=>$openid,
								'keyword1'=>'贷款进度更新提醒' ,
								'keyword2'=> $nickname,
								'keyword3'=> $process_msg,
							);
							$tel_data = $wx->senLoanMsg($tel_data);
							$res = $wx->sendTemMsg($tel_data,$access_token);
							//writeLog('贷款进度模板消息:'.$res);
						}
						$this->conn->autocommit(true);
					}else{
						$status = -999;
						$msg = "查询不到相关客户";
					}
				}else{
					$status = -999;
					$msg = "id,table,serverid 存在为空";
				}
				$back_info['msg'] = $msg;
				$back_info['result'] = ($status != -999) ? 'ok':'error';
			}else{
				$back_info['result'] = 'error';
				$back_info['msg'] = 'stauts为空';
			}
		} 
		echo jsonEncode($back_info,NULL);
	}
	public function checkStatus($status = NULL,$process){
		$msg;
		switch ($status) {
				case 1:
                    $msg = "请尽快领取您的借款合同";  
                    break;
                case 2:
                    $msg = "请尽快领取您的产权证";
                    break;
                case 3:
                    $msg = "客户经理接收了您的贷款申请";  
                    break;
                case 4:
                    $msg = "您需要准备相关资料,请查看'我的消息'";
                    break;
                case 5:
                	$process_result = $process[0][$status]['result'];
                	writeLog('process_result:'.$process_result);
                	if(!strcmp($process_result,'pass') )$msg = "审批结果已出,请查看'我的消息'";
                    else if(!strcmp($process_result,'again') )$msg = "资料不完整,请查看'我的消息'";
                    else $msg = "银行已拒绝你的贷款申请,请查看'我的消息'";
                    break;
                case 6:
                	if(isset($process[0][$status]['msg']) ){
                		$process_result = $process[0][$status]['msg']; 
                		$msg = '放款时间:'.$process_result['loantime'].'放款金额'.$process_result['firstmoney'].'第一次刷卡交易时间:'.$process_result['shuaka'];
                	}else{
                		$msg = "等待商家提交凭证";
                	}
                	break;
                case 7:
                    $process_result = $process[0][$status]['msg'];
                	$msg = '第一次刷卡交易时间:'.$process_result['shuaka'];
                	break;
                case 8:
                	$process_result = $process[0][$status]['msg'];
                	$msg = '放款时间:'.$process_result['loantime'].'第二次放款金额'.$process_result['secondmoney'].'第二次刷卡交易时间:'.$process_result['shuaka'].'第二次刷卡交易地点:'.$process_result['addr'];
                	break;
                case -1:
                	if(isset($process[0][$status]['msg']) ){
                       	$process_result = $process[0][$status]['msg'];
                		$msg = '第二次刷卡交易时间:'.$process_result['shuaka'];
                	}else{
                    	$msg = "恭喜您，银行已确认放款";	
                	}
                	break;
                default :
                    $msg = "银行还没有受理您的贷款";
                    break;
            }
		return $msg;
	}
}
