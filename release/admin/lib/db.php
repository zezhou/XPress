<?php
/**
 *  对数据库操作的通用封装
 */
if (!defined("INCLUDE_CORE")) die("Permission denied.");
require_once("sqlite_db.php");
class DB extends SqliteDB{
    var $db;
    function DB($dbname=null){
        $dbname=is_null($dbname)?DATABASE_NAME:$dbname;
        parent::__construct($dbname);
        //$this->SqliteDB($dbname);
    }
    function query($sql){
        return $this->db->query($sql);
    }
    function singleQuery($sql){
        return $this->db->singleQuery($sql);
    }
    function arrayQuery($sql){
		$aResult=$this->db->arrayQuery($sql);
        return $aResult;
    }
    function getLastArticle(){
		$sSQL="select title,url from ".DATABASE_ARTICLES_NAME." order by id DESC limit 0,1";
		$aResult=$this->db->arrayQuery($sSQL);
        return $aResult;
    }

	/**
	 * insert data into SQLite database.
	 * @param	array	$data	插入数据库的数据
	 * @return	boolean	
	 */
    function insert($sSqlKeys,$sSqlValues){
		$sSql="insert into ".DATABASE_ARTICLES_NAME." ($sSqlKeys) values ($sSqlValues)";
		return $this->db->query($sSql);
	}

    function getRelativeArticle($data){
		$sSql="select title,url from ".DATABASE_ARTICLES_NAME." where tags='".$data["tags"]."' and title<>'".$data['title']."' limit 0,".RELATIVE_ARTICLE_NUM;
		$aData=$this->db->arrayQuery($sSql,SQLITE_ASSOC);
	    return $aData;
    }
	function getNextId($nId){
		$sql="select * from ".DATABASE_ARTICLES_NAME." where id>".$nId." order by id ASC limit 0,1";
		$tmpResult=$this->db->singleQuery($sql);
		return $tmpResult;
	}
	function getPreId($nId){
		$sql="select * from ".DATABASE_ARTICLES_NAME." where id<".$nId." order by id DESC limit 0,1";
		$tmpResult=$this->db->singleQuery($sql);
		return $tmpResult;
    }
	function getMaxId(){
        $res=$this->db->arrayQuery("select max(id) from ".DATABASE_ARTICLES_NAME);
        if($res){
            return $res[0][0];
        }else{
            return false;
        }
    }
	/**
	 * 得到文章数
	 * @return int
	 */
	function getTotalNum(){
		$sSql="select count(*) from ".DATABASE_ARTICLES_NAME;
		$nTotalNumber=$this->db->singleQuery($sSql);
		return $nTotalNumber;
	}

}
