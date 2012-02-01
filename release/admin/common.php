<?php
if (!defined("INCLUDE_CORE")){die("Permission denied.");}
session_start();
header("Content-Type: text/javascript");
require_once(DATA_PATH."config.php");
require_once(ADMIN_LIB_PATH."login.php");
$login=new Login(
    array(
        'user' => $user,
        'salt' => $salt
    )
);
if(!$login->checkBySession()) die('{"success":false,"message":"操作权限错误"}');
