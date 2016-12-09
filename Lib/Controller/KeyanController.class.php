<?php

/**
*@const TOKEN zhengfeng
*@class FangdaiController
*@desctipe 科演公众号响应类
*@author qyx
*/

class KeyanController extends WeixinController{
    private $appid;//微信公众号的appid
    private $secret;//
    private $access_token;//
    private $token;//微信token
    private $conn;//数据库链接
    private $custom;//客户控制器
    private $token_path;//token_path
    private $sale;//销售控制器
    public $dir;//各种文件的前缀
    private $source;

    public function __construct(){
        $this->token = 'keyan';
        $this->appid = 'wx6cc8fb717e854d65';
        $this->secret = 'ca9f3a136c86420bc33adaa56a927455';
        $this->conn =  db::getConn();
        $this->custom = new CustomController();
        $this->sale = new SaleController();
        $this->source = 'keyan';
        $this->dir = __DIR__.'/../Public/keyan';
        if(!is_dir($this->dir)){
            mkdir($this->dir,0755,true);
        }
        $this->getToken();
    }
    public function index(){
    	//$this->valid();
        $this->responseMag();
    }

    /**
    *@param null
    *@return null
    *@descripe  响应客户操作
    */

    public function responseMag(){
        if($this->checkSignature($this->token)){
            $post_str = $GLOBALS["HTTP_RAW_POST_DATA"];
            if(!empty($post_str)){
                $post_str = simplexml_load_string($post_str, 'SimpleXMLElement', LIBXML_NOCDATA);
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
                        $qr = 1;//默认场景值为999

                        //判断是否已注册用户
                        $custom = $this->custom->checkCustom($msg['from']);
                        
                        //是否已注册销售员
                        $sale = $this->sale->checkSale($msg['from']);
                        
                        //判断是否是扫码关注
                        if (isset($post_str->EventKey) && ($post_str->EventKey !== NULL)){
                                $qrscene = explode('_', $post_str->EventKey);
                                $qr = isset($qrscene[1]) ? $qrscene[1] : 1;
                        }

                        //销售
                        if($qr == '99999' || $sale['id'] ){
                            $this->sSubAction($msg,$sale);
                        }else if( ($qr == "fangdai" && $custom['id'] == 0) || ($custom['type'] == '100') ){
                                //管理员
				                $sql = '';
                                if($custom['id'] == 0){
                                    $base_info = $this->getInfo($this->access_token,$msg['from']);
                                    $custom_data = array(
                                        'name'=>'0',
                                        'openid'=>$msg['from'].'',
                                        'sex'=>$base_info['sex'],
                                        'idcard'=>'0',
                                        'phone'=>'0',
                                        'type'=>100,
                                        'marry'=>0,
                                        'scene'=>$qr,
                                        'time'=>date('Y-m-d H:i:s',time()),
                                        'source'=>$this->source,
                                        'head_img'=>$base_info['headimgurl'],
                                        'pass_date'=>'0000-00-00 00:00:00',
                                        'nickname'=>$base_info['nickname']
                                    );

                                    $sql = insertString($custom_data,'custom');
                                    $this->conn->add($sql);
                                }else{
                                    $sql = "update custom set type = %d where openid = '%s' ";
                                    $sql = sprintf($sql,100,$msg['from']);
                                    writeLog($sql);
                                    $this->conn->update($sql);
                                }
                                $this->updateUserGroup($msg['from'].'',100);
                                $str = "欢迎你,管理员,菜单将更新!";
                                echo $this->textTpl($msg['from'],$msg['to'],time(),'text',$str);
                        }else {
                         	//客户
                            $this->cSubAction($msg,$qr,$custom);
                            $this->sendNew($msg);
                        }
                    }
                }else{
                    //$this->sendNew($msg);
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
                'source'=>$this->source,
                'head_img'=>$base_info['headimgurl'],
                'pass_date'=>'0000-00-00 00:00:00',
                'nickname'=>$base_info['nickname']
            );
            $custom = $base_info;
            $custom['id'] = $this->custom->registCustom($custom_data);
        }else{
            $sql = "update custom set scene = %d where id = %d ";
            $sql = sprintf($sql,$qr,$custom['id']);
            $this->conn->update($sql);
            //$this->updateUserGroup($custom['openid'],$custom['type']);
        }
        //$sale = $this->custom->querySale($custom['id']);
        //$data = $this->saleTel($msg['from'],$sale,$custom);
        //$str = $this->sendTemMsg($data,$this->access_token);
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
                'source'=>'tlm',
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

        $result = $this->updateUserGroup($msg['from'].'',101);
        $str = "欢迎你,销售员！请重新进入,菜单将更新!";
        echo $this->textTpl($msg['from'],$msg['to'],time(),'text',$str);
    }
    
    //发送专属销售员的基本信息
    public function saleTel($open_id,$sale,$custom){
        $tem = array(
            'touser'=>$open_id.'',
            'template_id'=>SALETEL,
            'topcolor'=>'#F08300',
            'data'=>array(
                'first'=>array(
                    'value'=>"恭喜您关注成功",
                    'color'=>'#F08300'
                ),               
                'keyword1'=>array(
                    'value'=>$custom['nickname'],
                    'color'=>'#F08300'
                ),
                'keyword2'=>array(
                    'value'=>date('Y-m-d H:i:s'),
                    'color'=>'#F08300'
                ),
                'remark'=>array(
                    'value'=>"您的专属销售:{$sale['name']},电话:{$sale['phone']},评分:{$sale['score']}",
                    'color'=>'#F08300'
                )
            )
        );
        return jsonEncode($tem,NULL);
    }

    //发送专属客户经理的基本信息
    public function serverTel($open_id,$data){
        $tem = array(
            'touser'=>$open_id.'',
            'template_id'=>"oCWKBn-JynHzJxBxNNoVld3o-wgrueMe2lf_EOZ41-E",
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
            'template_id'=>"FIicYQMYfIotBhQnJDNdZg6HCSq01sZyBbJP4Rc4Qco",
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
    *@descripe 客户分组
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
    *@descripe 更新客户分组
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
    *@param  null
    *@return  null
    *@descripe 取得access_token
    */
    public function getJsapi(){
        if(!is_dir($this->dir.'/jsapi')){
            mkdir($this->dir.'/jsapi',0755,true);
        }
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$this->access_token}&type=jsapi";
        $token = $this->readToken($this->dir.'/jsapi');
        $ticket = null;
        $time = time();
        if(!is_null($token)){
            if($token['time'] < $time || is_null($token['access_token'])){
                $data = getCurl($url);
                $data = json_decode($data,true);
                $ticket = $data['ticket'];
                $this->writeToken($this->dir.'/jsapi',$ticket,$time);
            }else{
                $ticket = $token['access_token'];
            }
        }
        echo jsonEncode(array('ticket'=>$ticket));
    }  

    /*
    *
    *@descripe 生成微信二维码
    */
    public function createQrcode($scene_id = '99999'){
        //$scene_id = isset($_POST['id']) ? $_POST['id'] : 1;
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
            //var_dump($data);
            $data = json_decode($data,true);
            $ticket = urlencode($data['ticket']);
            $ticket_url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket={$ticket}";
            if(!is_dir($this->dir.'/qrcode')){
                mkdir($this->dir.'/qrcode',0755,true);
            }
            
            $file_name = $this->dir.'/qrcode'."/{$scene_id}.jpg";
            
            if(!file_exists($file_name)){
                uploadImg($ticket_url,$file_name);
            }
            $qrcode_path = $file_name;
        }
        return $qrcode_path;
    }


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

            $sql = "select url from menu where id = '%d' and source = '%s' ";
            $sql = sprintf($sql,$state,$this->source);
            $url = $this->conn->find($sql);

            $url = isset($url['url']) ? $url['url'] : NULL;
            if(!is_null($url)){
               header("location:$url");
               exit();
            }else{
                 header("location:../404.html");
            } 
        }else{
              header("location:../404.html");
        }
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

    public function sendNew($msg){
         $base_info = $this->getInfo($this->access_token,$msg['from']);
        $data = array(
            '0'=>array(
                "Title"=>"欢迎“{$base_info['nickname']}”关注柯演装饰V慧品！", 
                "Description"=>"欢迎“{$base_info['nickname']}”关注天伦美V慧品！", 
                "PicUrl"=>"http://wx.iisun.net/hpjr/Lib/Tml/keyan.png", 
                "Url" =>"‘’http://wx.iisun.net/hpjr/keyan/sucai/keyandatu.html"
            ),
            '1'=>array(
                "Title"=>"【慧品沙龙】各种知识型文化活动，试探参与者的视觉、触觉和味觉", 
                "Description"=>"各种知识型文化活动，试探参与者的视觉、触觉和味觉.", 
                "PicUrl"=>"http://wx.iisun.net/hpjr/Lib/Tml/shalong.png", 
                "Url" =>"http://wx.iisun.net/hpsl/huipinshalong/html/shalonghui/shalongyinxiang/shalongyinxiang.html"
            ),
            
            '2'=>array(
                "Title"=>"【慧品·企业文化沙龙】面向企业提供集文化、娱乐、互动为一体的企业文化拓展产品", 
                "Description"=>"面向企业提供集文化、娱乐、互动为一体的企业文化拓展产品", 
                "PicUrl"=>"http://wx.iisun.net/hpjr/Lib/Tml/shalong.png", 
                "Url" =>"http://wx.iisun.net/hpsl/huipinshalong/html/shalonghui/qiyewenhuashalong/wenhuashalong.html"
            ),
            '3'=>array(
                "Title"=>"关于我们", 
                "Description"=>"面向企业提供集文化、娱乐、互动为一体的企业文化拓展产品", 
                "PicUrl"=>"http://wx.iisun.net/hpjr/Lib/Tml/logo.png", 
                "Url" =>"http://www.iisun.net/"
            ),


        );
        echo $this->imgTextTel($msg['from'],$msg['to'],time(),count($data),$data);

    }


    /**
     * [senLoanMsg description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     * @descripe 发送贷款进度更新详情
     */
    
    public function senLoanMsg($data){
        $tem = array(
            'touser'=>$data['openid'].'',
            'template_id'=>'-zVZODttsnkDwhYutzvtJiHk4pgm5PdDp_PZ_6EdbrY',
            'topcolor'=>'#F08300',
            'data'=>array(
                'first'=>array(
                    'value'=>"您的贷款申请进度已更新，请查看详情",
                    'color'=>'#F08300'
                ),               
                'keyword1'=>array(
                    'value'=>$data['keyword1'],
                    'color'=>'#F08300'
                ),
                'keyword2'=>array(
                    'value'=>$data['keyword2'],
                    'color'=>'#F08300'
                ),
                'keyword3'=>array(
                    'value'=>$data['keyword3'],
                    'color'=>'#F08300'
                ),
                'keyword4'=>array(
                    'value'=>date('Y-m-d H:i:s'),
                    'color'=>'#F08300'
                ),
                'remark'=>array(
                    'value'=>"",
                    'color'=>'#F08300'
                )
            )
        );
        return jsonEncode($tem,NULL);
    }
}
