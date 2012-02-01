<?php
/**
 * edit.php -  the class which post and modify article
 * 流程如下:
 * 接受数据 -> 处理数据 ->读入文章模板 -> 生成文章 -> 读入首页模板 -> 更新首页 -> 更新其它信息  
 */
if(!defined("INCLUDE_CORE")) die("Permission denied.");
require_once(ADMIN_LIB_PATH."db.php");
require_once("data.php");
/**
 * 该函数用来发布和编辑文章
 */
class Edit extends Data{
	 // 数据库连接
	var $db;
    var $data=null;
	function Edit(){
		$this->db=new DB();
	}
	/**
	 * 接收发送来的数据 ，插入数据库。
	 * 步骤为：先得到数据，然后校验数据，然后将数据插入数据库。
	 * @param	Object 传递的参数及数据
	 * @return	Boolen
	 */
	function post($data){
        $this->data=$this->getData($data);
		$this->data=$this->check($this->data,"input");
        $insData=array(
            'title'=>$this->data['title'],
            'content'=>$this->data['content'],
            'tags'=>$this->data['tags'],
            'time'=>$this->data['time'],
            'author'=>$this->data['author'],
            'url'=>$this->data['url']
        );
 		return $this->insertData($insData);
	}
	
    /**
     * get article url
     */
    function getArticleUrl($data){
        return ARTICLE_PATH.$data['date']."/".$data['title'].ARTICLE_SUFFIX;
    }
	/**
	 * 载入数据库外的其它数据
	 * @param array $data 需要被处理的数据
	 * @return Array
	 */
	function getData($data){
        if(!isset($data['author'])){
            if (isset($_SESSION['nickname'])){
                $data['author']=$_SESSION['nickname'];
            }elseif(isset($_SESSION['username'])){
                $data['author']=$_SESSION['username'];
            }else{
                $data['author']="anonymous";
            }
        }
        
        if (!isset($data['time'])){
            $data['time']=time();
        }
        if (!isset($data['date'])){
            $data['date']=@date("Ymd",$data['time']);
        }
        if (!isset($data['cndate'])){
            $data['cndate']=@date("Y年m月d日",$data['time']);
        }
        if (!isset($data['url'])){
            $data['url']=$this->getArticleUrl($data);
        }
        return $data;
	}

	function insertData($data){
		$sSqlKeys="";
		$sSqlValues="";
		foreach ($data as $key=>$value){
			$sSqlKeys.="'$key',";
			$sSqlValues.="'$value',";
		}
		$sSqlKeys=substr($sSqlKeys,0,-1);
		$sSqlValues=substr($sSqlValues,0,-1);
        if($this->db->insert($sSqlKeys,$sSqlValues)){
            return $this->db->getMaxId();
        }else{
            return false;
        }
    }

    function updateData($data,$id){
        $expr="";
        foreach($data as $key => $val){
            $expr.=" ".$key."='".$val."',";
        }
        $expr=trim($expr,",");
        $sql="update ".DATABASE_ARTICLES_NAME." set ".$expr." where id=".$id;
        $res=$this->db->query($sql);
        if($res){
            return true;
        }else{
            return false;
        }
        
    }

	/**
	 * 删除文章
	 * @param  $nId  要删除的文章id号
	 * @return Boolean 
	 */	
	function delete($data){
        $aId=explode(",",$data['id']);
		$return=true;
		if(is_array($aId)){
			foreach($aId as $key=>$val){
				$return=$this->deleteOneArticle($val)&&$return;
			}
		}else{
			return false;
		}
        if($return){
            require_once(ADMIN_LIB_PATH."publish.php");
            $oPub=new Publish();
            $return=$oPub->update();
        }
	    return $return;
	}

	/**
	 * 删除一篇文章
	 * @param  $nId  要删除的文章id号
	 * @return Boolean 
	 */
	function deleteOneArticle($nId){
		#删除sql数据库的文章记录
		$sql="delete from ".DATABASE_ARTICLES_NAME." where id=".$nId;
		$return=$this->db->query($sql);
        return $return;
	}
    function update($data){
        $success=true;
        $this->data=$this->getData($data);
		$this->data=$this->check($this->data,"input");
        $updateData=array(
            'title'=>$this->data['title'],
            'content'=>$this->data['content'],
            'tags'=>$this->data['tags'],
            'time'=>$this->data['time'],
            'author'=>$this->data['author'],
            'url'=>$this->data['url']
        );
 		return $this->updateData($updateData,$this->data['id']);
    }
}
