<?php
require_once("../config.php");
if (!defined("INCLUDE_CORE")){die("Permission denied.");}
require_once(DATA_PATH."config.php");
require_once(ADMIN_LIB_PATH."login.php");

$msg="";
session_start();
$login=new Login(
    array(
        'user' => $user,
        'salt' => $salt
    )
);

$loginSuccess=False;

if(isset($_REQUEST) && isset($_REQUEST['username']) && isset($_REQUEST['password'])){
	$username=$_REQUEST['username'];
	$password=$login->getPwd($_REQUEST['password']);
}else if(isset($_SESSION) && isset($_SESSION['username']) && isset($_SESSION['password'])){
    $username=$_SESSION['username'];
    $password=$_SESSION['password'];
}
if(isset($username) && isset($password)){
    $login->isSaveSession=true;
    if(@$_REQUEST['recall']==="on"){// if recall equal to "on"
        $login->isSaveCookie=true;
    }
    if ($login->check($username,$password)){
        $loginSuccess=true;
        header('Location: app/admin/main.html');
    }else{
        $msg="?msg=".$login->msg;
    }

}
if(!$loginSuccess){
    header('Location: app/admin/login.html'.$msg);
}
