<?php

# Get Tag
$album = $_GET['album'];
$verified = $_GET['verified'];

# Delete Data
if($album && $verified == "true") {
    $result = $db -> query("SELECT id FROM $album_table WHERE name='$album';");
	$album_id = $result -> fetch_array()['id'];
	
    $result = $db -> query("DELETE FROM $album_table WHERE id='$album_id';");
    $deleted = $result;
    if($deleted) {
        $result = $db -> query("DELETE FROM $amap_table WHERE album_id='$album_id';");
    }
}

?>

<div class="panel-heading">
	<a href="?">删除标签</a>
</div>
<div class="panel-body" style="max-width:400px; margin: 0 auto;">
    <?php 
        if($album && $verified == "true") {
            $status = $deleted ? "成功" : "失败<br>".mysqli_error($db);
            show_text("删除${status}！");
            jump_to("?");
        } else {
            echo "
    <br>
    <form method='get'>
        <input name='action' value='deletealbum' hidden=true/>
        <input name='album' value='$album' hidden=true/>
        <input name='verified' value='true' hidden=true/>
        <h2>您确认要删除${album}图集吗?</h2>
		<input class='btn btn-danger' type='submit' value='&nbsp;&nbsp;&nbsp;确认&nbsp;&nbsp;&nbsp;'>
	</form>
	";}
    ?>
</div>
