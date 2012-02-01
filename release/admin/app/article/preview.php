<?php
require_once("../../../config.php");
require_once(ADMIN_PATH."common.php");
require_once(ADMIN_LIB_PATH."preview.php");
$id=$_REQUEST["id"];
$preview = new Preview();
$html = $preview->getArticle($id);
header("Content-Type: text/html");
echo $html;
