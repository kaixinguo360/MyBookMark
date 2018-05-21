<?php

session_start();
if($_SESSION["user"]==""){
	//为空则返回登录页面
	exit('
        <html>
	        <script language="javascript">
		        location.href="./login.php"
	        </script>
        <html>
		');
} else {
	//验证ID是否存在
	$user = $_SESSION["user"];
	$result = $db->query("SELECT id FROM $user_table WHERE id='$user';");
	if(!$result -> num_rows)
	{
		//不存在则显示ID不存在
		exit('
	<html>
	        <script language="javascript">
		        location.href="./login.php"
	        </script>
        <html>
	    ');
	}
}

//ID正确则开启SESSION并检查用户数据库存在性
$data_table = TB_PREFIX . "data_" . $user;
$result = $db -> query("SHOW TABLES LIKE '$data_table';");
if(!$result -> num_rows){
    $db -> query("CREATE TABLE $data_table (id CHAR(32) PRIMARY KEY, url TEXT NOT NULL, info TEXT, time datetime NOT NULL DEFAULT CURRENT_TIMESTAMP);");
}
unset($result);
