<?php

$old_pw = $_POST['old_pw'];
$new_pw = $_POST['new_pw'];

if($_POST['old_pw'] || $_POST['new_pw']) {
    $result = $db -> query("SELECT password FROM $user_table WHERE id='". $_SESSION['user'] ."'");
    $now_pw = $result -> fetch_array()["password"];

    if($old_pw == $now_pw) {
        $db -> query("UPDATE $user_table SET password='$new_pw' WHERE id='". $_SESSION['user'] ."'");
        show_text("操作成功!<br><br><a class='btn btn-info' href='?'>返回</a>", "?");
        $_SESSION['user'] = '';
        exit();
    } else {
        jump_with_text("旧密码输入错误!", "?action=chpasswd");
    }
}
?>

<div class='panel-heading'>
    <a href='?'>修改密码</a>
</div>
<div class='panel-body' style="max-width:400px; margin: 0 auto;">
    <div style='max-width:400px; margin: 0 auto;'>
        <form method='post' action='?action=chpasswd'>
            <div class='form-group' id='inputdiv'>
                <label class="control-label" for="old_pw">旧密码</label>
                <input class='form-control inputbox' id='old_pw' placeholder='Old Password' type='password' name='old_pw' />
            </div>
            <div class='form-group' id='inputdiv'>
                <label class="control-label" for="new_pw">新密码</label>
                <input class='form-control inputbox' id='new_pw' placeholder='New Password' type='text' name='new_pw' autocomplete="off" />
            </div>
            <input class='btn btn-danger' type='submit' value='&nbsp;&nbsp;&nbsp;确认&nbsp;&nbsp;&nbsp;'>
        </form>
    </div>
</div>