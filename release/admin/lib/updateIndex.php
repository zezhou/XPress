<?php
/**
 * update,delete index.html
 *  读入模板，载入变量，更新文件,更新相关信息（包括首页和系统的其它信息）
 * @todo 加入判断文章长度自动截断功能
 */
if(!defined("INCLUDE_CORE"))die("Permission denied.");
require_once(ADMIN_LIB_PATH."db.php");
require_once("html.php");
require_once("data.php");
/**
 * 更新首页的类
 * @param string $sIndexFilePath 首页文件的存放位置
 * @param string $sIndexFileTpl 首页模板的位置
 */
class UpdateIndex extends Data{
 	var $sIndexFilePath;
 	var $sIndexFileTpl;
    var $totalPageNum;
    var $db;
	var $log;
 	function UpdateIndex(){
 		$this->sIndexFilePath = is_null(INDEX_PAGE_NAME)?"index.html":INDEX_PAGE_NAME;
 		$this->sIndexFileTpl = is_null(INDEX_PAGE_TPL)?"template/index.html":INDEX_PAGE_TPL;
 	    $this->db=new DB();
 	}
 	
 	/**
 	 * update index files
 	 * 更新首页和index页
	 * 首页显示文章数为每页文章展示数+余数
	 * 总文章数除以每页文章展示数为页面显示的余数。
     * 如果是重新建立所有页面，则从$nNewTotalPageNum开始一个一个建立页面。
     * 如果不是重新建立所有页面，则认为之前建立的所有页面都可靠且无问题，
     * 从$nNewTotalPageNum开始建立最多不超过三个的首页。用break退出。
 	 * @return boolean
 	 */
 	function update(){
        $return=true;
		$this->totalArticleNum=$this->db->getTotalNum();
        $remainder=$this->totalArticleNum%(INDEX_ARTICLE_NUMBER);
        if($this->totalArticleNum<INDEX_ARTICLE_NUMBER){
            $this->totalPageNum=1;
			$this->firstPageArticleNum=$remainder;
        }else{
            $this->totalPageNum=floor($this->totalArticleNum/INDEX_ARTICLE_NUMBER);
            $this->firstPageArticleNum=$remainder+INDEX_ARTICLE_NUMBER;
        }
		for($i=0;$i<$this->totalPageNum;$i++){
		    $aData=$this->getData($i);
		    $html=new html();
			$return=$html->create($aData,$this->sIndexFileTpl,ABS_ROOT_PATH.$aData['path'])&&$return;
		}
		return $return;
 	}
 	/**
 	 * 检测是否有非法数据。输入需要检测的数据，输出过滤后的数据。
 	 * @param $data 输入要检测的数据
 	 * @return mix
 	 */
	
 	function getArticleData($nStartNum=0,$nUpdateNum = INDEX_ARTICLE_NUMBER){
		global $user;
 		/*
 		 * 这里添加首页需更新内容的html代码
 		 */
 		$aData['site_name']=SITE_NAME;
 		if($nStartNum==0){$aData['site_title']=$aData['site_name'];}
 		else{$aData['site_title']="<a href=\"".INDEX_PAGE_NAME."\">".$aData['site_name']."</a>";}
 	
 		$aData['site_description']=SITE_DESCRIPTION;
 		$aData['aside']="博主简介";
 		/*
 		 * main article to present to index page.
 		 */

 		$sSql="select id,title,content,tags,time,author,url from ".DATABASE_ARTICLES_NAME." order by id DESC limit ".$nStartNum.",".$nUpdateNum ;
 		if($aResult=$this->db->arrayQuery($sSql,SQLITE_ASSOC)){
 			$article="<ol id=\"content\">";
 			foreach($aResult as $key=>$value){
 				$sDateYM=@date("y-m",intval($value['time']));
                $sDateD=@date("d",intval($value['time']));
 				$article.=<<<EOF
<li>
<h2 class="title">
<div class="time"><em class="time-y-m">{$sDateYM}</em><big>{$sDateD}</big></div>
<a href="{$value['url']}">{$value['title']}</a>
<br><small>{$value['author']}&nbsp;关键词:{$value['tags']}</small>
</h2>{$value['content']}
</li>
EOF;
 			}
 			$article.="</ol>";
 			$aData['article']=$article;
 			
 			return $aData;
 		
 		}else{
 		return false;	
 		
 		}

 		
 	}
    /**
     * 获得生成index页所需的数据
     * @param Int $i 需要获取的数据的页数
     * @return Array 
     */
    function getData($i){
    	//如果是第一页
        if($i==0){
            $data=$this->getFirstPageData($i);
        }
        //如果是第二页
        elseif ($i==1){
            $data=$this->getSecondPageData($i);
        }
        //如果是最后一页
        elseif($i==$this->totalPageNum-1){
            $data=$this->getLastPageData($i);
        }
        //其他页面
        else{
            $data=$this->getPageData($i);
        }
        $data['version']=XPRESS_VERSION;
        return $data;
    }

    /**
     * 更新首页的页面
     * @return Array 返回整理的生成页面需要的数据
     */
    function getPageData($i){
        $aData=$this->check($this->getArticleData($this->firstPageArticleNum+INDEX_ARTICLE_NUMBER*($i-1)),"output");
        //设置上一页/下一页
        $aData['previous']='<a href="index_'.($this->totalPageNum-$i+1).'.html" id="preview" class="btn fl">上一页</a>';
        $aData['path']="index_".($this->totalPageNum-$i).".html";
        $aData['next']='<a href="index_'.($this->totalPageNum-$i-1).'.html"  id="next" class="btn fl">下一页</a>';
        return $aData;
    }


    /**
     * 更新index的第一个页面
     * @param Int 生成的页号
     * @return Array 返回整理的生成页面需要的数据
     */
    function getFirstPageData($i){
        $aData=$this->getPageData($i);
        $aData=$this->check($this->getArticleData(0,$this->firstPageArticleNum),"output");
        $aData['path']=$this->sIndexFilePath;
        $aData['previous']="";
        $aData['next']=$this->totalPageNum>1?'<a href="index_'.($this->totalPageNum-1).'.html" id="readmore_btn" class="btn fl">查看更多内容</a>':"";
        return $aData;
    }

    /**
     * 更新index的第二个页面
     * @param Int 生成的页号
     * @return Array 返回整理的生成页面需要的数据
     */
    function getSecondPageData($i){
        $aData=$this->getPageData($i);
        $aData['previous']='<a href="'.INDEX_PAGE_NAME.'"  id="preview" class="btn fl">上一页</a>';
        if($this->totalPageNum==2){
            $aData['next']='<div id="next" class="fl"></div>';
        }
        return $aData;
    }

    /**
     * 更新index的最后页面
     * @param Int 生成的页号
     * @return Array 返回整理的生成页面需要的数据
     */
    function getLastPageData($i){
        $aData=$this->getPageData($i);
        $aData['next']='<div id="next" class="fl"></div>';
        return $aData;
    }

}
