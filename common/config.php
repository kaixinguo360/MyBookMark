<?php

# Set MySQL Server
define("DB_HOST", "localhost");
define("DB_USER", "test");
define("DB_PASS", "1234567");
define("DB_NAME", "test");
define("TB_PREFIX", "test_");

# Connect To Database
$db = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
if(mysqli_connect_error()){
echo 'ERROR: ';
echo mysqli_connect_error();
exit;
}

# Check Database
$user_table = TB_PREFIX . "user";
$result = $db -> query("SHOW TABLES LIKE '$user_table'");
if(!$result -> num_rows){
    $db -> query("CREATE TABLE $user_table (id CHAR(16) PRIMARY KEY, password TEXT, time datetime NOT NULL DEFAULT CURRENT_TIMESTAMP);");
    $db -> query("insert into $user_table (id, password) values ('admin', '1234567');");
}
unset($result);
