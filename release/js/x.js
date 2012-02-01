(function($){
$.url=function(url){
		url=url||window.location.href;
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

$.hash=function(){
    if (arguments.length===0){
        return $.url().hashParams||{};
    }else if(arguments.length===1){
        var hashs=$.url().hashParams||{};
        return hashs[arguments[0]];
    }else if (arguments.length==2){
    var name=arguments[0];
    var value=arguments[1];
    var url=window.location.href;

    var reBeReplaced=new RegExp("\#"+name+"=([^\#]*)","ig");
    var reRes=reBeReplaced.exec(url);
    if (reRes && reRes.length>1){
        var beReplaced=reRes[0];
        var replace=beReplaced.replace(reRes[1],value);
        window.location.href=url.replace(beReplaced,replace);
    }else{
        window.location.href=url+"#"+name+"="+value;
    }
   
    }
}
})(jQuery);
