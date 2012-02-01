<?php 
/**
 * XPress安装程序
 * 完成如下内容:
 *     - 创建XPress数据库文件
 *     - 创建用户数据文件
 *     - 创建系统配置文件
 * @Since 12.1
 */

define("INCLUDE_CORE",True);
require_once("admin/lib/db.php");

/**
 * 创建XPress数据库文件
 */

function createDb($data){
    require_once("config.php");
    if (!file_exists(DATA_PATH)){
        try{
            mkdir(DATA_PATH);
        }catch(Exception $e){
            if(chmod(ADMIN_PATH,777)){
                mkdir(DATA_PATH);
            }else{
                return False;
            }
        }
    }
    if (file_exists(DATABASE_NAME)){
        $time=time();
        $bakDBName=DATABASE_NAME.$time;
        rename(DATABASE_NAME,$bakDBName);
    }
    //create database 
    $db=new SQLiteDatabase(DATABASE_NAME);
    //fields include id,title,content,category,tags,date,author
    $sql=" CREATE TABLE ".DATABASE_ARTICLES_NAME." (id INTEGER PRIMARY KEY NOT NULL ,title TEXT NOT NULL ,content TEXT NOT NULL ,tags TEXT,time TEXT ,author TEXT,url TEXT);";
    if($bCreateTable=$db->query($sql)){
        return True;
    }else{
        return False;
    }
}

/**
 *  创建用户数据文件
 */

function createUser($data){
    require_once("admin/lib/user.php");
    $salt=md5(time())."salt";
    $user=new User(array("salt"=>$salt));
    $passport=$user->getPwd($data['password']);
    $userContent=<<<TEXT
<?php
if (!defined("INCLUDE_CORE")){die("Permission denied.");}
\$user=array();
\$salt="${salt}";
//[admin_info]
\$user[0]['username']='${data['username']}';
\$user[0]['nickname']='${data['nickname']}';
\$user[0]['password']='${passport}';
//[admin_info end]
TEXT;
    if(!isset($data['data_path'])){
        return False;
    }
    $fh=fopen(DATA_PATH."config.php","w");
    fwrite($fh,$userContent);
    fclose($fh);
    return True;
}

/**
 * 创建系统配置文件
 */

function createConfig($data){
    
    $configContent=<<<TEXT
<?php
/**
 * The core config file of the system.It include many constant variable which define the system variable.
 */
////////////////////////[SYS INFO]/////////////////////////////////

/**  @constant INCLUDE_CORE it used to checking if include this core file.  */
if (!defined("INCLUDE_CORE")){
    define("INCLUDE_CORE",true);
}
/** @constant ABS_ROOT_PATH	系统的绝对路径 */
define("ABS_ROOT_PATH",dirname(__FILE__)."/");

///////////////////////[ADMIN OPTION]///////////////////////////////
// 为了系统安全最好修改以下配置

/** @constant ADMIN_PATH 后台管理系统的路径 */
define("ADMIN_PATH",ABS_ROOT_PATH."admin/");  //将默认admin修改为一个不容易猜到的名字，如"guanli"，并将系统的admin目录名也修改为"guanli"

/** @constant DATA_PATH	数据存放的路径 */
define("DATA_PATH",${data['data_path']});  //将默认data目录修改为一个不容易猜到的名字(该路径最好在Apache目录之外),如"/data/xpress"，并且将系统默认的"admin/data"目录也修改为该路径。

/** @constant	DATABASE_NAME	数据库名 */
define("DATABASE_NAME",DATA_PATH."${data['database_name']}"); //将默认SQLite数据文件名Xpress修改为一个不容易猜到的名字，并将系统的DATA_PATH."Xpress"也修改为该名字。

///////////////////////[SITE INFO]///////////////////////////////////

/** @constant SITE_NAME 站点名 */
define("SITE_NAME","${data['site_name']}");

/** @constant SITE_NAME 站点描述 */
define("SITE_DESCRIPTION","${data['site_description']}");

/** @constant SITE_DOMAIN 站点域名 */
define("SITE_DOMAIN","${data['site_domain']}");

///////////////////////[SYS INFO]///////////////////////////////////

define("XPRESS_VERSION","v12.02");

/** @constant	DATABASE_ARTICLES_NAME	储存文章的数据库名 */
define("DATABASE_ARTICLES_NAME","xp_articles");

/**
 * @constant	SITE_RELATIVE_DIR	站点文章存放文件夹与首页的相对路径
 */
define("ARTICLE_RELATIVE_DIR","../../");

/**
 * @constant	SITE_RELATIVE_DIR	站点首页相对域名的绝对路径
 */
define("INDEX_RELATIVE_DIR","");

/** @constant	TEMPLATE_PATH	系统模板路径，非后台路径。后台模板路径在后台的config文件里设置 */
define("TEMPLATE_PATH",ABS_ROOT_PATH."themes/");

/**
 * @constant  SITE_STYLE	站点样式
 **/
define("SITE_STYLE","classic");

/**
 * 首页的文件名
 */
define("INDEX_PAGE_NAME","index.html");
/**
 * 首页的路径
 */
define("INDEX_PAGE_PATH",ABS_ROOT_PATH.INDEX_PAGE_NAME);
/**
 * 首页模板路径
 */
define("INDEX_PAGE_TPL",TEMPLATE_PATH.SITE_STYLE."/index.html");

/**
 * 文章页模板存放路径
 */
define("ARTICLE_PAGE_TPL",TEMPLATE_PATH.SITE_STYLE."/article.html");

/**
 * @constant TPL_LEFT_SYMBOL	模板变量起始符号
 */
define("TPL_LEFT_SYMBOL","<{\\\$");
define("TPL_RIGHT_SYMBOL","/}>");
define("INDEX_ARTICLE_NUMBER",5);
define("RELATIVE_ARTICLE_NUM",5);

/**
 * @constant TPL_LEFT_SYMBOL	模板变量起始符号
 */
define("ARTICLE_SUFFIX",".html");

/**  @constant	DEBUG	去臭虫模式 */
define("DEBUG",false);

/** 各模块路径 */
define("MODULE_PATH",ADMIN_PATH."app/");
define("ADMIN_LIB_PATH",ADMIN_PATH."lib/");

/** 用户信息存放文件 */
define("USER_DATA_PATH",DATA_PATH."config.php");

/** 存放文章路径  */
define("ARTICLE_PATH","post/");

TEXT;

    $fh=fopen("config.php","w");
    fwrite($fh,$configContent);
    fclose($fh);
    return True;
}





/**********/


if (file_exists("config.php")){
    echo <<<HTML
<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
系统已经安装，如想重新安装系统，请删除所有旧文件，并上传一份全新的代码，再重新运行本程序。
</body>
</html>
HTML;




 
}else if(isset($_REQUEST['do'])){

    $database_name=md5(time());
    $data = $_REQUEST;
    $data['database_name']=$database_name;
    $data_path=$data['data_path'];
    if($data_path[strlen($data_path)-1]!=="/"){
        $data_path=$data_path."/";
    }
    if ($data_path[0]!=="/"){
        $data_path="ABS_ROOT_PATH.\"${data_path}\"";
    }else{
        $data_path="\"${data_path}\"";
    }
    $data['data_path']=$data_path;
    $step=createConfig($data);
    
    if ($step){
        $step=createDb($data);
    }else{
        $message="创建配置文件错误";
    }

    if($step){
        $step=createUser($data);
    }else{
        $message="创建数据库错误。如果非window系统，请将'admin'的权限设置为'777'";
    }

    if ($step){
        $message="Done.<span style='color:red'>安装完毕.请删除安装文件.</span><a href='admin'>点这里进入后台</a>进行管理.";
    }else{
        $message="创建用户数据错误";
    }
    echo <<<HTML
<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
    ${message}
</body>
</html>
HTML;

}else{








/**********/





echo <<<HTML
<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" type="text/css" href="css/base.css" />
    <link rel="stylesheet" type="text/css" href="admin/css/panel.css" />
    <link rel="stylesheet" type="text/css" href="admin/css/table.css" />
    <style>
        #wrapper{
            width:950px;
            margin:0 auto;
        }
    </style>
</head>
<body>
<div id="wrapper">
<h1>XPress安装</h1>
<form method=post action="install.php">

    <h2>用户设置</h2>
<div class="table">
    <table>
        <thead><tr><th>选项</th><th>值</th><th>说明</th></tr></thead>
        <tr><td>用户名</td><td><input name="username" type="input"></td><td>登陆后台所用用户名</td></tr>
        <tr><td>昵称</td><td><input name="nickname" type="input"></td><td>发表文章所用昵称</td></tr>
        <tr><td>密码</td><td><input name="password" type=password></td><td></td></tr>
        <tr><td>重新输入密码</td><td><input name="re-password" type=password onblur="if(this.value!==forms[0].password.value){alert('两次输入密码不一致，请检查');}"></td><td></td></tr>
    </table>
    <h2>信息设置</h2>
    <table>
        <thead><tr><th>选项</th><th>值</th><th>说明</th></tr></thead>
        <tr><td>博客名</td><td><input name="site_name" value="XPress" type=input></td><td></td></tr>
        <tr><td>博客副标题</td><td><input name="site_description" value="一个简单的博客" type=input></td><td></td></tr>
        <tr><td>博客域名</td><td><input name="site_domain" value="www.your_blog_domain.com" type=input></td><td></td></tr>
    </table>

    <h2>系统设置</h2>
    <table>
        <thead><tr><th>选项</th><th>值</th><th>说明</th></tr></thead>
        <tr><td>数据路径</td><td><input name="data_path" value="admin/data/" type=input></td><td>可以是绝对路径；最好在外部能访问的web目录之外。</td></tr>
    </table>

</div>
    <div class='btn'><input class="button" name=do type=submit value="安装"></div>
</form>
</div>
</body>
</html>
HTML;


}
