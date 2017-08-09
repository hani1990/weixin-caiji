<?php

/**
 * Created by PhpStorm.
 * User: liuhan
 * Date: 17/3/9
 * Time: 下午10:36
 */
require_once "./db/db.php";
class Weixin
{
    //微信表
    const WEIXIN = 'weixin';
    //队列表
    const QUEUE = 'tmplist';
    //文章表
    const POST = 'post';

    public $db ;

    public function __construct(){
        $this->db = new DB();
    }

    //从数据库中查询biz是否已经存在，如果不存在则插入，这代表着我们新添加了一个采集目标公众号。
    public  function exitBiz($biz){
        $sql = "select * from ".Weixin::WEIXIN." where biz = '{$biz}' ";
        $ret = $this->db->get_one($sql);
        if($ret){
            //已经存在
            return false;
        }else{
            return $this->db->insert(Weixin::WEIXIN, ['biz' => $biz, 'collect' => time() ]);
        }

    }

    public function updateMp($biz, $name = '', $des = '', $icon = ''){
            
        return $this->db->update(Weixin::WEIXIN, ['name' => $name, 'des' => $des, 'icon' => $icon ], "biz = '{$biz}' ");
    }

    //在这里将图文消息链接地址插入到采集队列库中
    public  function addQueue($url){

        return $this->db->insert(Weixin::QUEUE, ['content_url' => $url] );
    }

    //在这里根据$content_url从数据库中判断一下是否重复
    public  function exitContentUrl($url){

        $sql = "select * from ". Weixin::POST ." where content_url =  '{$url}' ";
        $ret = $this->db->get_one($sql);
        $this->db->write_log("------------".json_encode($ret));
        return $ret;
    }

    //微信文章入库
    public  function addPost($data){
        return $this->db->insert(Weixin::POST, $data);
    }

    //log
    public function log($str){
        $this->db->write_log($str);
    }
}
