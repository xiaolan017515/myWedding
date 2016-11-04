<?php

class ManagerController{
	
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


    /**
    *
    *@descripe 获取销售员的列表
    */
    public function getSaleList(){
        $type = isset($_POST['type']) ? $_POST['type'] : NULL;
        $back_info = $this->back_info;
        if (!is_null($type)){
            $sql = "select a.nickname,a.id,a.name,a.phone,avg(b.score) as score
                    FROM sale a left JOIN  score b 
                    on a.id = b.belong_id and b.belong_role = 2 
                    where a.source = '%s'  GROUP BY a.id ";
            $sql = sprintf($sql,$type);
            $data = $this->conn->select($sql);    
            $data = isset($data) ? $data : array();
            $back_info['status'] = true;
            $back_info['msg'] = 'ok';
            $back_info['error_code'] = 0;
            $back_info['list'] = $data;
        } 
        echo jsonEncode($back_info);
    }



    /**
    *
    *@descripe根据销售员id,查询销售员详情 
    */

    public function saleDetail(){
        $sale_id = isset($_POST['sid']) ? $_POST['sid'] : NULL;
        $back_info = $this->back_info;
        if(!is_null($sale_id)){
            $sale = new SaleController();
            $data['sale'] = $sale->getSaleInfo($sale_id);
            $data['custom'] = $sale->getCustomInfo($sale_id);
            
            $back_info['status'] = true;
            $back_info['msg'] = 'ok';
            $back_info['error_code'] = 0;
            $back_info['sale'] = isset($data['sale']) ? $data['sale'] : array();
            $back_info['custom'] = isset($data['custom']) ? $data['custom'] : array();

        }
        echo jsonEncode($data);
    }


    /**
    *
    *@descripe 客户列表
    */

    public function getCustomList(){
        $type = isset($_POST['type']) ? $_POST['type'] : NULL;
        $back_info = $this->back_info;
        if(!is_null($type)){
            $sql = "select a.nickname,a.id,a.name,a.phone,a.type,b.name as sale_name from custom a LEFT JOIN sale b on a.scene = b.scene where a.source = '%s' order by  a.scene";
            $sql = sprintf($sql,$type);
            $data = $this->conn->select($sql);
            
            $back_info = $this->success;            
            $back_info['list'] = isset($data) ? $data : array();
        } 
        echo jsonEncode($data);
    }


    /**
    *
    *@descripe 客户分析
    */

    public function customAnaly(){
        $type = isset($_POST['type']) ? $_POST['type'] : NULL;
        $back_info = $this->back_info;
        if(!is_null($type)){
            $back_info = $this->success;
            //查询男性的人数
            $sql = "select id as num  from custom where source = '%s' and  sex = '%s' ";
            $sql = sprintf($sql,$type,'男');
            $list = $this->conn->select($sql);
            $back_info['man'] = count($list);

            //查询女性的人数
            $sql = "select id as num  from custom where source = '%s' and  sex = '%s' ";
            $sql = sprintf($sql,$type,'女');
            $list = $this->conn->select($sql);
            $back_info['woman'] =  count($list);
        } 
        echo jsonEncode($back_info);
    }


    /**
    *
    *@descripe 业绩统计
    */
    public function perforAnaly(){
        $type = isset($_POST['type']) ? $_POST['type'] : NULL;//贷款类型
        $back_info = $this->back_info;
        if(!is_null($type)){
            $back_info = $this->success;
            
            $today = date('Y-m-d');
            $next_day = date('Y-m-d',strtotime($today) + 86400);
            $today = strtotime($today);
            $next_day = strtotime($next_day);

            //今日总接待量

            $sql = "select type from custom where source = '%s' and unix_timestamp(time) between %d and %d ";
            $sql = sprintf($sql,$type,$today,$next_day);
            $custom_list = $this->conn->select($sql);
            $back_info['analy']['today_custom'] = count($custom_list);

            
            //今日总成交量

            $sql = "select type from custom where source = '%s' and unix_timestamp(pass_date) between %d and %d ";
            $sql = sprintf($sql,$type,$today,$next_day);
            $custom_list = $this->conn->select($sql);
            $back_info['analy']['today_total'] = count($custom_list);

            //总成交量
            $sql = "select type from custom where source = '%s' and type = %d ";
            $sql = sprintf($sql,$type,109);
            $custom_list = $this->conn->select($sql);
            $back_info['sale']['total'] = count($custom_list);

            //每个业务员的销量

            $sql = "select a.name,count(b.id) as num from sale a  
                    LEFT JOIN custom b on a.scene = b.scene  and b.type = 109 
                    where a.source = '%s' 
                    GROUP BY a.scene ";
            $sql = sprintf($sql,$type);
            $back_info['sale']['sale_list'] = $this->conn->select($sql);
            $back_info['sale']['sale_list'] = isset($back_info['sale']['sale_list']) ? $back_info['sale']['sale_list'] : array();
        }
        echo jsonEncode($back_info);
    }



    //获取管理员的二维码
    public function getQrcode(){
        //获取管理员的二维码图像路径
        $type = isset($_POST['type']) ? $_POST['type'] : NULL;
        $back_info = $this->back_info;
        if(!is_null($type)){
            $back_info = $this->success;
            $controller =  ucwords($type).'Controller';
            $handl = new $controller();
            $back_info['qcrode_path'] = $handl->createQrcode('99999');
        }
        echo jsonEncode($back_info);
    }


    /**
     * [sendMsg description]
     * @return [type] [description]
     * 管理员发送消息给管理员
     */
    
    public function sendMsg(){
        
        $data = isset($_POST['data']) ? $_POST['data'] : NULL;
        $type = isset($_POST['type']) ? $_POST['type'] : NULL;

        $back_info = $this->back_info;

        if ( (!is_null($data)) && (!is_null($type)) ){
            $data['time'] = date('Y-m-d H:i:s',time());
            $data['source'] = 6;
            $sql = "select id from sale ";
            $sql = sprintf($sql,$sale_id,$type);
            $sale_list = $this->conn->select($sql);
            if(count($sale_list)){
                foreach ($sale_list as $key => $value) {
                    $data['saleid'] = $value['id'];
                    $sql = insertString($data,'sale_msg');
                    $status = $this->conn->add($sql);
                }
            }
            if($status){
                $back_info = $this->success;
            }else{
                $back_info = array(
                    'error_code'=>-3,
                    'status'=>false,
                    'msg'=>'error sql',
                );
            }
        }
        echo jsonEncode($back_info);
    }

    public function xx(){
        $sql = " select a.content,b.name,b.phone  from sale_msg a 
                 inner join custom b on a.customid = b.id 
                 where a.source = 5 ";
        $list = $this->conn->select($sql);
        $str = "姓名,电话,预约详情\n";
        $str = setCharset($str);
        foreach ($list as $key => $value) {
            $str .= setCharset("{$value['name']},{$value['phone']},{$value['content']}\n");
        }
        $filename = date('Ymd').'.csv'; //设置文件名   
        exportCsv($filename,$str); //导出  
    }

}