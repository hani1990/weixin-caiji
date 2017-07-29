<?php
/**
 * Created by PhpStorm.
 * User: liuhan
 * Date: 17/3/15
 * Time: 下午7:56
 */
require_once './vendor/autoload.php';

/*
 * 通过anyproxy 传过来
 * @param cookie
 * @param biz
 * @max_frommsgid 最大  frommsgid
 * */

$cookie = "wap_sid=COj0gS8SQEpaYWMzc1VqMzhmTlIyOWFSX1RabzFpdG5PNmNjdW5yZTNZZjFrVy1fNmM0Z3N5NWQ0RkxuTmc1b3BTZTZ4TlMYBCCkFCiVwca6CzDC5aTGBQ==; wap_sid2=COj0gS8SiAFpbTdLQ0ZwZjl5M2RhNnVWY2JZZEo5R2FrVTF3alktU1Ywc0xiaExHcDRqVUxHNnV3MzZQNU9UVXRZOXhnczc0YjFQbWNYd291SERuOHdnaUdMajdIZnYzQmZWVi0waFk1S2RhUkhaUGJMUnprVVRMeU4ydjZITTNfYk5sbnQ2U2dBTUFBQX5+; rewardsn=629c24fc10abc6700b87; wxticket=3885162128; wxticketkey=c9c3ee7be321351b34f2ab1a05a42cf17160fea711912780637482f900862c1a; wxtokenkey=d94acf3da00c7d0d7cd1a1f4cead34c37160fea711912780637482f900862c1a; pgv_pvid=4850831696";

$url = "http://mp.weixin.qq.com/mp/profile_ext?action=getmsg&__biz=MzA3NTU4MDA1Mw==&f=json&frommsgid=1000000266&count=10&scene=124&is_ok=1&uin=OTg1OTc0ODA%3D&key=618e49d7a9d493d23c2311bdc446e17e3ef34d909abcc28c77c1553b697ce8eb27c22a8cc4346d503d51bf69c6892db28cb8333644fc62735809d84bc842c2748014c3a83fe6204ad3e61c3b673eb2bf&pass_ticket=v3Gfzk74%2BAX1SYn%2B73LLBrGj4Co%2BgqHYzzOLQotQevI%3D&wxtoken=&x5=0&f=json";
parse_str(parse_url(htmlspecialchars_decode(urldecode($url)),PHP_URL_QUERY ),$query);//解析url地址
$biz = $query['__biz'];//得到公众号的biz
$frommsgid = $query['frommsgid'];
//do{

   echo  $msg_url = "http://mp.weixin.qq.com/mp/profile_ext?action=getmsg&__biz={$biz}&f=json&frommsgid={$frommsgid}&count=10&scene=124&is_ok=1&uin=OTg1OTc0ODA%3D&key=618e49d7a9d493d23c2311bdc446e17e3ef34d909abcc28c77c1553b697ce8eb27c22a8cc4346d503d51bf69c6892db28cb8333644fc62735809d84bc842c2748014c3a83fe6204ad3e61c3b673eb2bf&pass_ticket=v3Gfzk74%2BAX1SYn%2B73LLBrGj4Co%2BgqHYzzOLQotQevI%3D&wxtoken=&x5=0&f=json";
    $curl = new \Curl\Curl();
    $curl->setHeader('cookie', $cookie);
    $curl->get($msg_url);
    $frommsgid = $frommsgid - 10;
    if ($curl->error) {
        echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
    } else {
        echo 'Response:' . "\n";
        var_dump($curl->response->general_msg_list);
        $msg = $curl->response->general_msg_list;
        $curl->post("http://weixin.caiji:8999/getMsgJson.php", array(
            "url" => $msg_url,
            "str" => $msg,
        ));

    }

//}while(
//    $curl->response->msg_count > 0
//);
