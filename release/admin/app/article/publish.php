<?php
if (!(defined("INCLUDE_CORE") && defined("INCLUDED_BY_API"))){die("Permission denied.");}
require_once(ADMIN_PATH."common.php");
require_once(ADMIN_LIB_PATH."publish.php");
$publish=new Publish();
$aParam=$publish->check($_REQUEST,"input");
if (isset($_REQUEST['action'])){
$action=$_REQUEST['action'];
$return=$publish->$action($aParam);
if ($return===true){
    echo <<<EOF
{"success":true}
EOF;
    }else{
    echo <<<EOF
{"success":false}
EOF;
    }
}
