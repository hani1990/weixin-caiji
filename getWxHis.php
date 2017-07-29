<?php
require_once "./db/db.php";
//getWxHis.php 当前页面为公众号历史消息时，读取这个程序
//在采集队列表中有一个load字段，当值等于1时代表正在被读取
//首先删除采集队列表中load=1的行
//然后从队列表中任意select一行
$db = new DB();
$db->write_log("getWxHis");
//$url = "http://www.baidu.com";
//echo "<script>setTimeout(function(){window.location.href='".$url."';},2000);</script>";//将下一个将要跳转的$url变成js脚本，由anyproxy注入到微信页面中。
$sql = "select * from `tmplist` ";
$res = $db->get_all($sql);
if(empty($res)){
    //队列表如果空了，就从存储公众号biz的表中取得一个biz，这里我在公众号表中设置了一个采集时间的time字段，按照正序排列之后，就得到时间戳最小的一个公众号记录，并取得它的biz
    $sql = "SELECT * FROM weixin ORDER BY collect DESC";
    $ret = $db->get_one($sql);
    $biz = $ret['biz'];
    $url = "http://mp.weixin.qq.com/mp/getmasssendmsg?__biz=".$biz."#wechat_webview_type=1&wechat_redirect";//拼接公众号历史消息url地址（第一种页面形式）
    //$url = "https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=".$biz."&scene=124#wechat_redirect";//拼接公众号历史消息url地址（第二种页面形式）
            
    //更新刚才提到的公众号表中的采集时间time字段为当前时间戳。
}else{
    //取得当前这一行的content_url字段
    $content_url = $res['content'];
    $url = $content_url;
    //将load字段update为1
    $db->update('tmplist', ['load' => 1], "id = ".$res['id']);
}
echo "<script>setTimeout(function(){window.location.href='".$url."';},2000);</script>";//将下一个将要跳转的$url变成js脚本，由anyproxy注入到微信页面中。
?>