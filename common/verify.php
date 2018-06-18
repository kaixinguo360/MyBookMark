<?php

session_start();
if(isset($_GET["api"])) {
    $api_id = $_GET["api"];
    // 查询api是否存在
    $result = $db->query("SELECT user, password FROM $api_table WHERE api_id='$api_id';");
    if(!$result -> num_rows) {
    	//不存在则显示ID不存在
        jump_to("./login.php");
    }
    $array = $result -> fetch_array();
    $user = $array['user'];
    $password = $array['password'];
    if($password != "" && $password != $_GET["pw"]) {
    	//密码不匹配则退出
        jump_to("./login.php");
    }
    $user_api_table = TB_PREFIX . "api_" . $user;
    $result = $db -> query("SELECT action FROM $user_api_table WHERE api_id='$api_id';");
    for ($i = 0; $i < $result -> num_rows; $i++) {
    	if($_GET["action"] == $result -> fetch_array()['action']) {
    	    $isGranted = TRUE;
    	}
    }
    if(!$isGranted) {
    	//未被授权则退出
        jump_to("./login.php");
    }
} else {
    if($_SESSION["user"]==""){
    	//为空则返回登录页面
    	jump_to("./login.php");
    } else {
    	//验证ID是否存在
    	$user = $_SESSION["user"];
    	$result = $db->query("SELECT id FROM $user_table WHERE id='$user';");
    	if(!$result -> num_rows){
    		//不存在则显示ID不存在
    	    jump_to("./login.php");
    	}
    	if($user == ROOT_USER) {
    		//为ROOT用户则跳转到管理页面
    	    jump_to("./admin/");
    	}
    }
}

//ID正确则开启SESSION并检查用户数据库存在性
$data_table = TB_PREFIX . "data_" . $user;
$tag_table = TB_PREFIX . "tag_" . $user;
$map_table = TB_PREFIX . "map_" . $user;
$album_table = TB_PREFIX . "album_" . $user;
$user_api_table = TB_PREFIX . "api_" . $user;
$setting_table = TB_PREFIX . "setting_" . $user;

check_table($data_table, "CREATE TABLE $data_table (id CHAR(32) PRIMARY KEY, url TEXT, source TEXT, info TEXT, time datetime NOT NULL DEFAULT CURRENT_TIMESTAMP);");
check_table($tag_table, "CREATE TABLE $tag_table (id INT(3) AUTO_INCREMENT PRIMARY KEY, name CHAR(64) UNIQUE, info TEXT);");
check_table($map_table, "CREATE TABLE $map_table (data_id CHAR(32), tag_id INT(3), PRIMARY KEY (data_id, tag_id));");
check_table($album_table, "CREATE TABLE $album_table (id INT(3) AUTO_INCREMENT PRIMARY KEY, name CHAR(64) UNIQUE, info TEXT, tags TEXT, except TEXT);");
check_table($user_api_table, "CREATE TABLE $user_api_table (api_id CHAR(16), action CHAR(16), PRIMARY KEY(api_id, action));");
check_table($setting_table, "CREATE TABLE $setting_table (name VARCHAR(16) PRIMARY KEY, value VARCHAR(255));");

unset($result);
