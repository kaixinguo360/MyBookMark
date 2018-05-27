<?php
session_start();

if(isset($_GET['logout'])) {
    $_SESSION['user'] = "";
}
if($_SESSION['user'] == ROOT_USER){
    return;
} else {
    jump_to("../");
}
