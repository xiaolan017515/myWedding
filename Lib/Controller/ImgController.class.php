<?php
/*
*图片相关操作控制器
*
*/
class ImgController{
	private $conn;
	private $max_file_size;//上传文件大小限制, 单位BYTE  
	private $upload_path;//上传文件的路径
	private $upload_name;//上传图片的名称
	private $imgType;
	private $back_info;
	private $success;
	public function __construct(){
		$this->max_file_size = isset($config['max_file_size']) ? $config['max_file_size'] : 1000000;  
		$this->upload_path = isset($config['upload_path']) ? $config['upload_path'] : 'Lib/Public/Img/'; 
		$this->imgType = array('image/jpg'=>1,'image/png'=>1,'image/jpeg'=>1,'image/pjpeg'=>1,'image/gif'=>1,'image/bmp'=>1);
		$this->conn = db::getConn();

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


	/**
	*上传图片
	*
	*/

	public function uploadOneImg(){
		$id = isset($_POST['id']) ? $_POST['id'] :  -1;
		$role = isset($_POST['role']) ? $_POST['role'] : 'other';
		$file_name = isset($_POST['file_name'] ) ? $_POST['file_name'] : 'img';
		if(!is_uploaded_file($_FILES[$file_name]['name'])){
			$img = $_FILES['img'];
			$data = array(
				'belong_id'=>$id,
				'belong_role'=>$role
			);
			$back_info = $this->uploadImg($img,$data);
			echo  jsonEncode($back_info);
		}
	}


	/**
	*
	*@descripe 上传素材
	*/

	public function uploadMedia(){
		$id = isset($_POST['id']) ? $_POST['id'] :  -1;
		$role = isset($_POST['role']) ? $_POST['role'] : 'other';
		$file_name = isset($_POST['file_name'] ) ? $_POST['file_name'] : 'img';
		$type = isset($_POST['type']) ? $_POST['type'] : 'fangdai'; //房贷还是车贷
		if(!is_uploaded_file($_FILES[$file_name]['name'])){
			$img = $_FILES['img'];
			$data = array(
				'belong_id'=>$id,
				'belong_role'=>$role
			);
			$back_info = $this->uploadImg($img,$data);
			
			if($back_info['status'] && !is_null($type)){

				$file_info = array(
					'filename'=>'/'.$back_info['img_info']['path'],
					'content-type'=>$back_info['img_info']['type'],
					'filelength'=>$back_info['img_info']['size']
				);
				$controller = ucwords($type.'Controller');
				$wx = new $controller();
				$wx->uploadMedia($file_info,$wx->getToken());
				
			}
		}
	}


	/**
	*
	*@descripe 上传头像
	*/

	public function uploadHeadImg(){
		$role = array(NULL,'manager','sale','custom');
		$media_id = isset($_POST['media_id']) ? $_POST['media_id'] : NULL;
		$loan_type = isset($_POST['loan_type']) ? $_POST['loan_type'] : NULL;
		$data = isset($_POST['data']) ? $_POST['data'] : NULL;
		$back_info['status'] = false;
		$back_info['msg'] = 'data is null ';
		if(!is_null($data) && !is_null($loan_type) && !is_null($media_id)){
			$controller = ucwords($loan_type.'Controller');
			$wx = new $controller();

			$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$wx->getToken()}&media_id={$media_id}";
			$path =  IMG_PATH.'/'.$role[$data['belong_role']].'/'.$loan_type;
			if(!is_dir($path)){
				mkdir($path,0777,true);
			}
			$path = $path.'/'.$data['belong_id'].'-'.time().'.jpeg';
			$status = uploadImg($url,$path);
			if($status){
				$data['path'] = $path;
				$data['name'] = $data['belong_id'].'jpeg';
				$data['time'] = date('Y-m-d H:i:s');
				$data['source'] = $loan_type;
				$data['type'] = 'jpeg';
				$data['size'] = filesize($path);
				$sql = insertString($data,'img');
 				$back_info['status'] = $this->conn->add($sql);
				$back_info['msg'] = $back_info['status'] ? 'success' : 'sql error';
 			}else{
 				$back_info['status'] = false;
				$back_info['msg'] = 'save file error ';
			}
		}
		echo jsonEncode($back_info);
	}



	public function uploadBase64Img(){
		$role = array(NULL,'manager','sale','custom');
		$img = isset($_POST['img']) ? $_POST['img'] : NULL;
		$data = isset($_POST['data']) ? $_POST['data'] : NULL;
		$loan_type = isset($_POST['loan_type']) ? $_POST['loan_type'] : NULL;
		$back_info = $this->back_info;
		if(!is_null($img) && is_null($loan_type)){

			$path = 'Lib/Public/'.$loan_type.'/head_img';
			if(!is_dir($path)){
				mkdir($path,0755,true);
			}
			$path = $path.'/'.$data['belong_id'].'-'.time().'.jpeg';

			$tp = @fopen($path,'w');
	    	$str = explode(',', $img);
	        fwrite($tp,base64_decode($str[1]));
	        fclose($tp);

	        if(file_exists($path)){
	        	$data['path'] = $path;
				$data['name'] = $data['belong_id'].'.jpeg';
				$data['time'] = date('Y-m-d H:i:s');
				$data['source'] = $loan_type;
				$data['type'] = 'jpeg';
				$data['size'] = filesize($path);
				$sql = insertString($data,'img');
				$status = $this->conn->add($sql);
 				$back_info['status'] = $status ? true : false;
				$back_info['msg'] = $status ? 'ok' : 'sql error';
	        	$back_info['error_code'] = $status ? 0 : -3;
	        }else{
	        	$back_info['status'] = false;
				$back_info['msg'] = "上传失败";
	        	$back_info['error_code'] = -51;
	        }
		}
		echo jsonEncode($back_info);
	}



	/**
	*@param img  图片文件
	*@param data 图片归属者id 和 角色 ，没有则传 id = -1,role = other
	*@descript 上传单张图片
	*/
	public function uploadImg($img = NULL,$data = NULL){
		if(isset($img) && isset($data)){
			if ($this->imgType[$img['type']]  && $img['size'] < $this->max_file_size){
				$id = isset($data['belong_id']) ? $data['belong_id'] : NULL;
				$role = isset($data['belong_role']) ? $data['belong_role'] :  NULL;	
				if( (!is_null($id) ) && (!is_null($role))){
					if(!is_dir($this->upload_path.$role)){
						mkdir($this->upload_path.$role,0755);
					}
					$status = move_uploaded_file($img['tmp_name'],$this->upload_path.$role.'/'.$img['name']);
					if($status){
						$data['path'] = $this->upload_path.$role.'/'.$img['name'];
						$data['name'] = $img['name'];
						$data['type'] = $img['type'];
						$data['size'] = $img['size'];
						$data['time'] = date('Y-m-d H:i:s',time());
						$sql = insertString($data,'img');
						$back_info['status'] = $this->conn->add($sql);
						if($back_info['status']){
							$back_info['img_info'] = $data;
							$back_info['img_id'] = $back_info['status'];
							$back_info['status'] = true;
							$back_info['msg'] = "上传图片成功!";
						}else{
							$back_info['status'] = false;
							$back_info['msg'] = "上传图片失败!";
						}
					}else{
							$back_info['status'] = false;
							$back_info['msg'] = "上传图片失败!";
					}
				}else{
					$back_info['status'] = false;
					$back_info['msg'] = "存在空数据!";
				}
			}else{
				$back_info['status'] = false;
				$back_info['msg'] = "图片格式出错或者大小超过限制,请重试!";
			}
		}else{
			$back_info['status'] = false;
			$back_info['msg'] = "图片不存在,请重试!";
		}
		clearstatcache();
		return $back_info;
	}


	public function getImg($belong_id,$belong_role){
		$sql = "select path,name from img where belong_id = '%d' and belong_role = '%s' ";
		$sql = sprintf($sql,$belong_id,$belong_role);
		$img = $this->conn->find($sql);
		return $img;
	}
}