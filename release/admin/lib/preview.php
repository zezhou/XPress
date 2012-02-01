<?php
/**
 * 生成发布文章的预览数据
 */

if(!defined("INCLUDE_CORE"))die("Permission denied");
require_once(ADMIN_LIB_PATH."publish.php");

class Preview extends Publish{
    function getArticle($id){
		$sSql="select * from ".DATABASE_ARTICLES_NAME." where id=$id";
        $fetchData = $this->db->arrayQuery($sSql);
        if (sizeof($fetchData)>=1){
            $fetchData=$fetchData[0];
        }
		$value=$this->check($fetchData,"output");
        $data=$this->getData($value);
        $data['site_url']="../../../";
        $data['site_domain']=SITE_DOMAIN;
        $tpl=isset($data['articleTpl'])?$data['articleTpl']:ARTICLE_PAGE_TPL;
		$data=$this->check($data,"output");
		$html=new html();
		return $html->generate($data,$tpl);
    }
}
