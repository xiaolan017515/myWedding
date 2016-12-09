<?php
require_once "jssdk.php";
$jssdk = new JSSDK("wxd2034fbae0f5537a", "a6f8e7af068875103defa88c651f5525");
$signPackage = $jssdk->GetSignPackage();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>周鑫建LOVE高霞</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <script src="//wximg.qq.com/wxp/libs/wxmoment/0.0.4/wxmoment.min.js"></script>
    <script src="http://api.map.baidu.com/api?v=2.0&ak=31hqsRiiq7OgILcH1Zd7lUzT"></script>
    <script src="//cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>
    <script src="//cdn.bootcss.com/Swiper/3.4.0/js/swiper.jquery.min.js"></script>
    
    <link rel="stylesheet" href="../web/css/common.css"/>
    <link rel="stylesheet" href="../build/index.min.css"/>
    
</head>
<body>
    <div id="main" class="container"></div>
    <div id="loading"></div>
    <div id="audio_btn" class="off" dstyle="display: block;">
        <audio loop="" src="http://zhouxinjian.top/web/img/ms.mp3" id="media" preload="none"></audio>
    </div>
    <img src="../web/img/back.png" id="goback"/>
    <script src="../web/js/plus/text_plugin.js"></script>
    <script src="../build/common.bundle.js"></script>
    <script src="../build/index.min.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script>
      wx.config({
        debug: false,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: '<?php echo $signPackage["timestamp"];?>',
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            'onMenuShareTimeline',
            'onMenuShareAppMessage'
        ]
      });

      wx.ready(function () {
        wx.onMenuShareAppMessage({
            title: '周鑫建与高霞的婚礼邀请',
            desc: '猛戳进来围观周鑫建与高霞的婚礼吧！！！！！！',
            link: 'http://zhouxinjian.top/wedding/',
            imgUrl: 'http://zhouxinjian.top/web/img/share.jpg',
            trigger: function (res) {
                // alert('用户点击发送给朋友123');
            },
            success: function (res) {
                // alert('已分享123');
            },
            cancel: function (res) {
                alert('取消了分享');
            },
            fail: function (res) {
                alert(JSON.stringify(res));
            }
        });
        wx.onMenuShareTimeline({
            title: '周鑫建与高霞的婚礼邀请',
            link: 'http://zhouxinjian.top/wedding/',
            imgUrl: 'http://zhouxinjian.top/web/img/share.jpg',
            trigger: function (res) {
                // alert('用户点击分享到朋友圈');
            },
            success: function (res) {
                // alert('已分享');
            },
            cancel: function (res) {
                alert('取消了分享');
            },
            fail: function (res) {
                alert(JSON.stringify(res));
            }
        });
      });
    </script>
</body>
</html>
