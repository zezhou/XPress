<?php 
/**
 * the class which create html.
 * @version 20090110
 */
if(!defined("INCLUDE_CORE"))die("Permission denied.");
require_once("simple_template.php");

class html{
	var $sTplLeftSymbol;
	var $sTplRightSymbol;
	function html(){
		$this->sTplLeftSymbol=is_null(TPL_LEFT_SYMBOL)?"<{\$":TPL_LEFT_SYMBOL;
		$this->sTplRightSymbol=is_null(TPL_RIGHT_SYMBOL)?"/}>":TPL_RIGHT_SYMBOL;
    }
    /**
     *  生成html源代码
     */
    function generate($tplData,$tplFile=""){
        if(empty($tplFile)){
			$data=$tplData;
		}else{
			$tpl=new simpleTemplate();
			$tpl->assign($tplData);
			$data=$tpl->parse($tplFile);
        }
        return $data;
    }
    /**
     * 创建html文件并将内容写入文件
     */
	function create($tplData,$tplFile="",$createFileName=""){
		$data = $this->generate($tplData,$tplFile);
		$createFileName=empty($createFileName)?$this->getRandomName():$createFileName;
		$sDir=dirname($createFileName);
		if(!is_dir($sDir)){
			mkdir($sDir,0755,true);
		}
		$fp=fopen($createFileName,"w");
		if(fwrite($fp,$data)){
			fclose($fp);
			return true;
		}else{
			fclose($fp);
			return false;
		}
		
	}
	function getRandomName(){
		return rand(date()).".html";
	}
}
