'use strict';

module.exports = {
  
  summary: 'the default rule for AnyProxy',
  
  /**
   * 
   * 
   * @param {object} requestDetail
   * @param {string} requestDetail.protocol
   * @param {object} requestDetail.requestOptions
   * @param {object} requestDetail.requestData
   * @param {object} requestDetail.response
   * @param {number} requestDetail.response.statusCode
   * @param {object} requestDetail.response.header
   * @param {buffer} requestDetail.response.body
   * @returns
   */
  *beforeSendRequest(requestDetail) {
    return null;
  },





  /**
   * 
   * 
   * @param {any} requestDetail 
   * @returns 
   */
  *beforeDealHttpsRequest(requestDetail) {
    return false;
  },

  /**
   * 
   * 
   * @param {any} requestDetail 
   * @param {any} error 
   * @returns 
   */
  *onError(requestDetail, error) {
    return null;
  },


  /**
   * 
   * 
   * @param {any} requestDetail 
   * @param {any} error 
   * @returns 
   */
  *onConnectError(requestDetail, error) {
    return null;
  },
};


//将json发送到服务器，str为json内容，url为历史消息页面地址，path是接收程序的路径和文件名
    function HttpPost(str,url,path) {
    var http = require('http');
    var data = {
        // str: encodeURIComponent(str),
        // url: encodeURIComponent(url)
        str:str,
        url:url
    };
    var content = require('querystring').stringify(data);
  
    var options = {
        method: "POST",
        host: "127.0.0.1",//注意没有http://，这是服务器的域名。
        port: 8008,
        path: "/"+path,//接收程序的路径和文件名
         headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                "Content-Length": content.length
            }
    };
   // console.log(content);
    console.log("++++++++++++++++++");
    console.log(options);
    var req = http.request(options, function (res) {
        console.log('STATUS:' + res.statusCode);
        //res.setEncoding('utf8');
        res.on('data', function (chunk) {
            console.log('BODY: ' + chunk);
        });
    });
    req.on('error', function (e) {
        console.log('problem with request: ' + e.message);
    });
      //加入post数据
    req.write(content);

    req.end();
}'use strict';

module.exports = {
  
  summary: 'the default rule for AnyProxy',
  
  /**
   * 
   * 
   * @param {object} requestDetail
   * @param {string} requestDetail.protocol
   * @param {object} requestDetail.requestOptions
   * @param {object} requestDetail.requestData
   * @param {object} requestDetail.response
   * @param {number} requestDetail.response.statusCode
   * @param {object} requestDetail.response.header
   * @param {buffer} requestDetail.response.body
   * @returns
   */
  *beforeSendRequest(requestDetail) {
    return null;
  },


  /**
   * 
   * 
   * @param {object} requestDetail
   * @param {object} responseDetail
   */
  *beforeSendResponse(requestDetail, responseDetail) {

     var request_url = "http://127.0.0.1:8008/";
     //https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=MzIzNzQyMzA1OA==&scene=124&devicetype=iPhone+OS9.3.2&version=16050320&lang=zh_CN&nettype=WIFI&a8scene=3&fontScale=100&pass_ticket=1Xej5zq%2FxWAXWghF%2Fw%2FfxgSs6WwzB69m7LbbUtBrzQGMPB45TX9dlmPfBUZINTA0&wx_header=1 
        var responseStr = responseDetail.response.body.toString();//转换变量为string
        if(/mp\/profile_ext\?action=urlcheck/i.test(requestDetail.url) || /mp\/profile_ext\?action=home/i.test(requestDetail.url) ){//当链接地址为公众号历史消息页面时(第二种页面形式)
        //  if(/mp\/profile_ext\?action=urlcheck/i.test(requestDetail.url)  ){//当链接地址为公众号历史消息页面时(第二种页面形式)
             try{


                console.log("action=home");
              var newResponse = Object.assign({}, responseDetail.response);
              try {
                 //  HttpPost(json.general_msg_list.toString(),requestDetail.url,"getMpBiz.php");
                  //循环采集历史页面
                  var request = require('urllib-sync').request;
                  var res = request(request_url + 'getMpBiz.php');
                  newResponse.body += res.data.toString();
                  console.log("*****************%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%*****");




                    //返回的body 
                  
                  var reg = /var msgList = \'(.*?)\';/;//定义历史消息正则匹配规则（和第一种页面形式的正则不同）
                  var ret = reg.exec(responseStr);
                  console.log("------getMsgJson---------");
                  if(ret){
                     HttpPost(ret[1],requestDetail.url,"getMsgJson.php");
                     //保存公众号名称和简介
                    var mpInfo = [];
                    var regName = /(.*)<\/strong>/gi;
                    var name = regName.exec(responseStr);
                  
                    var regDes = /(.*?)<\/p>/gi;
                    var des = regDes.exec(responseStr);
                  
                    var regIcon = /<img src="(.*)" id="icon">/gi;
                    var icon = regIcon.exec(responseStr);
                    mpInfo['name'] = name[1];
                    mpInfo['des'] = des[1];
                    mpInfo['icon'] = icon[1];
                    console.log(mpInfo);
                    //strMpInfo = JSON.stringify(mpInfo);
                    var jsonInfo = '{"name":"'+name[1]+'", "des":"'+des[1]+'", "icon":"'+ encodeURIComponent(icon[1])+ '"}';
                    console.log("------getMpInfo---------");
                    HttpPost(jsonInfo, requestDetail.url, "getMpInfo.php");
                  }
              
                 return {
                    response: newResponse
                  };  
                }catch(e){
                 return null;
              } 

                
            }catch(e){
              return null;
            }
            
         }else if(/mp\/profile_ext\?action=getmsg/i.test(requestDetail.url)){//第二种页面表现形式的向下翻页后的json
            console.log("action=getmsg");
            try {
                var json = JSON.parse(responseStr);
                if (json.general_msg_list != []) {
         
                    HttpPost(json.general_msg_list.toString(),requestDetail.url,"getMsgJson.php");//这个函数和上面的一样是后文定义的，将第二页历史消息的json发送到自己的服务器
                }
            }catch(e){
               return null;
            } 
            return null;
          }else if(/mp\/getappmsgext/i.test(requestDetail.url)){//当链接地址为公众号文章阅读量和点赞量时
            console.log("getappmsgext");
            try {  

                HttpPost(responseStr,requestDetail.url,"getMsgExt.php");//函数是后文定义的，功能是将文章阅读量点赞量的json发送到服务器
            
            }catch(e){
                return null;
            }
            return null;
         }else if(/mp\/appmsg_comment/i.test(requestDetail.url)){//当链接地址为公众号文章阅读量和点赞量时
            console.log("appmsg_comment");
            try {  

                HttpPost(responseStr,requestDetail.url,"getComment.php");//函数是后文定义的，功能是将文章阅读量点赞量的json发送到服务器
            
            }catch(e){
                return null;
            }
            return null;
         }
          // else if(/mp\/profile_ext\?action=home/i.test(requestDetail.url)){//第二种页面表现形式的向下翻页后的json
          //   console.log("action=home");
          //   var newResponse = Object.assign({}, responseDetail.response);
          //   try {
          //      //  HttpPost(json.general_msg_list.toString(),requestDetail.url,"getMpBiz.php");
          //       //循环采集历史页面
          //       var request = require('urllib-sync').request;
          //       var res = request(request_url + 'getMpBiz.php');
          //       newResponse.body += res.data.toString();
          //       console.log("*****************%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%*****");
          //       return {
          //         response: newResponse
          //       };  
          //   }catch(e){
          //      return null;
          //   } 
          //   return null;
          // } 
           else if(/s\?__biz/i.test(requestDetail.url) || /mp\/rumor/i.test(requestDetail.url)){//当链接地址为公众号文章时（rumor这个地址是公众号文章被辟谣了）
            console.log("getWxPost");
            var newResponse = Object.assign({}, responseDetail.response);
            try {
                //这里采用同步请求的方式，get请求完了之后就进入 callback()
                //nodejs 中的 http.get 方法是异步请求的，所以，http.get还没有请求完 就走到callback 方法，urllib-sync 同步请求的库 解决了
                var request = require('urllib-sync').request;
                var res = request(request_url + 'getWxPost.php');
                newResponse.body += res.data.toString();
                console.log("*****************%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%*****");
                return {
                  response: newResponse
                };  
  
            }catch(e){
                 //var newDataStr = serverResData.toString();
                 console.log(e);
                var newDataStr = "catch error in getWxPost";
                newResponse.body += newDataStr;
                return {
                  response: newResponse
                };
            }
            return null;
         } else {
          return null;
         }
  
  },


  /**
   * 
   * 
   * @param {any} requestDetail 
   * @returns 
   */
  *beforeDealHttpsRequest(requestDetail) {
    return false;
  },

  /**
   * 
   * 
   * @param {any} requestDetail 
   * @param {any} error 
   * @returns 
   */
  *onError(requestDetail, error) {
    return null;
  },


  /**
   * 
   * 
   * @param {any} requestDetail 
   * @param {any} error 
   * @returns 
   */
  *onConnectError(requestDetail, error) {
    return null;
  },
};


//将json发送到服务器，str为json内容，url为历史消息页面地址，path是接收程序的路径和文件名
    function HttpPost(str,url,path) {
    var http = require('http');
    var data = {
        // str: encodeURIComponent(str),
        // url: encodeURIComponent(url)
        str:str,
        url:url
    };
    var content = require('querystring').stringify(data);
  
    var options = {
        method: "POST",
        host: "127.0.0.1",//注意没有http://，这是服务器的域名。
        port: 8008,
        path: "/"+path,//接收程序的路径和文件名
         headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                "Content-Length": content.length
            }
    };
   // console.log(content);
    console.log("++++++++++++++++++");
    console.log(options);
    var req = http.request(options, function (res) {
        console.log('STATUS:' + res.statusCode);
        //res.setEncoding('utf8');
        res.on('data', function (chunk) {
            console.log('BODY: ' + chunk);
        });
    });
    req.on('error', function (e) {
        console.log('problem with request: ' + e.message);
    });
      //加入post数据
    req.write(content);

    req.end();
}
