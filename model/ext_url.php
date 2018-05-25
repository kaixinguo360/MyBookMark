<?php
$ext_url_table = TB_PREFIX . "ext_url_" . $user;
check_table($ext_url_table, "CREATE TABLE $ext_url_table (id CHAR(32), url TEXT, PRIMARY KEY(id));");

if($_GET['action'] == "img") {
	$result = $db -> query("SELECT url FROM $ext_url_table WHERE id='$id';");
    if($result -> num_rows) {
        $ext_url = $result -> fetch_array()['url'];
	}
	if($ext_url) {
	echo "
<div class='grid-item'>
	<div style='margin:10px'>
		<h4><a href='$ext_url'>附加链接</a></h4>
		<a href='?mod=ext_url&id=$id'>编辑</a>
	</div>
</div>
    ";
} else {
	echo "
<div class='grid-item'>
	<div style='margin:10px'>
		<a href='?mod=ext_url&id=$id'>添加链接</a>
	</div>
</div>
    ";
}
    return;
}

# Get Data
$url = $_GET['url'];
$id = $_GET['id'];

# Check Data
if(isset($_GET['url'])) {
	$db -> query("DELETE FROM $ext_url_table WHERE id='$id';");
    $result = $db -> query("INSERT INTO $ext_url_table (id, url) VALUES ('$id', '$url');");
    $updated = $result;
} else {
    # Get Ext Url Of Img
    $result = $db -> query("SELECT url FROM $ext_url_table WHERE id='$id';");
    if($result -> num_rows) {
        $url = $result -> fetch_array()['url'];
	}
}

?>

<div class="panel-heading">
	<a href="?">编辑附加链接</a>
</div>
<div class="panel-body" style="max-width:400px; margin: 0 auto;">
    <?php 
        if(isset($_GET['url'])) {
            $status = $updated ? "成功" : "失败<br>".mysqli_error($db);
            jump_with_text("更新${status}！", "?action=img&id=$id");
        } else {
            echo "
    <br>
    <form method='get' action='?' >
        <input name='mod' value='ext_url' hidden=true/>
        <input name='id' value='$id' hidden=true/>
        <div class='form-group' id='inputdiv'>
            <textarea class='form-control inputbox' placeholder='Ext URL' type='text' name='url' style='height:40%;'>$url</textarea>
        </div>
		<input class='btn btn-info' type='submit' value='&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;'/>
	</form>
	";}
    ?>
</div>
