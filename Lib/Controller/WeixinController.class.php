<?php
/**
*微信公众号通用方法
*消息回复，发送模板消息等等
*
*/
class WeixinController{


    /**
    *@param null
    *@return array 缓存的token 和 过期时间
    *@descripe  读取文本中的access_token 和 过期时间
    */

    public function readToken($path){
        $handle = fopen($path.'/token.txt','r');
        $token = fgets($handle);
        fclose($handle);
        $data = explode(':', $token);
        $data['access_token'] = isset($data[0]) ?($data[0]) : 0;
        $data['time'] = isset($data[1]) ?($data[1]) : 0;
        return $data;
    }


    /**
    *@param access_token  微信生成的access_token
    *@param time          access_token 过期时间
    *@return null
    *@descripe 将生成的access_token 和 过期时间 写入文本
    */
    public function writeToken($path,$access_token,$time){
        $time = intval($time + 500);
        $handle = fopen($path.'/token.txt', 'w');
        fwrite($handle, $access_token.':'.$time);
        fclose($handle);
        return null;
    }


	/**
	*
	*@descripe 更新指定的公众号token
	*@example {"token":null}
	*/

	public function updateToken(){
		$type = isset($_GET['type']) ? $_GET['type'] : NULL;
		//echo $type;
		$token['token'] = '-1';
		if(!is_null($type)){
			$controller = $type.'Controller';
			$handle = new $controller();
			$token['token'] = $handle->getToken();
		}
		echo jsonEncode($token);
	}


	/**
	*@param string access_token 微信公众号的token
	*@param string opneid       客户的openid
	*@return array 				客户的基本信息
	*
	*/

	public function getInfo($access_token,$openid){
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openid}&lang=zh_CN";
        $back_info = getCurl($url);
        $data = json_decode($back_info,true);
        $data['sex'] = $data['sex'] ? '男':'女';
        return $data;
    }


    /**
    *@param null
    *@return 
    *@descripe 检验签名是否来自微信
    */

    public function checkSignature($define_token){
        $back_info = true;
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];        
        $token = $define_token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            $back_info = true;
        }else{
            $back_info = false;
        }
        return $back_info;
    }


	/**
    *@param    
    *@return str
    *@descripe 文本消息模板
    */

    public function textTpl($to_user,$from_user,$time,$type,$content){
        $text_tpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[%s]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                <FuncFlag>0</FuncFlag>
                </xml>";
        $result_str = sprintf($text_tpl,$to_user,$from_user,$time,$type,$content);
        return $result_str;
    }


    /**
    *@param    
    *@return str
    *@descripe 图片消息模板
    */

    public function imgTpl($to_user,$from_user,$time,$type,$mediaid){
        $text_tpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Image>
					<MediaId><![CDATA[%s]]></MediaId>
					</Image>
					</xml>";
        $result_str = sprintf($text_tpl,$to_user,$from_user,$time,$type,$mediaid);
        return $result_str;
    }


    /**
     * [imgTextTel description]
     * @param  [type] $to_user   [description]接收方
     * @param  [type] $from_user [description]发送方
     * @param  [type] $num       [description]图文数量
     * @param  [type] $time      [description]生成时间
     * @param  [type] $data      [description]发送的数据
     * @return [type]            [description]
     */
    
    public function imgTextTel($to_user,$from_user,$time,$num,$data){
       // $url = "http://wx.iisun.net/kangaixiehui/aixinchuandi.html?id=".$data['id'].'&num='.$data['num'];
        $str = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[news]]></MsgType>
                <ArticleCount>%d</ArticleCount>
                <Articles>
                ";
        $str = sprintf($str,$to_user,$from_user,$time,$num);
        foreach ($data as $key => $value) {
            $tel_str = "
                    <item>
                    <Title><![CDATA[%s]]></Title> 
                    <Description><![CDATA[%s]]></Description>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    <Url><![CDATA[%s]]></Url>
                    </item>
                    ";   
            $tel_str = sprintf($tel_str,$value['Title'],$value['Description'],$value['PicUrl'],$value['Url']);
            $str  .= $tel_str;
        }

        $str .=  "</Articles></xml>";
        //writeLog($str);
        return $str;
    }


    /**
    *@param null
    *@return  json      服务器返回消息
    *@descripe 创建分组
    */

    public function createGroup($access_token,$data = null){
        $group_url = "https://api.weixin.qq.com/cgi-bin/groups/create?access_token={$access_token}";
        $data = array('group'=>array('name'=>urlencode($data)));
        $data = json_encode($data);
        $back_info = postCurl($group_url,$data);
        $back_info = json_decode($back_info,true)['group'];
        $back_info['name'] = urldecode($back_info['name']);
        return $back_info;
    }


    /**
    *@param null
    *@return  json      服务器返回消息
    *@descripe 删除分组
    */

    public function delGroup($id,$access_token){
        $group_url = "https://api.weixin.qq.com/cgi-bin/groups/delete?access_token={access_token}";
        $data = array('group'=>array('id'=>$id));
        $data = json_encode($data);
        $back_info = postCurl($group_url,$data);
        return $back_info;
    }


    /**
    *@param open_id 客户的open_id
    *@return json 客户所在的分组信息
    *@descrioe 根据客户的openid查询客户所在分组信息 
    */

    public function queryUserGroup($open_id,$access_token){
        $query_url = "https://api.weixin.qq.com/cgi-bin/groups/getid?access_token={$access_token}";
        $data = array('openid'=>$open_id);
        $data = json_encode($data);
        $back_info = postCurl($query_url,$data);
        return $back_info;
    }   


    /**
    *@param string  access_token 公众号的token
    *@return  json      服务器返回消息
    *@descripe 查询公众号已建立的分组
    */

    public function getGroup($access_token){
        $group_url = "https://api.weixin.qq.com/cgi-bin/groups/get?access_token={$access_token}";
        $back_info = getCurl($group_url);
        $back_info = json_decode($back_info,true)['groups'];
        foreach ($back_info as $key => $value) {
            $back_info[$key]['name'] = urldecode($back_info[$key]['name']);
        }
        return $back_info;
    }


    /**
    *@param array  菜单样式数组
    *@return array 自定义菜单样式
    *@descripe 生成自定义菜单样式
    */

    public function menuData($data){
        if(isset($data['matchrule'])){
            $data  = array('button'=>$data['menu'],'matchrule'=>$data['matchrule']);
        }else{
            $data  = array('button'=>$data['menu']);
        }

        return $data;
    }


    /**
    *@param array 菜单样式
    *@param string string access_token  公众号的token
    *@return json 服务器返回结果
    *@descripe 根据自定义菜单样式生成菜单
    */

    public function createMenu($data,$access_token){
        $menu_url = '';
        if(isset($data['matchrule'])){
            $menu_url = "https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token={$access_token}";
        }else{
            $menu_url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
        }
        $back_info = postCurl($menu_url,json_encode($data,JSON_UNESCAPED_UNICODE));
        return $back_info;
    }
    

    /**
    *@param string access_token  公众号的token
    *@return json 服务器返回结果
    *@descripe 查询生产的菜单
    */

    public function  getMenu($access_token){
        $menu_url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token={$access_token}";
        $back_info = getCurl($menu_url);
        $back_info =  json_decode($back_info,true);
        return $back_info;
    }


    /**
    *@param string access_token  公众号的token
    *@return json 服务器返回信息
    *@descripe 删除公众号原有菜单
    */

    public function delMenu($access_token){
        $menu_url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token={$access_token}";
        $back_info = getCurl($menu_url);
        return $back_info;
    }


    /**
    *@param array 发送的模板消息
    *@param string 公众号的token
    */

    public function sendTemMsg($data,$access_token){
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";
        $res = postCurl($url,$data);
    }


    /**
    *
    *@descripe 微信上传素材
    */

    public function uploadMedia($file,$access_token){
        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token={$access_token}&type=image";
        $real_path = "{$_SERVER['DOCUMENT_ROOT']}{$file['filename']}";
        $data = array(
            'media'=>new CURLFile($real_path),
            'form-data'=>$file
        );
        $media = postCurl($url,$data);
        $media_id = jsonDecode($media)['media_id'];
        var_dump($media);
        $this->uploadnews($media_id,$access_token);
        
    }


    public function uploadNews($media_id,$access_token){
        $data = array(
            'articles'=>array(
                array(               
                'thumb_media_id'=>$media_id,
                'author'=>'qyx',
                'title'=>urlencode('黑喂狗'),
                'content_source_url'=>'www.iisun.net',
                'content'=>urlencode('图文消息群发')
                )
            )
        );
        $data = jsonEncode($data);
        $data = urldecode($data);
        $url = "https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token={$access_token}";
        $data = postCurl($url,$data);
        $media = jsonDecode($data);
        $media_id = $media['media_id'];
        var_dump($media_id);
        $this->sendNews($media_id,'109',$access_token);
    }

    public function sendNews($media_id = NULL,$group_id = NULL,$access_token = NULL){
        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token={$access_token}";
        $data = array(
            'touser'=>array(
                    'ofxyzs7LP1H3V5gBez8Qd-Ia6m4o',
                    'ofxyzs1kTDbj6CBjp5-Lr15wJvdI',
                    'ofxyzs7tmxLDvgMjPpHl20CqjSuE',
                    'ofxyzs6IiHmZrmrHdet4dnfTQriY',
                    'ofxyzs8tZS6CSWMrI9j8l1l_0L6Y',
                    'fxyzs6W9LYpmdTbGQ7oO9HiVByU'
            ),
            'mpnews'=>array(
                'media_id'=>$media_id
            ),
            'msgtype'=>'mpnews'
        );
        $data = jsonEncode($data);
        //print_r($data);
        $data = postCurl($url,$data);
    }
}