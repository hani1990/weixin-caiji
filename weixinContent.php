<?php
include_once ('./lib/simpledom/HtmlDom.php');

use simple\dom\HtmlDom;

/**
 * Created by PhpStorm.
 * User: liuhan
 * Date: 17/4/9
 * Time: 下午4:00
 */

      function get_content($url){
//        $http = new Client(null);
//        $response = $http->get($url);
        $dom = HtmlDom::file_get_html($url);
        if(!$dom){
            return false;
        }
      //  $data['title'] = $dom->find("title", 0)->plaintext;
        $content = $dom->find("div[id=js_content]", 0)->outertext;
        $content = str_replace("data-src", "src", $content);
        $find = ['https://mmbiz.qlogo.cn','http://mmbiz.qlogo.cn', 'http://mmbiz.qpic.cn'];
        $data['content'] = str_replace($find, "http://www.zhaoshu114.com/index.php?c=mp&a=getimg&imageUrl=http://mmbiz.qpic.cn", $content);
        return $data['content'];
    }

?>