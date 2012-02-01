<?php
/**
 * 将数据输入，输出xml格式的数据
 * [算法]:使用数组储存数据，下标表示标签名，内容表示数据。通过循环得到想要的东西。
 * @version: 20090509
 */
class rss extends data{
	var $aData;
	var $sSpecVersion;
	
	public function __construct($aData="",$sSpecVersion="rss2.0"){
		$this->aData=$aData;
		$this->sSpecVersion=$sSpecVersion;
	}
	
	function output(){

		$sReturn=<<<EOF
<?xml version="1.0"?>
<rss version="2.0">
<channel>
{$this->getContent($this->aData)}
</channel>
</rss>
EOF;
		return $this->check($sReturn,"output");
	}
		
  	function getContent($aData){
   	$sReturn="";

	foreach($aData as $key=>$val){
		if(is_array($aData[$key])){
			if(is_numeric($key)){
				$sReturn.="<$this->lastKey>".self::getContent($aData[$key])."</$this->lastKey>\n";
				
			}else{
				$this->lastKey=$key;
				$sReturn.=self::getContent($aData[$key]);
			}
		}else{
				$sReturn.="<$key><![CDATA[{$aData[$key]}]]></$key>\n";
		}
		
		
	}
	

	return $sReturn;
	}	
	function getItem($aData){
		$sReturn="";
		
		
		return $sReturn;
	}
}
