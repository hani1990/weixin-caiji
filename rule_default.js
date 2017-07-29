var utils      = require("./util"),
    bodyParser = require("body-parser"),
    path       = require("path"),
    fs         = require("fs"),
    Promise    = require("promise");

var isRootCAFileExists = require("./certMgr.js").isRootCAFileExists(),
    interceptFlag      = true;

//e.g. [ { keyword: 'aaa', local: '/Users/Stella/061739.pdf' } ]
var mapConfig = [],
    configFile = "mapConfig.json";
function saveMapConfig(content,cb){
    new Promise(function(resolve,reject){
        var anyproxyHome = utils.getAnyProxyHome(),
            mapCfgPath   = path.join(anyproxyHome,configFile);

        if(typeof content == "object"){
            content = JSON.stringify(content);
        }
        resolve({
            path    :mapCfgPath,
            content :content
        });
    })
    .then(function(config){
        return new Promise(function(resolve,reject){
            fs.writeFile(config.path, config.content, function(e){
                if(e){
                    reject(e);
                }else{
                    resolve();
                }
            });
        });
    })
    .catch(function(e){
        cb && cb(e);
    })
    .done(function(){
        cb && cb();
    });
}
function getMapConfig(cb){
    var read = Promise.denodeify(fs.readFile);

    new Promise(function(resolve,reject){
        var anyproxyHome = utils.getAnyProxyHome(),
            mapCfgPath   = path.join(anyproxyHome,configFile);

        resolve(mapCfgPath);
    })
    .then(read)
    .then(function(content){
        return JSON.parse(content);
    })
    .catch(function(e){
        cb && cb(e);
    })
    .done(function(obj){
        cb && cb(null,obj);
    });
}

setTimeout(function(){
    //load saved config file
    getMapConfig(function(err,result){
        if(result){
            mapConfig = result;
        }
    });
},1000);


module.exports = {
    token: Date.now(),
    summary:function(){
        var tip = "the default rule for AnyProxy.";
        if(!isRootCAFileExists){
            tip += "\nRoot CA does not exist, will not intercept any https requests.";
        }
        return tip;
    },

    shouldUseLocalResponse : function(req,reqBody){
        //intercept all options request
        var simpleUrl = (req.headers.host || "") + (req.url || "");
        mapConfig.map(function(item){
            var key = item.keyword;
            if(simpleUrl.indexOf(key) >= 0){
                req.anyproxy_map_local = item.local;
                return false;
            }
        });


        return !!req.anyproxy_map_local;
    },

    dealLocalResponse : function(req,reqBody,callback){
        if(req.anyproxy_map_local){
            fs.readFile(req.anyproxy_map_local,function(err,buffer){
                if(err){
                    callback(200, {}, "[AnyProxy failed to load local file] " + err);
                }else{
                    var header = {
                        'Content-Type': utils.contentType(req.anyproxy_map_local)
                    };
                    callback(200, header, buffer);
                }
            });
        }
    },

    replaceRequestProtocol:function(req,protocol){
    },

    replaceRequestOption : function(req,option){
        var newOption = option;
        if(/google/i.test(newOption.headers.host)){
            newOption.hostname = "www.baidu.com";
            newOption.port     = "80";
        }
        return newOption;
    },

    replaceRequestData: function(req,data){
    },

    replaceResponseStatusCode: function(req,res,statusCode){
    },

    replaceResponseHeader: function(req,res,header){
        // return {
        //   response: {
        //     statusCode: 200,
        //     header: { 'content-type': 'text/html' },
        //     body: 'this could be a <string> or <buffer>'
        //   }
        // };
    },

    // Deprecated
    // replaceServerResData: function(req,res,serverResData){
    //     return serverResData;
    // },

    replaceServerResDataAsync: function(req,res,serverResData,callback){
        var request_url = "http://192.168.10.228:8999/";

         if(/mp\/getmasssendmsg/i.test(req.url)){//当链接地址为公众号历史消息页面时(第一种页面形式)
           
            if(serverResData.toString() !== ""){
                try {//防止报错退出程序
                    console.log("getmasssendmsg");
                    var reg = /msgList = (.*?);\r\n/;//定义历史消息正则匹配规则
                    var ret = reg.exec(serverResData.toString());//转换变量为string
                    HttpPost(ret[1], req.url, "getMsgJson.php");//这个函数是后文定义的，将匹配到的历史消息json发送到自己的服务器
                    var http = require('http');
                    http.get( request_url + 'getWxHis.php', function(res) {//这个地址是自己服务器上的一个程序，目的是为了获取到下一个链接地址，将地址放在一个js脚本中，将页面自动跳转到下一页。后文将介绍getWxHis.php的原理。
                        res.on('data', function(chunk){
                            callback(chunk+serverResData);//将返回的代码插入到历史消息页面中，并返回显示出来
                        })
                    });
                }catch(e){//如果上面的正则没有匹配到，那么这个页面内容可能是公众号历史消息页面向下翻动的第二页，因为历史消息第一页是html格式的，第二页就是json格式的。
                    try {
                        var json = JSON.parse(serverResData.toString());
                        if (json.general_msg_list != []) {
                            HttpPost(json.general_msg_list,req.url,"getMsgJson.php");//这个函数和上面的一样是后文定义的，将第二页历史消息的json发送到自己的服务器
                        }
                    }catch(e){
                        console.log(e);//错误捕捉
                    }
                    callback(serverResData);//直接返回第二页json内容
                }
            }
         }else if(/mp\/profile_ext\?action=urlcheck/i.test(req.url) || /mp\/profile_ext\?action=home/i.test(req.url) ){//当链接地址为公众号历史消息页面时(第二种页面形式)
            console.log("action=urlcheck");
            try {
                var reg = /var msgList = \'(.*?)\';/;//定义历史消息正则匹配规则（和第一种页面形式的正则不同）
                var ret = reg.exec(serverResData.toString());//转换变量为string
                console.log("------dddddddd------------------");
                console.log(ret);
                HttpPost(ret[1],req.url,"getMsgJson.php");//这个函数是后文定义的，将匹配到的历史消息json发送到自己的服务器
                // var http = require('http');
                // http.get( request_url + 'getWxHis.php', function(res) {//这个地址是自己服务器上的一个程序，目的是为了获取到下一个链接地址，将地址放在一个js脚本中，将页面自动跳转到下一页。后文将介绍getWxHis.php的原理。
                //     res.on('data', function(chunk){
                //         callback(chunk+serverResData);//将返回的代码插入到历史消息页面中，并返回显示出来
                //     })
                // });
                    var request = require('urllib-sync').request;
                    var msgurl = request_url+'getMsgList.php?url='+encodeURIComponent(req.url)
                    //console.log(msgurl);
                    var res = request( msgurl );
                    console.log(res.data.toString());
                    callback(serverResData+res.data.toString());
            }catch(e){
                callback(serverResData);
            }
         }else if(/mp\/profile_ext\?action=getmsg/i.test(req.url)){//第二种页面表现形式的向下翻页后的json
            console.log("action=getmsg");
            try {
                var json = JSON.parse(serverResData.toString());
                if (json.general_msg_list != []) {
                   // console.log(json.general_msg_list);
                    console.log("++++++++++++++");
                    console.log(req.url);
                    HttpPost(json.general_msg_list.toString(),req.url,"getMsgJson.php");//这个函数和上面的一样是后文定义的，将第二页历史消息的json发送到自己的服务器
                    // console.log("get_msg_list");
                    // var request = require('urllib-sync').request;
                    // var msgurl = request_url+'getMsgList.php?url='+encodeURIComponent(req.url)
                    // //console.log(msgurl);
                    // var res = request( msgurl );
                    // console.log(res.data.toString());
                    // callback(serverResData+res.data.toString());
                }
            }catch(e){
                console.log('catch getmsg');
                console.log(e);
            }
            callback(serverResData);
         }else if(/mp\/getappmsgext/i.test(req.url)){//当链接地址为公众号文章阅读量和点赞量时
            console.log("getappmsgext");
            try {
                console.log(serverResData.toString());
                console.log(req.url);
                HttpPost(serverResData.toString(),req.url,"getMsgExt.php");//函数是后文定义的，功能是将文章阅读量点赞量的json发送到服务器
            }catch(e){
         
            }
            callback(serverResData);
         }else if(/s\?__biz/i.test(req.url) || /mp\/rumor/i.test(req.url)){//当链接地址为公众号文章时（rumor这个地址是公众号文章被辟谣了）
            console.log("getWxPost");
            try {
                //这里采用同步请求的方式，get请求完了之后就进入 callback()
                //nodejs 中的 http.get 方法是异步请求的，所以，http.get还没有请求完 就走到callback 方法，urllib-sync 同步请求的库 解决了
                var request = require('urllib-sync').request;
                var res = request(request_url + 'getWxPost.php');
                console.log(res.data.toString());
                callback(serverResData+res.data.toString());

            }catch(e){
                 //var newDataStr = serverResData.toString();
                var newDataStr = "catch error in getWxPost";
                callback(newDataStr);
            }
             serverResData +=  "hell weixin";
             callback(serverResData);

         }else{
            
            callback(serverResData);
         }
        
        callback(serverResData);
    },

    pauseBeforeSendingResponse: function(req,res){
    },

    shouldInterceptHttpsReq:function(req){
        //return true;
        return interceptFlag;
    },

    //  replaceServerResDataAsync: function(req,res,serverResData,callback){
    //     //add "hello github" to all github pages
    //     if(req.headers.host == "github.com"){
    //         serverResData += "hello github";
    //     }
    //     callback(serverResData);
    // },

    // shouldInterceptHttpsReq :function(req){
    //     //intercept https://github.com/
    //     //otherwise, all the https traffic will not go through this proxy

    //     // return true;
    //     if(req.headers.host == "github.com"){
    //         return true;
    //     }else{
    //         return false;
    //     }
    // },

    //[beta]
    //fetch entire traffic data
    fetchTrafficData: function(id,info){},

    setInterceptFlag: function(flag){
        interceptFlag = flag && isRootCAFileExists;
    },

    _plugIntoWebinterface: function(app,cb){

        app.get("/filetree",function(req,res){
            try{
                var root = req.query.root || utils.getUserHome() || "/";
                utils.filewalker(root,function(err, info){
                    res.json(info);
                });
            }catch(e){
                res.end(e);
            }
        });

        app.use(bodyParser.json());
        app.get("/getMapConfig",function(req,res){
            res.json(mapConfig);
        });
        app.post("/setMapConfig",function(req,res){
            mapConfig = req.body;
            res.json(mapConfig);

            saveMapConfig(mapConfig);
        });

        cb();
    },

    _getCustomMenu : function(){
        return [
            // {
            //     name:"test",
            //     icon:"uk-icon-lemon-o",
            //     url :"http://anyproxy.io"
            // }
        ];
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
        host: "192.168.10.228",//注意没有http://，这是服务器的域名。
        port: 8999,
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
