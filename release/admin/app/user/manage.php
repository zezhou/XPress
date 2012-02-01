<?php
if (!(defined("INCLUDE_CORE"))){die("Permission denied.");}
require_once(ADMIN_PATH."common.php");
require_once(ADMIN_LIB_PATH."user.php");
$action=$_REQUEST['action'];
$oUser=new User(array("salt"=>$salt,"user"=>$user));
//header("Content-Type: text/javascript");
switch($action){
    case "modify":
        $data=array();
        if (isset($_REQUEST['username'])){
            $data['username']=$_REQUEST['username'];
        }
        if (isset($_REQUEST['nickname'])){
            $data['nickname']=$_REQUEST['nickname'];
        }
        if (isset($_REQUEST['password'])){
            $data['password']=$_REQUEST['password'];
        }
        $res = $oUser->modify($data)?array("success"=>true):array("success"=>false);
        echo json_encode($res);
        break;

    case "delete":
        $index=$_REQUEST['index'];
        $res = $oUser->delete($index)?array("success"=>true):array("success"=>false);
        echo json_encode($res);
        break;
}
?>
