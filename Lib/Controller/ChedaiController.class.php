<?php

/**
*@const TOKEN chedai
*@class ChedaiController
*@desctipe 车贷公众号响应类
*@author qyx
*/

class ChedaiController extends WeixinController{

    private $appid;
    private $secret;
    private $access_token;
    private $token;
    private $conn;
    private $custom;
    private $token_path;
    private $sale;

    public function __construct(){
        $this->token = 'chedai';
        $this->appid = 'wx42b842721dee9c1b';
        $this->secret = '3fa603269e9259db94a43096be15a9c4';
        $this->conn =  db::getConn();
        $this->custom = new CustomController();
         $this->sale = new SaleController();
        $this->getToken();

    }


    public function index(){
    	$this->responseMag();
    }


    /**
    *@param null
    *@return null
    *@descripe  响应用户操作
    */

    public function responseMag(){
        
        if($this->checkSignature($this->token)){

            $post_str = $GLOBALS["HTTP_RAW_POST_DATA"];
            
            if(!empty($post_str)){
                
                $post_str = simplexml_load_string($post_str, 'SimpleXMLElement', LIBXML_NOCDATA);
                
                //获取微信发送来的XML格式的消息

                $msg['from'] = $post_str->FromUserName;//发送方帐号（一个OpenID） 
                $msg['type'] = $post_str->MsgType;//消息类型
                $msg['id'] = $post_str->MsgId;
                $msg['to'] = $post_str->ToUserName; 
                $msg['time'] = time();

                //如果是事件,
                if ($msg['type'] == 'event'){
                    $event = $post_str->Event;

                    //关注 
                    if  ($event == 'subscribe'){                        
                        $qr = 999;//默认场景值为999

                        //判断是否已注册用户
                        $custom = $this->custom->checkCustom($msg['from']);
                        
                        //是否已注册销售员
                        $sale = $this->sale->checkSale($msg['from']);
                        
                        //判断是否是扫码关注
                        if (isset($post_str->EventKey) && ($post_str->EventKey != NULL)){
                                $qrscene = explode('_', $post_str->EventKey);
                                $qr = $qrscene[1];
                        }
                        writeLog('qr:'.$qr);
                        //销售
                        if($qr == '99999' || $sale['id'] ){
                            $this->sSubAction($msg,$sale);
                        }else if( ($qr == 'chedai' && $custom['id'] == 0 ) || ($custom['type'] == '110') ){
                              if($custom['id'] == 0){
                                    $base_info = $this->getInfo($this->access_token,$msg['from']);
                                    $custom_data = array(
                                        'name'=>'0',
                                        'openid'=>$msg['from'].'',
                                        'sex'=>$base_info['sex'],
                                        'idcard'=>'0',
                                        'phone'=>'0',
                                        'type'=>110,
                                        'marry'=>0,
                                        'scene'=>$qr,
                                        'time'=>date('Y-m-d H:i:s',time()),
                                        'source'=>'chedai',
                                        'head_img'=>$base_info['headimgurl'],
                                        'pass_date'=>'0000-00-00 00:00:00',
                                        'nickname'=>$base_info['nickname']
                                    );
                                    $sql = insertString($custom_data,'custom');
                                    $this->conn->add($sql);
                                }else{
                                    $sql = "update custom set type = %d where openid = '%s' ";
                                    $sql = sprintf($sql,110,$msg['from']);
                                    $this->conn->update($sql);
                                }
                                $this->updateUserGroup($msg['from'].'',110);
                                $str = "欢迎你,管理员,菜单将更新!";
                                echo $this->textTpl($msg['from'],$msg['to'],time(),'text',$str);
                        }else{  
                            $this->cSubAction($msg,$qr,$custom);
                        }
                    }else if  ($event == 'LOCATION'){
                        $latitude = $post_str->Latitude;
                        $longitude = $post_str->Longitude;
                        $precision = $post_str->Precision;
                        //$str = "纬度:{$latitude},经度{$longitude},精度{$precision}";
                        //writeLog($str);
                    }
                }else{
                }
            }
        }
    }



    /**
    *
    *@descripe 客户关注公众号的
    */

    public function cSubAction($msg,$qr,$custom){
        if($custom['id'] == 0){
            $base_info = $this->getInfo($this->access_token,$msg['from']);
            $custom_data = array(
                'name'=>'0',
                'openid'=>$msg['from'],
                'sex'=>$base_info['sex'],
                'idcard'=>'0',
                'phone'=>'0',
                'type'=>0,
                'marry'=>0,
                'scene'=>$qr,
                'time'=>date('Y-m-d H:i:s',time()),
                'source'=>'chedai',
                'head_img'=>$base_info['headimgurl'],
                'pass_date'=>'0000-00-00 00:00:00',
                'nickname'=>$base_info['nickname']
            );
            $custom['id'] = $this->custom->registCustom($custom_data);
        }else{
            $sql = "update custom set scene = %d where id = %d ";
            $sql = sprintf($sql,$qr,$custom['id']);
            $this->conn->update($sql);
            $this->updateUserGroup($custom['openid'],$custom['type']);
        }
        $sale = $this->custom->querySale($custom['id']);
        $data = $this->saleTel($msg['from'],$sale);
        $str = $this->sendTemMsg($data,$this->access_token);
    }


    /**
    *
    *@descripe 销售关注公众号的
    */

    public function sSubAction($msg,$sale){
        if($sale['id'] == 0){
            $base_info = $this->getInfo($this->access_token,$msg['from']);
            $data = array(
                'name'=>0,
                'phone'=>0,
                'scene'=>0,
                'source'=>'chedai',
                'sex'=>$base_info['sex'],
                'idcard'=>0,
                'date'=>date('Y-m-d H:i:s'),
                'openid'=>$msg['from'],
                'nickname'=>$base_info['nickname'],
                'head_img'=>$base_info['headimgurl']
            );
            $this->conn->autocommit(false);

            $sql = insertString($data,'sale');
            writeLog($sql);
            $sale_id = $this->conn->add($sql);

            $sql = "update sale set scene = %d where id = %d ";
            $sql = sprintf($sql,$sale_id,$sale_id);
            $update = $this->conn->update($sql);
            if($sale_id && $update){
                $this->conn->commit();
            }else{
                $this->conn->rollback();
            }
            $this->conn->autocommit(true);
        }

        $result = $this->updateUserGroup($msg['from'].'',111);
        writeLog($result);
        $str = "欢迎你,销售员！请重新进入,菜单将更新!";
        echo $this->textTpl($msg['from'],$msg['to'],time(),'text',$str);
    }



    //发送专属销售员的基本信息
    public function saleTel($open_id,$data){
        $tem = array(
            'touser'=>$open_id.'',
            'template_id'=>"9fC0NAAtk-dlSZtI_QSAJmzOtMLE3ilW3O-R8bEvCiA",
            'topcolor'=>'#F08300',
            'data'=>array(
                'name'=>array(
                    'value'=>$data['name'],
                    'color'=>'#F08300'
                ),
                'phone'=>array(
                    'value'=>$data['phone'],
                    'color'=>'#F08300'
                ),
                'score'=>array(
                    'value'=>$data['score'],
                    'color'=>'#F08300'
                ),
            )
        );
        return jsonEncode($tem,NULL);
    }

    //发送专属客户经理的基本信息及银行贷款详情
    public function serverTel($open_id,$data){
        $tem = array(
            'touser'=>$open_id.'',
            'template_id'=>"QJE-hzbqpzdboeXA5Oke5C4yF_cvNps1uHXl2RcSlmI",
            'topcolor'=>'#F08300',
            'data'=>array(
                'name'=>array(
                    'value'=>$data['name'],
                    'color'=>'#F08300'
                ),
                'phone'=>array(
                    'value'=>$data['phone']."\r\n",
                    'color'=>'#F08300'
                ),
                'content'=>array(
                    'value'=>$data['content'],
                    'color'=>'#F08300'
                )
            )
        );
        return jsonEncode($tem,NULL);
    }

    //发送贷款操作的的基本信息
    public function loanInfo($open_id,$data){
        $tem = array(
            'touser'=>$open_id.'',
            'template_id'=>"SfCds5HWul_8dqX5isVCr3DIg8R6kaNuVmm-7BUmERA",
            'topcolor'=>'#F08300',
            'data'=>array(
                'do'=>array(
                    'value'=>$data['do'],
                    'color'=>'#F08300'
                ),
                'status'=>array(
                    'value'=>$data['status'],
                    'color'=>'#F08300'
                )

            )
        );
        return jsonEncode($tem,NULL);
    }



    /**
    *@param null
    *@return  json      服务器返回消息
    *@descripe 用户分组
    */

    public function userGroup(){
        $open_id = isset($_SESSION['openid']) ? $_SESSION['openid'] : NULL;
        $group_id = isset($_POST['group_id']) ? $_POST['group_id'] : 109;
        $back_info = json_encode(array('errcode'=>-1));
        if(!is_null($open_id)){
            $group_url = "https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token={$this->access_token}";
            $data = array(
                'openid'=>$open_id,
                'to_groupid'=>$group_id,
            );
            $data = json_encode($data);
            $back_info = postCurl($group_url,$data);
            $back_info = json_decode($back_info,true);
            if ($back_info['errcode'] == 0){
               $custom = array('type'=>$group_id);
                $this->custom->updateCustom($_SESSION['id'],$custom);
            }
            $back_info = json_encode($back_info);
        }
        echo $back_info;
    }


    /**
    *@param null
    *@return  json      服务器返回消息
    *@descripe 更新用户分组
    */

    public function updateUserGroup($open_id = NULL,$group_id = NULL){
        $open_id = isset($open_id) ? $open_id : NULL ;
        $group_id = isset($group_id) ? $group_id : 0;
        $back_info = null;
        if(!is_null($open_id)){
            $group_url = "https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token={$this->access_token}";
            $data = array(
                'openid'=>$open_id,
                'to_groupid'=>$group_id,
            );
            $data = json_encode($data);
            $back_info = postCurl($group_url,$data);
        }
        return $back_info;
    }


    /**
    *@param  null
    *@return  null
    *@descripe 取得access_token
    */

    public function getToken(){
        if(!is_dir(CHEDAI_TOKEN)){
            mkdir(CHEDAI_TOKEN,0755,true);
        }
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->secret}";
        $token = $this->readToken(CHEDAI_TOKEN);
        $time = time();
        if(!is_null($token)){
            if($token['time'] < $time || is_null($token['access_token'])){
                $data = getCurl($url);
                $data = json_decode($data,true);
                $this->access_token = $data['access_token'];
                $this->writeToken(CHEDAI_TOKEN,$this->access_token,$time);
            }else{
                $this->access_token = $token['access_token'];
            }
        }
        return $this->access_token;
    }



    /**
    *@descripe  获取用户code ,并得到openid.
    */

    /**
    *@descripe  获取客户code ,并得到openid.
    */

    public function receiveCode(){
        $code = isset($_GET['code']) ? $_GET['code'] : NULL;
        $state = isset($_GET['state']) ? intval($_GET['state']) : NULL;
        if(!is_null($code) &&  !is_null($state)){
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appid}&secret={$this->secret}&code={$code}&grant_type=authorization_code";
            $data = getCurl($url);
            $data = json_decode($data,true);
            $openid = $data['openid'];
            $custom = $this->custom->checkCustom($openid);
            $sale = $this->sale->checkSale($openid);
            $id = null;
            if($sale['id'] != 0){
                $id = $sale['id'];
            }else if($custom['id'] != 0){
                $id = $custom['id'];
            }
            $_SESSION['openid'] = $openid;
            $_SESSION['id'] = $id;

            $sql = "select url from menu where id = '%d' ";
            $sql = sprintf($sql,$state);
            $url = $this->conn->find($sql);

            $url = isset($url['url']) ? CHEDAI_PATH.$url['url'] : NULL;
            if(!is_null($url)){
               header("location:$url");
               exit();
            }else{
                echo "<center>暂无页面</center>";
            } 
        }else{
             echo "<center>暂无页面</center>";
        }
    }


    /*
    *
    *@descripe 生成微信二维码
    */

    public function createQrcode($scene_id){
        $qrcode_path = null;
        if(!is_null($scene_id)){
            $ticket_url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$this->access_token}"; 
            $ticket = array(
                'action_name'=>'QR_LIMIT_STR_SCENE',
                'action_info'=>array(
                    'scene'=>array(
                        'scene_str'=>$scene_id.''
                    )
                )
            );
            $ticket = json_encode($ticket);
            $data = postCurl($ticket_url,$ticket);
            $data = json_decode($data,true);
            $ticket = urlencode($data['ticket']);
            $ticket_url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket={$ticket}";
            if(!is_dir(CHEDAI_QRCODE)){
                mkdir(CHEDAI_QRCODE,0777,true);
            }
            $file_name = CHEDAI_QRCODE."/{$scene_id}.jpg";
            if(!file_exists($file_name)){
                uploadImg($ticket_url,$file_name);
            }
            $qrcode_path = $file_name;
        }
        return $qrcode_path;
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