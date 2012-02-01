/**
 * This is a simple web editor which has WYSWYG  , source and markdown mode to surport edit feature in XPress blog.
 * Usage:
 * var editor=new X.Editor();
 * editor.create('mainPanel','test');
 * Feature:
 *     - Alignment (left, center, right)
 *     - Font formatting (bold, underline, italic, font size, font name, font color)
 *     - Word counts, remove formatting, insert lines, undo, redo, and insert emoticons
 * @author xyz
 * @since 20081008
 * @version 12.01
 */

if (!X) var X = {};

/* 兼容浏览器的一些方法 */

var Handler={
    /**
     * the add event function is diffrent between IE and FF.the function bellow is compat the diffrent.
     * 
     * @param {Object} obj 
     * @param {Object} eventType like click mouseover etc
     * @param {Object} func the function which you want to attach it to the DOM
     * @param {Object} capture 
     */
    "addEvent": function(obj, eventType, func, capture){
        if (document.addEventListener) {
            obj.addEventListener(eventType, func, capture);
            return true;
        }else {
            var r = obj.attachEvent("on" + eventType, func);
            return r;
        }
    },
    "removeEvent":function(obj, eventType, func, capture){
        if (obj.removeEventListener) {
            obj.removeEventListener(eventType, func, capture);
            return true;
        }else {
            var r = obj.detachEvent("on" + eventType, func);
            return r;
        }
    },
    "addClass" : function(obj,className){
        if (obj && obj.className && !obj.className.match(new RegExp('(\\s|^)'+className+'(\\s|$)'))){
            obj.className+=" "+className;
        }
    },
    "removeClass" : function(obj,className){
        obj.className=obj.className.replace(className,'');
    },
    "show":function(obj){
        obj.style.display="";
    },
    "hide":function(obj){
        obj.style.display="none";
    },
    /*
     Mozilla supports the W3C standard of accessing iframe's document object through
     IFrameElmRef.contentDocument, while Internet Explorer requires you to access it through document.frames
     ["IframeName"] and then access the resulting docusment
     */
    "getIFrameDocument": function(sID){
        if (document.getElementById(sID) && document.getElementById(sID).contentDocument) {  // if contentDocument exists, W3C compliant (Mozilla)
            return document.getElementById(sID).contentDocument;
        }else if(document.frames && document.frames[sID]) {     // IE
            return document.frames[sID].document;
        }else{
            return null;
        }
    }
};

/* 编辑，源代码，Markdown等编辑方式的切换*/

var Tab=function(conf){
    this.conf=conf;
    this.selectedClassName="selected";
    if (conf.renderTo){
        this.render(conf.renderTo);
    }
}

Tab.prototype.render = function(element){
    var html = '<div class="editor_nav_inner"><div class="editor_nav_tab" id="x_editor_source">源代码</div><div class="editor_nav_tab selected" id="x_editor_edit">编辑</div><div class="editor_nav_tab" id="x_editor_markdown"><span id="markdown_text">Markdown</span></div><div id="x_editor_preview" class="editor_nav_tab">预览</div></div><div style="clear:both"></div>';
    if(typeof(element)=="string"){
        element = document.getElementById(element);
    } 
    element.innerHTML = html;
    this.bindEvent();
    return html;
}

Tab.prototype.unselect=function(){
    for (var domId in this.conf.tab){
        var dom=document.getElementById(domId);
        Handler.removeClass(dom,this.selectedClassName);
    }
}

Tab.prototype.select=function(domId){
    this.unselect();
    var dom=document.getElementById(domId);
    Handler.addClass(dom,this.selectedClassName);
}

Tab.prototype.bindEvent=function(){
    var me =this;
    for (var domId in this.conf.tab){
        var dom=document.getElementById(domId);
        (function(me,domId,dom){ // let domIt instant eq
            Handler.addEvent(dom,"click",function(e){
                me.conf.tab[domId].call(me,e,domId);
            },false);
        })(me,domId,dom);
    }
}

/* 工具栏 */

var Toolbar=function(conf){
    this.conf= conf || {};
    if (this.conf.renderTo){
        this.render(this.conf.renderTo);
    }
};

Toolbar.prototype.render = function(element){
    var sHtml = '<div class="editor_toolbar_block"><div class="editor_toolbar_content">';
    var aToolbarSets = this.conf.toolbarSets[this.conf.toolbarSet];
    for (nNum in aToolbarSets) {
        switch (aToolbarSets[nNum]) {
            case "bold":
                sHtml += '<div title="加粗" class="Editor_TB_Button" id="editor_bold"></div>';
            break;
            case "italic":
                sHtml += '<div title="斜体" class="Editor_TB_Button" id="editor_italic"></div>';
            break;
            case "underline":
                sHtml += '<div title="下划线" class="Editor_TB_Button" id="editor_underline"></div>';
            break;
        }
    }
    sHtml +='</div><div class="editor_toolbar_footer">字体</div></div><div style="clear:both"></div>';
    if (typeof(element) == "string"){
        element=document.getElementById(element);
    }
    element.innerHTML=sHtml;
    this._loadEvent();
    return sHtml;
}

Toolbar.prototype.show=function(){
    Handler.show(document.getElementById("editor_toolbar"));
    this._loadEvent();
}

Toolbar.prototype.hide=function(){
    Handler.hide(document.getElementById("editor_toolbar"));
}

Toolbar.prototype.bold = function(handle){
    Handler.getIFrameDocument(handle.instanceName+"___Frame").execCommand('bold',false,null); //识别选中的区域 插入HTML代码
}

Toolbar.prototype.italic = function(handle){
    Handler.getIFrameDocument(handle.instanceName+"___Frame").execCommand('italic',false,null);
}

Toolbar.prototype.underline = function(handle){
    Handle.getIFrameDocument(handle.instanceName+"___Frame").execCommand('underline',false,null);
}

Toolbar.prototype._loadEvent=function(){
    var aToolbarSets = this.conf.toolbarSets[this.conf.toolbarSet];
    for (nNum in aToolbarSets) {
        switch (aToolbarSets[nNum]) {
            /*bold, underline, italic, font size, font name, font color*/
            case "bold":
                var d = document.getElementById("editor_bold");
            var t = this;//会形成闭包？
            d.onclick = function(){
                this.bold(t)
            }					
            delete d, t;
            break;
            case "italic":
                var d = document.getElementById("editor_italic");
            var t = this;//会形成闭包？
            d.onclick = function(){
                this.italic(t);
            }					
            delete d, t;
            break;
            case "underline":
                var d = document.getElementById("editor_underline");
            var t = this;//会形成闭包？
            d.onclick = function(){
                this.underline(t)
            }				
            delete d, t;
            break;
        }
    }
}


/**
 * @class Editor
 * @constructor 
 */

X.Editor = function(options){
    if (!options) var options = {};
    this.conf = options;
    this.style = "classic";
    this.height = "100%";
    this.width = "100%";
    this.value = null;
    this.editorPath = options.editorPath||"./";
    this.editorCssPath = this.editorPath+"css/";
    this.enterMode = "p"; //"p"|"div"|"br"
    this.area = "";
    this.body = "";
    this.mode = "";
    /*
     * bold, underline, italic, font size, font name, font color+
     * Word counts, remove formatting, insert lines, undo, redo, and insert emoticons
     * 代码 引用 插入图片 插入flash 
     */
    this.toolbarSet = "default";
    //this.toolbarSets['default'] = ['|','字体','bold','italic','underline','fontsize','|','样式','link','removelink','code','quote','image','flash','|','编辑','do','undo','redo','paste','removeformatting','wordcounts'];
    this.toolbarSets = {'default':['bold','italic','underline']};
    if (options.domId && options.instanceName){
        this.create(options.domId,options.instanceName);
    }
};


/*
 * Functions for Editor
 */
var xep=X.Editor.prototype;

/**
 *  set or get value
 */

xep.val=function(value){
    if(value){
        this.value = value;
        if(this.mode=="markdown"){
            document.getElementById("editor_markdown_content").value=this.value;
        }else if (this.mode=="source"){
            this.area.body.innerHTML = this._HTMLEncode(this.value);
        }else{
            this.area.body.innerHTML = this.value;
        }
    }else{
        if (this.mode == "markdown"){
            this.value=document.getElementById("editor_markdown_content").value;
        }else if (this.mode=="source"){
            this.value = this._HTMLDecode(this.area.childNodes[0].innerHTML);
        }else{
            this.value=this.area.body.innerHTML;
        }
    }
    return this.value;
}

/*
 * create the editor,parameter:domId (the ID of DOM) instanceName  (the name what you want post-data have)
 */

xep.create=function(domId,instanceName){
    var me = this;
    this.instanceName = instanceName;
    this.domId = domId;
    this.createHtml();
    this.toolbar=new Toolbar({"renderTo":"editor_toolbar","toolbarSets":this.toolbarSets,"toolbarSet":this.toolbarSet});
    this.tab=new Tab({
        "renderTo":"editor_nav",
        "tab":{
        "x_editor_preview":function(e){me.conf.preview.call(me,e)},
        "x_editor_edit":function(e,domId){me.editMode.call(me,e,domId)},
        "x_editor_markdown":function(e,domId){me.markdownMode.call(me,e,domId)},
        "x_editor_source":function(e,domId){me.sourceMode.call(me,e,domId);}
        }
    });
    this.editMode(null,"x_editor_edit",this.tab);
};

xep.createHtml= function(){ //create iframe and html
    var sHtml = '';
    sHtml+='<div id="editor">\
           <div id="editor_nav"></div>\
           <div id="editor_toolbar"></div>\
           <div id="editor_area"></div>\
           <input type="hidden" id="' + this.instanceName + '" name="' + this.instanceName + '" value="" style="display:none" >\
           </div>';

    var dom = document.getElementById(this.domId);
    this._loadCSS();
    dom.innerHTML = sHtml; //get the editor's iframe and input html code
    return sHtml;
}

xep._createIFrameHtml= function(){ //create iframe	
    var sHtml = '';
    sHtml = '<iframe id="' + this.instanceName + '___Frame" name="' + this.instanceName + '___Frame" frameborder="0" width="' + this.width + '" height="' + this.height + '"></iframe>';

    return sHtml;
}

xep._HTMLEncode= function(text){
    if (typeof(text) != "string") 
        text = text.toString();
    text = text.replace(/&/g, "&amp;").replace(/"/g, "&quot;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    return text;
}

xep._HTMLDecode= function(text){
    if (typeof(text) != "string") 
        text = text.toString();
    text = text.replace(/&amp;/g, "&").replace(/&quot;/g, '"').replace(/&lt;/g, "<").replace(/&gt;/g, ">");
    return text;
}

xep._loadScript= function(url){
    document.write('<scr' + 'ipt type="text/javascript" src="' + url + '"><\/scr' + 'ipt>');
}

xep._loadCSS= function(){
    var oCss = document.createElement("link");
    oCss.setAttribute("rel", "stylesheet");
    oCss.setAttribute("type", "text/css");
    oCss.setAttribute("href", this.editorCssPath + "editor.css");
    document.getElementsByTagName("head")[0].appendChild(oCss);
    delete (oCss);
}

xep._loadEvent= function(){
    var me=this;
    Handler.addEvent(this.area, "keypress",function(e){me.handleKeyPress.call(me,e)}, false);
    
    //Handler.addEvent(this.area,"blur", function(e){ //if the mouse moveout the editor area ,then let the editor value equal the input value 
        //this.parent.document.getElementById(me.instanceName).value = this.value=this.document.body.innerHTML;
    //}, false);
}


xep.handleKeyPress=function(event){
    var e=event||window.event;	
    var code=e.charCode||e.keyCode;

    switch (code){
        case 13:
            if(!e.preventDefault()){
                e.returnValue=false;
            }
            var oSelect=this.area.getSelection();
            var oRange = oSelect.getRangeAt(0);
            if(!oRange.startContainer){
                return false;
            }
            var oStartBlock=document.createElement(this.enterMode);
            oStartBlock.innerHTML=oRange.startContainer.data.substring(0,oRange.startOffset);
            var oEndBlock=document.createElement(this.enterMode);
            oEndBlock.innerHTML=oRange.startContainer.data.substring(oRange.startOffset);
            if(oEndBlock.innerHTML==""){
                oEndBlock.innerHTML="<br _fixbug=''>";
            }
            if(oStartBlock.innerHTML==""){
                oStartBlock.innerHTML="<br _fixbug=''>";
            } 

            var oNewBlock=document.createDocumentFragment();
            oNewBlock.appendChild(oStartBlock);
            oNewBlock.appendChild(oEndBlock);
            //var oReplaceBlock=oRange.startContainer.parentNode;
            var oReplaceBlock=oRange.startContainer;
            
            oReplaceBlock.parentNode.replaceChild(oNewBlock,oReplaceBlock);
            var position=oRange.startOffset;//插入内容时光标所处的节点的子节点下标
            oTmpNode=oRange.commonAncestorContainer; 
            oSelect.collapse(oTmpNode.childNodes[position+1],0);//新插入一行后，按照期望，光标应该是在新插入行的行首，但在firefox下是在原来第一行的行首。通过这段代码来修复这个问题
            //      editor.moveToNodeContents(oTmpNode.childNodes[1]);
            //move mouse to the new position
            break;

        default:
            return;
    }
    return;
}

xep.moveToNodeContents=function(targetNode){
    var range = this.area.createRange();
    var referenceNode = targetNode;
    range.selectNodeContents(referenceNode);
    range.setStart(referenceNode,1);
    range.setEnd(referenceNode,1);
    range.collapse(true);
}


/** 各不同编辑模式的插件 */
xep.markdownMode=function(e,domId){
    this.tab.select(domId);
    this.toolbar.hide();
    var content = this.body.textContent; 
    this.value=this.val();
    document.getElementById("editor_area").innerHTML="<textarea id='editor_markdown_content'>"+content+"</textarea>";
    this.mode = "markdown";
    this.val(this.value);
}

xep.sourceMode = function(e,domId){
    if(this.mode !="html"){
        this.editMode(e.domId);
    }else{
        this.tab.select(domId);
    }
    this.toolbar.hide();
    this.value=this.val()
    this.mode = "source";
    this.val(this.value);
}

xep.editMode = function(e,domId){
    if(this.mode == "html"){
        return true;
    }else if(this.mode){
        this.value=this.val();
    }
    this.area = Handler.getIFrameDocument(this.instanceName + "___Frame");//contentWindow is get the iframe
    if(!this.area){
        document.getElementById("editor_area").innerHTML='<div id="editor_iframe">' + this._createIFrameHtml() + '</div>';
        this.area = Handler.getIFrameDocument(this.instanceName + "___Frame");//contentWindow is get the iframe
    }
    this.body=this.area.body;

    if(!this.area.contentEditable){
        //IE
        this.area.designMode = "On";
        this.area.contentEditable = true;

        //firefox
        this.area.open();
        this.area.writeln('<html dir="ltr"><style>body{background:#FFF;}body, td{font-family: Arial, Verdana, sans-serif;font-size: 1em;}</style><body></body></html>');
        this.area.close();
        //以下代码用于在firefox当中插入<p>标签来换行，<br>标签用来修复插入光标在<p>标签外的bug
        if(!this.body.firstChild){
            var oNode=document.createElement(this.enterMode);
            oNode.innerHTML="<br _fixbug=''>";
            this.body.appendChild(oNode);
        }
    }

    this.mode="html";
    this.val(this.value)
    this.tab.select(domId);
    this.toolbar.show();
    this._loadEvent();
}

