<?php
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
//再解析str变量
$json = json_decode($str,true);//进行json_decode

//var_dump($json['appmsgstat']['read_num']);exit();

$sql = "select * from `post` where `biz`='".$biz."' and `content_url` like '%".$sn."%'" ;
$post = $db->get_one($sql);
//根据biz和sn找到对应的文章

$read_num = $json['appmsgstat']['read_num'];//阅读量
$like_num = $json['appmsgstat']['like_num'];//点赞量
//在这里同样根据sn在采集队列表中删除对应的文章，代表这篇文章可以移出采集队列了
$sql = "delete from `tmplist` where `content_url` like '%".$sn."%'";
$db->query($sql);
            
//然后将阅读量和点赞量更新到文章表中。
$msg['readNum'] = $read_num ;
$msg['likeNum'] = $like_num ;
$ret = $db->update(Weixin::POST , $msg , "content_url like '%". $sn . "%' ");

exit(json_encode($msg));//可以显示在anyproxy的终端里
?>