<?php
require("./common/config.php");
//------------------------------------------------------------
//验证部分
session_start();

//读取ID
$name=$_POST['name'];

if($name) {
    $result = $db->query("SELECT id,password FROM $user_table WHERE id='$name';");
    if($result -> num_rows) {
        $array = $result -> fetch_array();
	    $password = $array['password'];
	    $userexist = TRUE;
    }
}

//if($userexist && isset($_POST['password']) && password_verify($_POST['password'],explode("\n",file_get_contents("./data/".$_POST['name']."/config.txt"))[0])){
if($userexist && isset($_POST['password']) && $_POST['password'] == $password) {
	$_SESSION["user"] = $name;
	//header('location:?');//禁止跳转,否则无法登录
} else {
	echo '
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable="no""/>
	<link href="//apps.bdimg.com/libs/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <script src="//code.jquery.com/jquery.js"></script>
    <script src="//apps.bdimg.com/libs/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function(){
        $(".inputbox").focus(function(){
                $(".inputdiv").attr("class","form-group");
			    $(".inputhelp").remove();
        });
    });
    </script>
	</head>
	<body>
	<div class="container" style="max-width:400;margin: 0 auto;position: relative;top: 50%;margin-top: -150px;">
	<div class="panel panel-info text-center">
	<div class="panel-heading">用户登录
	</div>
	
	<div class="panel-body">
	<form method="post">
	    ';
	if(isset($_POST['password']))
	{
		echo '<div class="form-group has-error has-feedback inputdiv">
			          <input class="form-control has-error inputbox" placeholder="User" type="text" name="name" value="'.$name.'"/>
			          <span class="glyphicon glyphicon-remove form-control-feedback inputhelp" aria-hidden="true"></span>
			      </div>
			      <div class="form-group has-error has-feedback inputdiv">
			          <input class="form-control has-error inputbox" placeholder="Password" type="password" name="password" />
			          <span class="glyphicon glyphicon-remove form-control-feedback inputhelp" aria-hidden="true"></span>
			          <span class="help-block inputhelp">密码或ID错误</span>
	              </div>';
	} else {
		echo '<div class="form-group" id="inputdiv">
			          <input class="form-control inputbox" id="inputbox1" placeholder="ID" type="text" name="name"  value="'.$name.'"/>
				  </div>
			      <div class="form-group" id="inputdiv" id="inputdiv">
			          <input class="form-control inputbox" id="inputbox2" placeholder="Password" type="password" name="password" />
	              </div>';
	}
	exit('
	<input class="btn btn-info" type="submit" value="&nbsp;&nbsp;&nbsp;登陆&nbsp;&nbsp;&nbsp;" style="margin-top:20px"/>
	</form>
	</div>
	</div>
	</div>
	</body>
</html>
	');
	
}
//------------------------------------------------------------
?>

<html>
<script language="javascript">
location.href="./index.php"
</script>
</html>