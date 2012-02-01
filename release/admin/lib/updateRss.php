<?php
if(!defined("INCLUDE_CORE"))die("Permission denied");
require_once("rss.php");

class updateRss extends rss{
	var $sXmlFileName="rss.xml";
	var $sXmlFilePath=ABS_ROOT_PATH;
	var $status=false;
	var $sSpecVersion="rss2.0";
	function updateRss(){
		
		
	}
	function run(){
        $this->aData=$this->format($this->getData());
		if($this->createXmlFile($this->output(),$this->sXmlFilePath.$this->sXmlFileName)){
			return true;
		}else{
			return false;
		}
    }
	function createXmlFile($aData,$file){
		require_once("html.php");
		$html=new html();
		if($html->create($aData,"",$file)){
			return true;
		}
		else{
			return false;
		}
		
	}
	function getData($sql=""){
		
		global $db,$user;
		if(empty($db)){
			require_once("sqlite_db.php");
			$db_handle=new sqlite_db(DATABASE_NAME);
			$db=$db_handle->handle;
		}
		
		if(empty($sql)){
			$sql="select * from ".DATABASE_ARTICLES_NAME."  order by time DESC limit 0,10";
		}
		
		$aData=$db->arrayQuery($sql);
		return $aData;
	}
	function format($aData){
		
		$aReturn=array();
		

		switch($this->sSpecVersion){
			case "rss2.0":
			//required
			$aReturn['title']=SITE_NAME;//The name of the channel.Required.
			$aReturn['link']="http://".SITE_DOMAIN;//The URL to the HTML website corresponding to the channel.Required.
			$aReturn['description']=SITE_DESCRIPTION;//Phrase or sentence describing the channel.Required.
			
			//optional
			$aReturn['language']="zh-CN";
			//copyright is omited
			//managingEditor is omited
			//webMaster is omited
			//pubDate is omited
			$aReturn['lastBuildDate']=@date(DATE_RFC822);
			//category is omited
			//generator is omited
			//docs is omited
			//cloud is omited
			//ttl is omited
			//image is omited
			//rating is omited
			//textInput is omited
			//skipHours is omited
			//skipDays is omited
			
			
			//items content
			
			$aItems=array();
			foreach ($aData as $key=>$val){
				$aItem=array();
				
				$aItem['title']=$val['title'];
				$aItem['link']="http://".SITE_DOMAIN."/".$val['url'];
				$aItem['description']=$val['content'];
				$aItem['author']=$val['author'];
				//comments is omited
				//enclosure Describes a media object that is attached to the item. is omited
				//guid is omited
				$aItem['pubDate']=@date(DATE_RFC822,$val['time']);
				//source The RSS channel that the item came from is omited
				$aItems[]=$aItem;
			}
			$aReturn['item']=$aItems;
			break;
		}
		return $aReturn;
	}
}
