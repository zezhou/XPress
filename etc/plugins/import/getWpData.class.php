<?php 
/**
 * wordpress 到 xpress的转换程序
 * [数据导入]本类功能为获取数据时做处理（"\r"->'\r',"\n"->'\n'），并生成SQL语句
 * 接下来的功能1：导入数据时进行和发布文章一样的处理，，然后加上特定的规则，最后输入数据库
 * 接下来的功能2：输入数据库后，重新生成所有数据文件。
 * 调用本类后生成的数据保存在变量$this->sqlData中。未测试在大数据情况下是否有问题。
 *
 */
class getWpData{
	
	var $sqlData;#存放调用本类后生成的数据
	#get wp data
	/**
	 * 连接数据库，获取数据，对数据进行处理，生成SQL语句
	 * @param $host 数据库地址
	 * @param $username 用户名
	 * @param $password 密码
	 * @param $wpTableName 数据库名
	 * @return none
	 */
	function getWpData($host="localhost",$username="root",$password="",$wpTableName="wordpress"){
		
		#连接数据库
		$db=mysql_connect($host,$username,$password);
		mysql_select_db($wpTableName,$db);
				
		#读取需要的数据
		
		#读取post2cat表
		$post2cat_sql="SELECT * FROM `wp_post2cat`";
		$post2cat_data=mysql_query($post2cat_sql,$db);
		$post2cat_data_array=array();
		
		while($post2cat_result=mysql_fetch_array($post2cat_data)){
			$post2cat_data_array[$post2cat_result['post_id']]=$post2cat_result['category_id'];
		}
		
		#获取目录树据,如果目录存在，跳过，不存在，创建
		$category_sql="SELECT * FROM `wp_categories`";
		$category_data=mysql_query($category_sql,$db);
		
		#对数据进行处理
		/*
		 * cat_ID 	cat_name 	category_nicename 	category_description 	category_parent 	category_count
		 */
		
		/*
		 * category_ID INTEGER PRIMARY KEY NOT NULL ,category_name TEXT NOT NULL ,category_description TEXT NOT NULL ,category_count INTEGER
		 */
		
		$category_sql_data=array();
		$category_name_array=array();
		$category_sql_data[]="CREATE TABLE ".DATABASE_CATEGORIES_NAME." (category_ID INTEGER PRIMARY KEY NOT NULL ,category_name TEXT NOT NULL ,category_description TEXT NOT NULL ,category_count INTEGER);\n";
		
		while($category_result=mysql_fetch_array($category_data)){
			$category_name=$this->replace($category_result['cat_name']);
			$category_name_array[]=$category_name;
			$category_description=$this->replace($category_result['category_description']);
			$category_count=$this->replace($category_result['category_count']);
			$category_sql_data[]="insert into ".DATABASE_CATEGORIES_NAME." ('category_name','category_description','category_count') values ('".$category_name."','".$category_description."','".$category_count."');\n";
		}
		
		#获取文章数据
		$article_sql="select * from `wp_posts`";
		$article_data=mysql_query($article_sql,$db);
		$article_sql_data=array();
		
		#对数据进行处理
	
		$article_sql_data[]="CREATE TABLE ".DATABASE_ARTICLES_NAME." (id INTEGER PRIMARY KEY NOT NULL ,title TEXT NOT NULL ,content TEXT NOT NULL ,category TEXT,tags TEXT,date TEXT ,author TEXT,url TEXT);\n";
		while($article_result=mysql_fetch_array($article_data)){
			if($article_result['post_status']!=='publish') continue;
			$content=$this->replace($article_result['post_content']);
			$title=$this->replace($article_result['post_title']);
			$date=strtotime($this->replace($article_result['post_date']));
			$category=$post2cat_data_array[$article_result['ID']];
			$author=$article_result['post_author'];
			
			#@todo $category?
			$url=$category_name_array[$category-1]."/".$title.ARTICLE_SUFFIX;
			$tags=$this->replace($article_result['post_title']);
			$article_sql_data[]="insert into ".DATABASE_ARTICLES_NAME." ('title','content','category','tags','date','author','url') values ('".$title."','".$content."','".$category."','".$tags."','".$date."','".$author."','".$url."');\n";
		}
		

		
		#获取管理员数据
		#生成数据$this->sqlData,文件的生成留给调用本类的程序来处理
		$this->sqlData=implode($article_sql_data).implode($category_sql_data);
		
	}
	
	function replace($data){
		$data=str_replace('\'', '\'\'',str_replace("\n",'\n',str_replace("\r",'\r',$data)));
		return $data;
	}
}
