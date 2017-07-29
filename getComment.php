<?php
//获取评论消息
require_once ("./Weixin.php");
require_once ("./db/db.php");

$db = new DB();
$db->write_log("getMsgExt");


$str = $_POST['str'];
$url = $_POST['url'];//先获取到两个POST变量
//先针对url参数进行操作
parse_str(parse_url(htmlspecialchars_decode(urldecode($url)),PHP_URL_QUERY ),$query);//解析url地址

$biz = $query['__biz'];//得到公众号的biz
$sn = $query['sn'];
$mid = $query['appmsgid'];
//再解析str变量
$json = json_decode($str,true);//进行json_decode

//var_dump($json['appmsgstat']['read_num']);exit();


$comment = json_encode($json['elected_comment']);
            
//将评论内容更新到文章表中。
$msg['comment'] = $comment ;
$ret = $db->update(Weixin::POST , $msg , "mid = '{$mid}' ");

exit(json_encode($msg));//可以显示在anyproxy的终端里
?>