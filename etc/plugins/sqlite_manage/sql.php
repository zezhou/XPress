<form action="sql.php" method="post">
<input type=text name=database value="<?php echo $_POST['database']?>"><br>
<textarea name=sql rows=10 cols="50"><?php echo stripslashes($_POST['sql'])?></textarea><br>
<input type=submit value=submit>
</form>

<div style="background:yellow;clor:green">
<?php
if(!empty($_POST)){
	$db=new SQLiteDatabase($_POST['database']);
	$result=$db->arrayQuery(stripslashes($_POST['sql']));
	var_dump($result);
}
?>
</div>
