<?php

session_start();
if($_SESSION["user"]==""){
	//为空则返回登录页面
	jump_to("./login.php");
} else {
	//验证ID是否存在
	$user = $_SESSION["user"];
	$result = $db->query("SELECT id FROM $user_table WHERE id='$user';");
	if(!$result -> num_rows)
	{
		//不存在则显示ID不存在
	    jump_to("./login.php");
	}
}

//ID正确则开启SESSION并检查用户数据库存在性
$data_table = TB_PREFIX . "data_" . $user;
$tag_table = TB_PREFIX . "tag_" . $user;
$map_table = TB_PREFIX . "map_" . $user;
$result = $db -> query("SHOW TABLES LIKE '$data_table';");
if(!$result -> num_rows){
    $db -> query("CREATE TABLE $data_table (id CHAR(32) PRIMARY KEY, url TEXT NOT NULL, info TEXT, time datetime NOT NULL DEFAULT CURRENT_TIMESTAMP);");
}
$result = $db -> query("SHOW TABLES LIKE '$tag_table';");
if(!$result -> num_rows){
    $db -> query("CREATE TABLE $tag_table (id INT(3) AUTO_INCREMENT PRIMARY KEY, name CHAR(64) UNIQUE);");
}
$result = $db -> query("SHOW TABLES LIKE '$map_table';");
if(!$result -> num_rows){
    $db -> query("CREATE TABLE $map_table (data_id CHAR(32), tag_id INT(3), PRIMARY KEY (data_id, tag_id));");
}
unset($result);
