<?php
/**
 * wordpress 到 xpress的转换程序
 * 分两部分：1，将XPRESS需要的WP数据以sql的形式保存为一个文本文件。
 * 			2，将文本文件导入xpress，并更新全站
 *
 */

require_once("../../core.php");
	
#insert into xpress
class insertData{
	function insertData($file){
		$db=new SQLiteDatabase(DATABASE_NAME);
		$sqlQuery=file($file);
		foreach($sqlQuery as $key=>$values){
			$query=$this->checkData($values);
			if(empty($query)) continue;
				if($db->query($query)){
				//echo "<span style=\"color:green\">执行语句：<pre>".$query."</pre>成功！</span><br>";
				} else{
					echo "<textarea>$query</textarea>";
				//echo "<span style=\"color:red\">执行语句：<pre>".$query."</pre>失败！</span><br>";
				}
			
		}
	}
	
	
	function checkData($data){
		/**
		 * @todo finish this function in the future
		 */

		$data=trim(str_replace("\n",'\n',str_replace("\r",'\r',$data)));
		$data=substr($data,0,-2);
		$data=preg_replace("/\<\!\-\-(.*)\-\-\>/i","",$data);
		$data=preg_replace("/\<div id=wp_internal style=position\:absolute\;left\:\-9112px>(.*)<\/a><\/div>/i","",$data);
		return $data;
	}
/**
 * Add slashes before "'" and "\" characters so a value containing them can
 * be used in a sql comparison.
 *
 * @param   $a_string string   the string to slash
 * @param   $is_like boolean  whether the string will be used in a 'LIKE' clause
 *                   (it then requires two more escaped sequences) or not
 * @param   $crlf boolean  whether to treat cr/lfs as escape-worthy entities
 *                   (converts \n to \\n, \r to \\r)
 *
 * @param   $php_code boolean  whether this function is used as part of the
 *                   "Create PHP code" dialog
 *
 * @return  string   the slashed string
 *
 * @access  public
 */
	function PMA_sqlAddslashes($a_string = '', $is_like = false, $crlf = false, $php_code = false)
	{
    if ($is_like) {
        $a_string = str_replace('\\', '\\\\\\\\', $a_string);
    } else {
        $a_string = str_replace('\\', '\\\\', $a_string);
    }

    if ($crlf) {
        $a_string = str_replace("\n", '\n', $a_string);
        $a_string = str_replace("\r", '\r', $a_string);
        $a_string = str_replace("\t", '\t', $a_string);
    }

    if ($php_code) {
        $a_string = str_replace('\'', '\\\'', $a_string);
    } else {
        $a_string = str_replace('\'', '\'\'', $a_string);
    }

    return $a_string;
	}
}
?>
