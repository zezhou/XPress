<?php
/**
 * transfer wordpress data to xpress data
 */
define("REBUILD",true);
require_once("../../core.php");
require_once("./getWpData.class.php");
require_once("./insertData.class.php");
require_once(ADMIN_PATH."class/edit.php");
require_once(ADMIN_PATH."data/config.php");
$action=$_GET['action'];

################配置
$datafile="wpdata.txt";#存放数据的地址
$host="localhost";
$usrname="";
$pwd="";
$dbname="";
$html="";
################操作表单
$operaForm=<<<EOF
		请选择操作：<select name="opera">
		<option value="auto">自动导出/导入数据</option>
		<option value="export">从Wordpress导出数据</option>
		<option value="import">将数据导入xpress</option>
		</select>&nbsp;
		<input type="submit" name="submit" onclick="goto()" value="submit">
EOF;


################对操作进行处理

switch ($action){
	case "export":
		$getWpData=new getWpData($host,$usrname,$pwd,$dbname);
		if(!is_null($getWpData->sqlData))
		{
			echo "成功得到WP数据。<br>";
			if($handle=fopen($datafile,"w")){
			echo "打开数据文件成功<br>";
			
				if(fwrite($handle,$getWpData->sqlData)){
					echo "写入数据成功<br>";
					echo "$getWpData->sqlData";
				}
			
			fclose($handle);
			}else{
				
				echo "打开数据文件失败<br>";
			}
			
			
			
		}else{
			echo "获取数据时发生错误！<br>";
		}
		
		$html=$operaForm;
		break;
		
	case "import":
		
		
		$getWpData=new insertData($datafile);
		$edit=new edit();
		$edit->updateAll();
		foreach ($edit->log as $key=>$val){
			$html.=$val;
		};
		break;
	case "clear":
		if(true==DEBUG) {
			if($handle=fopen(DATABASE_NAME,"w")){
			$html.="打开数据文件成功<br>";
			
				if(fwrite($handle," ")){
					$html.="清除数据库内容成功";
				}
			
			fclose($handle);
		}
		}
		
		$html.=$operaForm;
		
		break;
	case "auto":
		if(true==DEBUG) {
			if($handle=fopen(DATABASE_NAME,"w")){
			$html.="打开数据文件成功<br>";
			
				if(fwrite($handle," ")){
					$html.="清除数据库内容成功";
				}
			
			fclose($handle);
		}
		}
		
		
		$getWpData=new insertData($datafile);
		$edit=new edit();
		$edit->updateAll();
		foreach ($edit->log as $key=>$val){
			$html.=$val;
		};
		$html.=$operaForm;
		break;
default:
$html=$operaForm;
break;	
}



echo <<<EOF

<!doctype html>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>数据转换</title>
<script> 
function goto(){
var action=document.getElementsByName("opera")[0].value;
location="?action="+action;
}
</script>
</head>

<body>
$html
</body>
</html>

EOF;
?>

