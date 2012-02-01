<?php 
/*
 * data.php -  the class which get data from form with post or get method
 * @version: 20090110
 */

class Data {
	/**
	 * 构造函数，无内容
	 * @return none
	 */
	function data(){
		
	}
	/**
	 * 获取POST或GET的数据。
	 * @param $method	发送数据的方法
	 * @param $name	发送数据的字段名
	 * @return array
	 */
	function get($method,$name){
		$aData=array();
		if($method=="post" || $method=="both"){
			if(is_array($name)){
				foreach($name as $key=>$val){
					$aData[$val]=$_POST[$val];
				}
			}else{
				$aData[$val]=$_POST[$val];
			}
		}
		if($method=="get" || $method=="both"){
			if(is_array($name)){
				foreach($name as $key=>$val){
					$aData[$val]=$_GET[$val];
				}
			}else{
				$aData[$val]=$_GET[$val];
			}
		}
		return $aData;
	}
	/**
	 * 检查数据的正确性，进行数据的替换
	 * @param $data 传入的数据
	 * @param $action 数据的类型
	 * @return array || string
	 */
	function check($data,$action="input"){
		$return="";
		if(is_array($data)){
			
			foreach($data as $key=>$val){
				$return[$key]=$this->checkOne($val,$action);
				}
			}
			else{
			$return=$this->checkOne($data,$action);
		}
		//var_dump($return);
		return $return;
	}
	/**
	 * 检查一个数据的正确性，进行数据的替换
	 * @param $data 传入的数据
	 * @param $action 数据的类型
	 * @return string
	 */
	function checkOne($data,$action){
		switch($action){
				case "input":
					$data=str_replace("\n",'\n',$data);
					$data=str_replace("\r",'\r',$data);
					break;
				case "output":
					$data=str_replace('\n',chr(10),$data);
					$data=str_replace('\r',chr(13),$data);
					break;
					
				default:
					
					break;	
			}
			return $data;
	}
}
?>
