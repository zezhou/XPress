<?php
/**
 *  发布文章
 */
if(!defined("INCLUDE_CORE"))die("Permission denied");
require_once(ADMIN_LIB_PATH."db.php");
require_once(ADMIN_LIB_PATH."data.php");
require_once(ADMIN_LIB_PATH."html.php");
class Publish extends Data{
    var $db;
    function Publish(){
        $this->db=new DB();
    }

	function update($data=null){
        return $this->updateAll();
    }
	/**
	 * 更新所有文章和首页
	 * @param none
	 * @return boolean
	 */
    function updateAll(){
		if($this->updateHTML("all")){
            if($this->updateIndexHtml()){
                if($this->updateRss()){
		            return true;
                }
            }
        }
        return false;
	}

    #更新rss
    function updateRss(){
        require_once(ADMIN_LIB_PATH."updateRss.php");
        $rssUpdate=new updateRss();
        return $rssUpdate->run();
    }

	/*
	 * 更新HTML文件
	 * 
	 * @param	int	$id	更新的文件id,id可以为一个包含需要更新id的数组或一个数字
	 * @todo	improve this function in the future.add  fault tolerance feature.
	 * @return	boolean
	 * 
	 */
	function updateHTML($id){
		if(is_array($id)){
			$id=join(",",$id);
		}
		$sSql="select * from ".DATABASE_ARTICLES_NAME;
		if($id!=="all") $sSql=$sSql." where id=$id";
		$data=$this->db->arrayQuery($sSql,SQLITE_ASSOC);
		$checkedData=$this->check($data,"output");
		$bReturn=true;
		if (is_array($checkedData)&&sizeof($checkedData)>0){
            foreach($checkedData as $key=>$value){
                $data=$this->getData($value);
                $bReturn=$this->createArticleHtml($data,ARTICLE_PAGE_TPL)&&$bReturn;
            }
		
        }
		return $bReturn;
	}
	
	/**
	 * 更新,生成文章
	 * @param array  数据
	 * @return Boolean
	 */
	function createArticleHtml($data){
        $createFileName=isset($data['url'])?$data['url']:$this->getArticleUrl($data);
        $tpl=isset($data['articleTpl'])?$data['articleTpl']:ARTICLE_PAGE_TPL;
		$data=$this->check($data,"output");
		$html=new html();
		return $html->create($data,$tpl,$createFileName);
	}
	
    /**
	 * 载入生成页面需要的数据
	 * @param array $data 需要被处理的数据
	 * @return array
	 */
	function getData($data){
        $data['sContent']=$data['content'];
        $nPreId=$this->db->getPreId($data['id']);
        $nNextId=$this->db->getNextId($data['id']);
        
        $sSQL="select title,url from ".DATABASE_ARTICLES_NAME;
        
        if($nPreId) {
            $sSQL.=" where id=".$nPreId;
        }
        
        if($nNextId && $nPreId) {
            $sSQL.=" or id=".$nNextId;
        }elseif($nNextId && !$nPreId){
            $sSQL.=" where id=".$nNextId;
        }
        $aResult=$this->db->arrayQuery($sSQL);
        if($nNextId && $nPreId) {
            $data['next']="<a href=\"".ARTICLE_RELATIVE_DIR.$aResult[1]['url']."\" style=\"float:right\" \">下一篇:".$aResult[1]['title']."</a>";
        }elseif($nNextId && !$nPreId){
            $data['next']="<a href=\"".ARTICLE_RELATIVE_DIR.$aResult[0]['url']."\" style=\"float:right\" \">下一篇:".$aResult[0]['title']."</a>";
        }else{
            $data['next']="";
        }
        
        if($nPreId) {$data['previous']="<a href=\"".ARTICLE_RELATIVE_DIR.$aResult[0]['url']."\">上一篇:".$aResult[0]['title']."</a>";
        }else{
            $data['previous']="";
        }


		$aResult=$this->db->getLastArticle();
        if (sizeof($aResult)>0){
		    $this->data['previous']="<a href=\"".ARTICLE_RELATIVE_DIR."/".$aResult[0]['url']."\">上一篇:".$aResult[0]['title']."</a>";
        }else{
		    $this->data['previous']="";
        }

		$this->data['next']="";
		$data['heading']=$data['title'];
        $data['site_name']=SITE_NAME;
 		$data['site_description']=SITE_DESCRIPTION;
		$data['site_url']=ARTICLE_RELATIVE_DIR;
		$data['site_domain']=SITE_DOMAIN;
        if(isset($data['sContent']) && !isset($data['content'])){
            $data['content']=$data['sContent'];
        }
        if(!isset($data['author'])){
            if (isset($_SESSION['nickname'])){
                $data['author']=$_SESSION['nickname'];
            }elseif(isset($_SESSION['username'])){
                $data['author']=$_SESSION['username'];
            }else{
                $data['author']="anonymous";
            }
        }
        $data['cndate']=@date("Y年m月d日",$data['time']);
        $data['articleTpl']=ARTICLE_PAGE_TPL;
        $data['version']=XPRESS_VERSION;

        $aData=$this->db->getRelativeArticle($data); // relative articles
		if(count($aData)>0){
            $data['relative_article']="<div id=\"relativeArticle\"><h3>你可能还会对以下文章感兴趣：</h3><ul>".count($aData);
            foreach($aData as $key=>$value){
                if($data['title']==$value['title']) continue;
                $data['relative_article'].="<li><a href=\"".$value['url']."\">".$value['title']."</a></li>";
            }
		    $data['relative_article'].="</ul></div>";
		}else{
			$data['relative_article']="";
		}
        return $data;
	}


    /**
	 * 更新首页
	 * @return boolean
	 */
	function updateIndexHtml(){
        require_once(ADMIN_LIB_PATH."updateIndex.php");
		$index=new updateIndex();
		return $index->update();	
	}
}
