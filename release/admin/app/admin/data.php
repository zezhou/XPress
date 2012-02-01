<?php
/**
 *  向管理后台提供各种数据的接口
 */
if (!(defined("INCLUDE_CORE"))){die("Permission denied.");}
require_once(ADMIN_PATH."common.php");
$action=$_REQUEST['action'];
switch($action){
case "init":
$siteName=SITE_NAME;
    echo <<<INIT
{
    "menu":[{
        "name":"发布文章",
        "url":"article/post.html"
        },{
        "name":"管理文章",
        "url":"article/manage.html"
        },{
        "name":"管理系统",
        "url":"system/system.html"
        },{
        "name":"管理用户",
        "url":"user/user.html"
        }],
    "siteName":"${siteName}"
        
}
INIT;
    break;

case "getUserList":
    $userData=array();
    foreach($user as $index=>$item){
        $userData[]=array($index,$item['username'],$item['nickname']);
    }
    $data=array("success"=>True,"data"=>$userData);
    $data=json_encode($data);
    echo $data;
    break;

case "getArticleList":
    $res=array();
    require_once(ADMIN_LIB_PATH."db.php");
    $db=new DB();
    $sql="select id,title,url from ".DATABASE_ARTICLES_NAME." order by id DESC limit 0,10";
    $query=$db->arrayQuery($sql);
    for ($i=0;$i<count($query);$i++){
        $res[]=array($query[$i]['id'],$query[$i]['title'],$query[$i]['url']);
    }
    $data=array("success"=>True,"data"=>$res);
    echo json_encode($data);
    break;

case "getArticle":
    $res=array();
    require_once(ADMIN_LIB_PATH."db.php");
    $db=new DB();
    $sql="select * from ".DATABASE_ARTICLES_NAME." where id=".$_REQUEST['id'];
    $query=$db->arrayQuery($sql);
    $content = $query[0];
    $content=str_replace('\n',chr(10),$content);
    $content=str_replace('\r',chr(13),$content);
    $data=array("success"=>True,"data"=>$content);
    echo json_encode($data);
    break;
}
