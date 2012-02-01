<?php
if (!(defined("INCLUDE_CORE") && defined("INCLUDE_CORE"))){die("Permission denied.");}
require_once(ADMIN_PATH."common.php");
require_once(ADMIN_LIB_PATH."edit.php");
require_once(ADMIN_LIB_PATH."php-markdown/markdown.php");
$edit=new Edit();
$aParam=$edit->check($_REQUEST,"input");
$action=$aParam['action'];

if (method_exists($edit,$action)){
    if(isset($aParam['mode']) && $aParam['mode'] == "markdown" && $aParam['content']){ // surport markdown
        $aParam['content']=str_replace('\n',chr(10),$aParam['content']);
        $aParam['content']=str_replace('\r',chr(13),$aParam['content']);
        $aParam['content']=Markdown($aParam['content']);
    }
    $return=$edit->$action($aParam);
    if ($return===true){
    echo <<<EOF
{"success":true}
EOF;

    }elseif(isset($return) && (is_string($return) || is_int($return))){
     echo <<<EOF
{"success":true,"data":$return}
EOF;
    }else{
    echo <<<EOF
{"success":false}
EOF;
    }
}

