<?php
/*
 * no cache template class
 *  
 * Version: 0.0.1
 */


class SimpleTemplate  {
var $_tpl_vars;
var $achieve;
var $tag;
var $refill;
var $errMsg="";
/**
 * 简易模板类,结构函数
 * 
 * @return void
 * 
 */

function SimpleTemplate(){
//empty
}
/**
 * 标记需要该模板类替换的变量以及该变量的值
 * 
 * @param $tpl_var	变量名
 * @param $value	替换值
 * @return void
 */
function assign($tpl_var , $value = null)
    {
        if (is_array($tpl_var)){
            foreach ($tpl_var as $key => $val) {
                if ($key != '') {
                    $this->_tpl_vars[$key] = $val;
                }
            }
        } else {
            if ($tpl_var != '')
                $this->_tpl_vars[$tpl_var] = $value;
        }
    }
/**
 * 将模板变量替换，并显示在页面上。如果只需获得替换后的模板的数据，而不想在页面上显示该数据，请使用$simple_template->parse()函数
 * 
 * @param $tplFile	模板文件的地址
 * @return string or boolean
 */
function display($tplFile,$vars=null){
    if (isset($vars)){
        if (is_array($vars)){
            $len=sizeof($vars);
            for ($i=0;$i<$len;$i++){
                $item=$vars[$i];
                $key=$item['key'];
                $val=$item['value'];
                $this->assign($key,$val);
            }
        }
    }
	if($achieve=$this->parse($tplFile)){
		echo $achieve;
	}else{
		return false;
	}
}
/**
 * 将模板变量进行替换，返回替换后的数据
 * @param $tplFile	模板文件的地址
 * @return string or boolean
 */
function parse($tplFile){
		$sTplLeftSymbol=is_null(TPL_LEFT_SYMBOL)?"<{\$":TPL_LEFT_SYMBOL;
		$sTplRightSymbol=is_null(TPL_RIGHT_SYMBOL)?"/}>":TPL_RIGHT_SYMBOL;
		if(file_exists($tplFile)) {
		    if($tpl=file_get_contents($tplFile)){
                if($this->_tpl_vars){
                    foreach($this->_tpl_vars as $key =>$value){
                        $this->tag[]=$sTplLeftSymbol.$key.$sTplRightSymbol;
                        $this->refill[]=$value;
                    }
                }

                    $achieve= @str_replace($this->tag,$this->refill,$tpl);
                    $encLeftSymbol=str_replace(array("\\","$","{","}","/"),array("\\\\","\\$","\\{","\\}","\\/"),$sTplLeftSymbol);
                    $encRightSymbol=str_replace(array("\\","$","{","}","/"),array("\\\\","\\$","\\{","\\}","\\/"),$sTplRightSymbol);
                    $reEmptySybol=$encLeftSymbol."\w+".$encRightSymbol;
                    $achieve= @preg_replace('/'.$reEmptySybol.'/',"",$achieve);
                    return $achieve;
		}else{
		$this->errMsg.="模板文件不能打开，请检查文件属性!";
		return false;
		}
	
	}
	else
	{
	$this->errMsg.="模板文件不存在!";
	return false;
	}

}
/**
 * 显示错误信息
 * 
 * @return unknown_type
 * @todo 增加与log类的接口？
 */
function halt(){
    //when someting false happen
    echo $this->errMsg;
    }
}

?>
