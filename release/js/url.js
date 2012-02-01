/**
 * @description handle url
 * <p>Usage: var aUrl=new X.URL();</p>
 * @version 20090301
 */
if (!X) var X={};//申明命名空间
/**
 * 初始化即处理当前url
 * @param sURL	要处理的URL
 * @return	source	当前url
 * 			protocol	协议
 * 			host	主机名
 * 			port	端口
 * 			query	请求字符串
 * 			params	发送请求的参数和参数值
 * 			file	请求的文件
 * 			hash	请求的锚点或js用#号形式传递参数的字符串
 * 			hashParams	以#好形式传递的参数和值
 * 			path	请求的服务器路径
 * 			relative	相对路径
 * 			segments	请求的目录和文件树
 */
X.URL=function(sURL){
    if(!sURL) sURL=location.href;
    return this.parseURL(sURL);
};

X.URL.prototype={
    /**
     * 处理URL
     * @param url 要处理的url
     * @return	source	当前url
     * 			protocol	协议
     * 			host	主机名
     * 			port	端口
     * 			query	请求字符串
     * 			params	发送请求的参数和参数值
     * 			file	请求的文件
     * 			hash	请求的锚点或js用#号形式传递参数的字符串
     * 			hashParams	以#好形式传递的参数和值
     * 			path	请求的服务器路径
     * 			relative	相对路径
     * 			segments	请求的目录和文件树
     */
    parseURL:function(url) {
                 var a =  document.createElement('a');
                 a.href = url;
                 return {
                     source: url,
                         protocol: a.protocol.replace(':',''),
                         host: a.hostname,
                         port: a.port,
                         query: a.search,
                         params: (function(){
                             var ret = {},
                         seg = a.search.replace(/^\?/,'').split('&'),
                         len = seg.length, i = 0, s;
                         for (;i<len;i++) {
                             if (!seg[i]) { continue; }
                             s = seg[i].split('=');
                             ret[s[0]] = s[1];
                         }
                         return ret;
                         })(),
                         file: (a.pathname.match(/\/([^\/?#]+)$/i) || [,''])[1],
                         hash: a.hash.replace('#',''),
                         hashParams:(function(){
                             //http://localhost/xublog/admin/login.php#action=manage_table
                             var ret = {},
                         seg = a.hash.split('#'),
                         len = seg.length, i = 0, s;
                         for (;i<len;i++) {
                             if (!seg[i]) { continue; }
                             s = seg[i].split('=');
                             ret[s[0]] = s[1];
                         }
                         return ret;

                         })(),

                         path: a.pathname.replace(/^([^\/])/,'/$1'),
                         relative: (a.href.match(/tp:\/\/[^\/]+(.+)/) || [,''])[1],
                         segments: a.pathname.replace(/^\//,'').split('/')
                 };
             }
};
/*
   parseURL: (function() {
   var keys = ['source', 
   'prePath', 
   'scheme', 
   'username', 
   'password', 
   'host', 
   'port', 
   'path', 
   'dir', 
   'file',
   'query', 
   'fragment'];
   var re = /^((?:([^:\/?#.]+):)?(?:\/\/)?(?:([^:@]*):?([^:@]*)?@)?([^:\/?#]*)(?::(\d*))?) \
   ((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*)) \
   (?:\?([^#]*))?(?:#(.*))?/;    
   return function(sourceUri) {
   var uri = {};
   var uriParts = re.exec(sourceUri);
   for(var i = 0; i < uriParts.length; ++i) {
   uri[keys[i]] = (uriParts[i] ? uriParts[i] : '');
   }
   return uri;
   }
   })();*/

/*
   escapeHTML: function(str) {
   var div  = document.createElement('div');
   var text = document.createTextNode(str);
   div.appendChild(text);
   return div.innerHTML;
   }

   unescapeHTML: function(str) {
   var div       = document.createElement('div');
   div.innerHTML = str.replace(/<\/?[^>]+>/gi, '');
   return div.childNodes[0] ? div.childNodes[0].nodeValue : '';
   }
   */
