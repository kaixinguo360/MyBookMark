<?php

require("../common/config.php");
require("./functions.php");
require("./verify.php");

require("./head.php");
$action = $_GET["action"];
$action = $action ? $action : "admin";
require("./page/". $action .".php");
require("../common/foot.php");