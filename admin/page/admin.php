<?php

# Get Data
$todo = $_GET['todo'];
$user = $_GET['user'];
$passwd = $_GET['passwd'];
$verified = $_GET['verified'];

# Check Data
if($todo == "create") {
    # Add to Database
	if($user) {
	    $result = $db -> query("insert into $user_table (id, password) values ('$user', PASSWORD('$passwd'));");
	}
} else if($todo == "remove") {
    # Remove From Database
    if(isset($_GET['verified'])) {
        if($verified == $user) {
            $result = $db -> query("DELETE FROM $user_table WHERE id='$user';");
        } else {
            $error = "输入用户名不一致, 删除操作终止!";
        }
    } else {
        echo "
        <div class='panel-heading'>
            <a href='?'>确认删除</a>
        </div>
        <div class='panel-body'>
            <div style='max-width:400px; margin: 0 auto;'>
                <p><b>请再次输入你要删除的用户名</b></p>
                <form>
                    <input hidden=true name='todo' value='remove' />
                    <input hidden=true name='user' value='$user' />
                    <div class='form-group' id='inputdiv'>
                        <input class='form-control inputbox' placeholder='User To Delete' type='text' name='verified' />
                    </div>
                    <input class='btn btn-danger' type='submit' value='&nbsp;&nbsp;&nbsp;确认&nbsp;&nbsp;&nbsp;'>
                </form>
            </div>
        </div>
        ";
        return;
    }
} else if($todo == "reset") {
    # Update Database
	if($user) {
	    $result = $db -> query("UPDATE $user_table SET password='' WHERE id='$user'");
	}
} else if($todo == "login") {
	if($user) {
	    $_SESSION['user'] = $user;
	    jump_to("../");
	}
} else {
    $result = $db -> query("SELECT * FROM $user_table;");
    $list = "<table class='table'>";
    $list .= "<tr class='active'>";
    $list .= "<th>用户名</th>";
    $list .= "<th>密码</th>";
    $list .= "<th>日期</th>";
    $list .= "<th style='min-width:3em'>操作</th>";
    $list .= "</tr>";
    for ($i = 0; $i < $result -> num_rows; $i++) {
    	$array = $result -> fetch_array();
    	$user = $array['id'];
    	$passwd = $array['password'] ? "<a href='?todo=reset&user=$user'>重置</a>" : "<a href='?todo=login&user=$user'>未设置</a>";
    	$time = $array['time'];
    	if($user == ROOT_USER) {
    	    continue;
    	}
        $list .= "<tr>";
        $list .= "<td>$user</td>";
        $list .= "<td>$passwd</td>";
        $list .= "<td>$time</td>";
        $list .= "<td><a href='?action=admin&todo=remove&user=$user'>删除</a></td>";
        $list .= "</tr>";
    }
    $list .= "</table>";
}

?>

<div class="panel-heading">
	<a href="?">用户管理</a>
</div>
<div class="panel-body" style="max-width:400px; margin: 0 auto;">
    <?php 
        if($todo) {
            $status = $result ? "成功" : "失败<br>".mysqli_error($db);
            
            #Display Data
            echo "
            <div>
                <p><b>操作$status</b></p>
            </div>
            ";
            if($error) {
                echo "<div><p>$error</p></div>";
            }
            echo '
            <div style="margin-bottom:10px;">
                <a class="btn btn-info" href="./index.php">&nbsp;&nbsp;&nbsp;返回&nbsp;&nbsp;&nbsp;</a>
            </div>
            ';
        } else {
            echo "
<div class='table-responsive'>
    $list
</div>
<div>
    <div><b>添加用户</b></div>
    <form method='get'>
        <input name='action' value='admin' hidden=true/>
        <input name='todo' value='create' hidden=true/>
        <div class='form-group' id='inputdiv'>
            <input class='form-control inputbox' placeholder='User Name' type='text' name='user' />
        </div>
        <div class='form-group' id='inputdiv'>
            <input class='form-control inputbox' placeholder='Password' type='text' name='passwd' autocomplete='off'/>
        </div>
    	<input class='btn btn-info' type='submit' value='&nbsp;&nbsp;&nbsp;添加&nbsp;&nbsp;&nbsp;'>
    </form>
</div>
	";}
    ?>
</div>
