<?php
require_once ("Weixin.php");
$str = $_POST['str'];
$url = $_POST['url'];//先获取到两个POST变量

$weixin = new Weixin();
$weixin->log("getMsgJson");
//先针对url参数进行操作
parse_str(parse_url(htmlspecialchars_decode(urldecode($url)),PHP_URL_QUERY ),$query);//解析url地址
$biz = $query['__biz'];//得到公众号的biz



//再解析str变量
$json = json_decode($str,true);//首先进行json_decode

//var_dump($json);exit();
$weixin->log(4);
if(!$json) {

    $json = json_decode(htmlspecialchars_decode($str), true);//如果不成功，就增加一步htmlspecialchars_decode
}

//接下来进行以下操作
//从数据库中查询biz是否已经存在，如果不存在则插入，这代表着我们新添加了一个采集目标公众号。
$name = trim($json['name']);
$des = trim($json['des']);
$icon = urldecode( trim($json['icon']) );
echo "***********************************";

$weixin->updateMp($biz, $name, $des, $icon);