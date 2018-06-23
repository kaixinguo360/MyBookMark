<?php

require("./common/config.php");
require("./common/functions.php");
require("./common/verify.php");

if($_GET['mod']) {
	require("./model/".$_GET['mod'].'.php');
} else {
	$type = $_GET['type'] ? $_GET['type'] : 'list';
	$title = $action;
	require("./data/".$type.'.php');
}

exit;