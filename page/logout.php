<?php

# Clean SESSION
$_SESSION["user"] = "";

# Clean Cookies
setcookie("sort", NULL, time() - 100);
setcookie("loadingimg", NULL, time() - 100);
setcookie("updated", NULL, time() - 100);

jump_to("./login.php");

