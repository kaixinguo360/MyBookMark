<?php

# Set MySQL Server
define("DB_HOST", "localhost");
define("DB_USER", "test");
define("DB_PASS", "1234567");
define("DB_NAME", "test");
define("TB_PREFIX", "test_");
define("ROOT_USER", "root");

# Set Models
$models_add = array("pixiv");
$models_img = array("img_search");

# Set Img Storage
$img_storage = "";

# Connect To Database
$db = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
if(mysqli_connect_error()){
echo 'ERROR: ';
echo mysqli_connect_error();
exit;
}

# Check Database
$user_table = TB_PREFIX . "user";
$api_table = TB_PREFIX . "api";
$result = $db -> query("SHOW TABLES LIKE '$user_table'");
if(!$result -> num_rows){
    $db -> query("CREATE TABLE $user_table (id CHAR(16) PRIMARY KEY, password TEXT, time datetime NOT NULL DEFAULT CURRENT_TIMESTAMP);");
    $db -> query("insert into $user_table (id, password) values ('". ROOT_USER ."', PASSWORD('1234567'));");
}
$result = $db -> query("SHOW TABLES LIKE '$api_table'");
if(!$result -> num_rows){
    $db -> query("CREATE TABLE $api_table (api_id CHAR(16) PRIMARY KEY, user CHAR(16), password TEXT, time datetime NOT NULL DEFAULT CURRENT_TIMESTAMP);");
}
unset($result);
