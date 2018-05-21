<?php

require("./common/config.php");
require("./common/functions.php");
require("./common/verify.php");
require("./common/router.php");
require("./common/head.php");
require("./page/".$action.'.php');
require("./common/foot.php");
exit;
