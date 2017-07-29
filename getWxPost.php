<?php
require_once ("./db/db.php");
// $url = "http://www.baidu.com";
////echo "<script>setTimeout(function(){alert('test');},5000);</script>";
//echo "<script>setTimeout(function(){window.location.href='".$url."';},2000);</script>";//将下一个将要跳转的$url变成js脚本，由anyproxy注入到微信页面中。
//exit();
$db = new DB();
$db->write_log("getWxPost");

//getWxPost.php 当前页面为公众号文章页面时，读取这个程序
//首先删除采集队列表中load=1的行
$del_sql = "delete  from `tmplist` where `load` = 1 ";
$res = $db->query($del_sql);


//然后从队列表中按照“order by id asc”选择多行(注意这一行和上面的程序不一样)
$select_sql = "select * from `tmplist` order by id asc";
$ret = $db->get_all($select_sql);

        if(!empty($ret) && count($ret)>1){//(注意这一行和上面的程序不一样)
            //取得第0行的content_url字段
            $content_url = $ret[0]['content_url'];
            $url = $content_url;
            //var_dump($ret);
            //将第0行的load字段update为1
            $db->update('tmplist', ["`load`" => 1],  "`id` = ".$ret[0][id] );
        }else{
            //队列表还剩下最后一条时，就从存储公众号biz的表中取得一个biz，这里我在公众号表中设置了一个采集时间的time字段，按照正序排列之后，就得到时间戳最小的一个公众号记录，并取得它的biz
            $sql = "select * from weixin order by collect";
            $weixin = $db->get_one($sql);
            $biz = $weixin['biz'];
            $url = "http://mp.weixin.qq.com/mp/getmasssendmsg?__biz=".$biz."#wechat_webview_type=1&wechat_redirect";//拼接公众号历史消息url地址（第一种页面形式）
            //$url = "https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=".$biz."&scene=124#wechat_redirect";//拼接公众号历史消息url地址（第二种页面形式）
            //更新刚才提到的公众号表中的采集时间time字段为当前时间戳。
        }
        echo "<script>setTimeout(function(){window.location.href='".$url."';},2000);</script>";//将下一个将要跳转的$url变成js脚本，由anyproxy注入到微信页面中。
?>