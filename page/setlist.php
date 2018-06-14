<?php

$sort = $_POST['sort'];
$loadingimg = $_POST['loadingimg'];

if($_POST['is_update']) {
    if($sort=='ASC' || $sort=='DESC' || $sort=='RAND') {
        setcookie("sort_mode", $sort, time()+60*60*24*30);
    }
    setcookie("loadingimg", $loadingimg, time()+60*60*24*30);
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
            <option value='DESC' <?php if($_COOKIE['sort_mode']=='DESC') echo"selected='selected'";?>>最新在前</option>
            <option value='ASC' <?php if($_COOKIE['sort_mode']=='ASC') echo"selected='selected'";?>>最旧在前</option>
            <option value='RAND' <?php if($_COOKIE['sort_mode']=='RAND') echo"selected='selected'";?>>随机</option>
        </select>
        <br>
        <div style='margin:10px'><b>加载占位图片</b></div>
        <input class='form-control' type=text name='loadingimg' value='<?php echo $_COOKIE['loadingimg'];?>'/>
		<br>
		<input class='btn btn-info' type='submit' value='&nbsp;&nbsp;&nbsp;保存&nbsp;&nbsp;&nbsp;'>
	</form>
</div>
