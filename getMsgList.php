<?php
require_once './db/db.php';
require_once './vendor/autoload.php';
/**
 * Created by PhpStorm.
 * User: liuhan
 * Date: 17/3/10
 * Time: 下午10:15
 */
//$url = "/mp/profile_ext?action=getmsg%26__biz=MzA4MjEyNTA5Mw==%26f=json%26frommsgid=1000000251%26count=10%26scene=124%26is_ok=1%26uin=OTg1OTc0ODA%3D%26key=b70871c00611ead7c4f95e08b0533c26c442286769970359847342e201842febce05c2ea96fd166fbc7472a915506a495d4b14efabb0ad896e45fc187f9a56d6e640a41d4d2836e0e46579758ef5df3c%26pass_ticket=wFxXfJzLcBuzMHF1BUfv7QGDKlShy9DxkPbzXuNHAiY%3D%26wxtoken=%26x5=0%26f=json" ; //$_POST['url'];
//$cookie = "wap_sid=COj0gS8SQFdIZlFGQWRQX2U2RG1pcEx4enpzM25sN1J6NmZzYmNGSWNKbjRUcnZ3NERlcnRKcVdxN0lERDg4OGJJaVVjQWcYBCD9ESil/tW9CzD2+qTGBQ==; wap_sid2=COj0gS8SiAEyM19tXzBlT0FQVW5RcUlsN0hXSUc3cnlJdlpwQmNhZTk1YnlXOF9ubVEtM2I1WGlld2RmNXZmMVRtUEZJWjBRdGVZMGtkOTVzN1YxakFoVFk3djAweXozMVcySHQwS3NPWFVVaFVWVURzaWNCUEhFTHR4Ulg1MldsbDd0UGZVQWdBTUFBQX5+; rewardsn=629c24fc10abc6700b87; wxticket=3885162128; wxticketkey=c9c3ee7be321351b34f2ab1a05a42cf17160fea711912780637482f900862c1a; wxtokenkey=d94acf3da00c7d0d7cd1a1f4cead34c37160fea711912780637482f900862c1a; pgv_pvid=4850831696" ;//$_POST['cookie'];//先获取到两个POST变量
$url = "/mp/profile_ext?action=getmsg&__biz=MjM5MzM4NTUyOA==&f=json&frommsgid=999999999&count=10&scene=124&is_ok=1&uin=OTg1OTc0ODA%3D&key=f9128360d66942a370455fc6dec74dc67c9571a9602bb4513eea943197e1509199d7568ecee0da09eeb2764c9868050345c2ba083b86182d7eb48d0826f80f559c5aa23937e23728633bd7702c86f7d3&pass_ticket=wFxXfJzLcBuzMHF1BUfv7QGDKlShy9DxkPbzXuNHAiY%3D&wxtoken=&x5=0&f=json";
$cookie = "wap_sid=COj0gS8SQHVUZkVkaG9BWGlrdk9DVTJUSzlydTRRaUtJVzQtcS1QYTExRW03ODNCRnE0WkVxOElYa1VWRHNkSG9qSmhTYmkYBCD9ESi41KD1CDCrjqXGBQ==; wap_sid2=COj0gS8SiAFqRmtYN3FRaW9fbzQ0SG05THV6ZlNlRmpQRzJJN2Z6bEk3QVdLQ18xX0dINHlZVkxTcjNwWW1mc1o1VU42MDMzLWlRMDhIWU1vM3J0VGl5aUhiWjlTU2duQ2FqYUVUWUlQUkhrdFhnN29Iand1NW5wdDYxTXNtR21RMGh5dlRqYWdBTUFBQX5+; rewardsn=629c24fc10abc6700b87; wxticket=3885162128; wxticketkey=c9c3ee7be321351b34f2ab1a05a42cf17160fea711912780637482f900862c1a; wxtokenkey=d94acf3da00c7d0d7cd1a1f4cead34c37160fea711912780637482f900862c1a; pgv_pvid=4850831696";
parse_str(parse_url(htmlspecialchars_decode(urldecode($url)),PHP_URL_QUERY ),$query);//解析url地址
//var_dump($query);
$biz = $query['__biz'];//得到公众号的biz
$frommsgid = $query['frommsgid'];




/*
 * @param cookie
 * @param biz
 * @max_frommsgid 最大  frommsgid
 * */
//http://mp.weixin.qq.com/mp/profile_ext?action=getmsg&__biz=MzA3NTU4MDA1Mw==&f=json&frommsgid=1000000266&count=10&scene=124&is_ok=1&uin=OTg1OTc0ODA%3D&key=618e49d7a9d493d23c2311bdc446e17e3ef34d909abcc28c77c1553b697ce8eb27c22a8cc4346d503d51bf69c6892db28cb8333644fc62735809d84bc842c2748014c3a83fe6204ad3e61c3b673eb2bf&pass_ticket=v3Gfzk74%2BAX1SYn%2B73LLBrGj4Co%2BgqHYzzOLQotQevI%3D&wxtoken=&x5=0&f=json
//$cookie = "wap_sid=COj0gS8SQEpaYWMzc1VqMzhmTlIyOWFSX1RabzFpdG5PNmNjdW5yZTNZZjFrVy1fNmM0Z3N5NWQ0RkxuTmc1b3BTZTZ4TlMYBCCkFCiVwca6CzDC5aTGBQ==; wap_sid2=COj0gS8SiAFpbTdLQ0ZwZjl5M2RhNnVWY2JZZEo5R2FrVTF3alktU1Ywc0xiaExHcDRqVUxHNnV3MzZQNU9UVXRZOXhnczc0YjFQbWNYd291SERuOHdnaUdMajdIZnYzQmZWVi0waFk1S2RhUkhaUGJMUnprVVRMeU4ydjZITTNfYk5sbnQ2U2dBTUFBQX5+; rewardsn=629c24fc10abc6700b87; wxticket=3885162128; wxticketkey=c9c3ee7be321351b34f2ab1a05a42cf17160fea711912780637482f900862c1a; wxtokenkey=d94acf3da00c7d0d7cd1a1f4cead34c37160fea711912780637482f900862c1a; pgv_pvid=4850831696";
$i = 1;
do{
    $msg_list_url = "http://mp.weixin.qq.com/mp/profile_ext?action=getmsg&__biz={$biz}&f=json&frommsgid={$frommsgid}&count=10&scene=124&is_ok=1&uin=OTg1OTc0ODA%3D&key=618e49d7a9d493d23c2311bdc446e17e3ef34d909abcc28c77c1553b697ce8eb27c22a8cc4346d503d51bf69c6892db28cb8333644fc62735809d84bc842c2748014c3a83fe6204ad3e61c3b673eb2bf&pass_ticket=v3Gfzk74%2BAX1SYn%2B73LLBrGj4Co%2BgqHYzzOLQotQevI%3D&wxtoken=&x5=0&f=json";
    $curl = new \Curl\Curl();
    $curl->setHeader('cookie', $cookie);
    $curl->get($msg_list_url);
    if ($curl->error) {
        echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
    } else {
        //var_dump($curl->response);
        $msg_count = $curl->response->msg_count;
        echo "msg count {$msg_count} \n";
    }
    echo $frommsgid = $frommsgid - 10;
    sleep(1);
    $msg = $curl->response->general_msg_list;
    $curl->post("http://192.168.10.228:8999//getMsgJson.php", array(
        "url" => $msg_list_url,
        "str" => $msg,
    ));
    echo "第 ". $i++ . " 个历史列表链接";
}while( $msg_count > 0 );

//先针对url参数进行操作
//parse_str(parse_url(htmlspecialchars_decode(urldecode($url)),PHP_URL_QUERY ),$query);//解析url地址
//var_dump($query);
//微信历史列表URL
//http://mp.weixin.qq.com/mp/profile_ext?action=getmsg&__biz=MjM5NDA0NDkzOQ==&f=json&frommsgid=1000000&count=10&scene=124&is_ok=1&uin=OTg1OTc0ODA%3D&key=618e49d7a9d493d23c2311bdc446e17e3ef34d909abcc28c77c1553b697ce8eb27c22a8cc4346d503d51bf69c6892db28cb8333644fc62735809d84bc842c2748014c3a83fe6204ad3e61c3b673eb2bf&pass_ticket=v3Gfzk74%2BAX1SYn%2B73LLBrGj4Co%2BgqHYzzOLQotQevI%3D&wxtoken=&x5=0&f=json
/*
 * 需要拼接好参数之后 发送到 微信端去执行,直接请求是请求不到的,需要cookie
 * 替换 frommsgid 的值 1000000232 开始往下减, 每次减10,一直到 返回 为空
 * {
  "ret": 0,
  "errmsg": "ok",
  "msg_count": 0,
  "can_msg_continue": 0,
  "general_msg_list": "{\"list\":[]}"
}
*/
//var_dump($query);
//$msg_url = htmlspecialchars_decode(urldecode($url)) ;
//
////注入, 拼接历史文章列表地址
//$tmp = '';
//foreach ($query as $k => $v){
//    if($k != 'url'){
//        if($k == 'frommsgid'){
//            $v = $v -10;
//        }
//        $tmp .= $k.'='.$v.'&';
//    }
//
//}
//$tmp = substr($tmp, 0, -1);
//$msg_list_url = "http://mp.weixin.qq.com/mp/profile_ext?" . $tmp;

//$msg_list_url = "http://www.baidu.com";
//$db = new DB();
//$sql = "select * from history_list";
//$ret = $db->get_one($sql);
//$msg_list_url = $ret['list_url'] ;
//echo "<script>setTimeout(function(){window.location.href='".$msg_list_url."';},2000);</script>";//将下一个将要跳转的$url变成js脚本，由anyproxy注入到微信页面中。


?>