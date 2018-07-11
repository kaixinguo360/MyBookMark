<?php

$sort = $_POST['sort'];
$loadingimg = $_POST['loadingimg'];
$nsfw = $_POST['nsfw'];

if($_POST['is_update']) {
    if($sort=='ASC' || $sort=='DESC' || $sort=='RAND') {
        run_sql("DELETE FROM $setting_table WHERE name='sort';");
        run_sql("INSERT INTO $setting_table (name, value) VALUES ('sort', '$sort');");
    }
    run_sql("DELETE FROM $setting_table WHERE name='loadingimg';");
    run_sql("INSERT INTO $setting_table (name, value) VALUES ('loadingimg', '$loadingimg');");
    
    run_sql("DELETE FROM $setting_table WHERE name='nsfw';");
    run_sql("INSERT INTO $setting_table (name, value) VALUES ('nsfw', '$nsfw');");
    
    setcookie("updated", NULL, time() - 100);
    jump_with_text('设置成功', '?');
}

?>

<div class="panel-heading">
	<a href="?">列表设置</a>
</div>
<div class="panel-body" style="max-width:400px; margin: 0 auto;">
    <form method='post'>
        <div style='margin:10px'><b>排序方式</b></div>
        <input hidden=true name='is_update' value='true'/>
        <select class='form-control' name='sort'>
            <option value='DESC' <?php if($_COOKIE['sort']=='DESC') echo"selected='selected'";?>>最新在前</option>
            <option value='ASC' <?php if($_COOKIE['sort']=='ASC') echo"selected='selected'";?>>最旧在前</option>
            <option value='RAND' <?php if($_COOKIE['sort']=='RAND') echo"selected='selected'";?>>随机</option>
        </select>
        <br>
        <select class='form-control' name='nsfw'>
            <option value='0' <?php if($_COOKIE['nsfw']=='0') echo"selected='selected'";?>>Hide NSFW</option>
            <option value='1' <?php if($_COOKIE['nsfw']=='1') echo"selected='selected'";?>>Show NSFW</option>
        </select>
        <br>
        <div style='margin:10px'><b>加载占位图片</b></div>
        <input class='form-control' type=text name='loadingimg' value='<?php echo $_COOKIE['loadingimg'];?>'/>
		<br>
		<input class='btn btn-info' type='submit' value='&nbsp;&nbsp;&nbsp;保存&nbsp;&nbsp;&nbsp;'>
	</form>
</div>
