<?php
require_once("config.php");
define("INCLUDED_BY_API",true);

if (isset($_REQUEST) && isset($_REQUEST['request'])){
    if (isset($_REQUEST['request'])){
        $request=$_REQUEST['request'];
    }else if(isset($_REQUEST['r'])){
        $request=$_REQUEST['r'];
    }else{
        $request=null;
    }
    $actItems=explode("/",$request);
    if (sizeof($actItems)>1){
        $module=$actItems[0];
        $app=$actItems[1].".php";
        $modulesList=scandir(MODULE_PATH);
        if (in_array($module,$modulesList)){
            $modulePath=MODULE_PATH.$module."/";
            $appsList=scandir($modulePath);
            if(in_array($app,$appsList)){
                $appPath=$modulePath.$app;
                define("REQUEST_MODULE_PATH",$modulePath);
                require_once($appPath);
                if (function_exists("run")){
                    run($_REQUEST);
                }              
            } 
        }
    }
}
