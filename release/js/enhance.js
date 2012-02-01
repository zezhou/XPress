/**
 * @description This is a base js to inhance the js framework
 * <p>Usage: include this file</p>
 * @version 20081008
 */
var loadScript=function(url){
    document.write('<scr' + 'ipt type="text/javascript" src="' + url + '"><\/scr' + 'ipt>');
    };
var loadCss=function(url){
	var oCss=document.createElement("link");
	oCss.setAttribute("rel","stylesheet");
	oCss.setAttribute("type","text/css");
	oCss.setAttribute("href",url);
	document.getElementsByTagName("head")[0].appendChild(oCss);
	delete(oCss);
}
var in_array=function(needle,haystack){
	  if(needle instanceof Array)
	    for(var i=needle.length-1;i>=0;i--) 
	    	if(in_array(needle[i],haystack)) return true;

	  for(var i=haystack.length-1;i>=0;i--) if(haystack[i]===needle) return true;
	    return false;
	}
