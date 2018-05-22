<?php

# Get Data
$id = $_GET['id'];
$verified = $_GET['verified'];

# Delete Data
if($id && $verified == "true") {
    $result = $db -> query("DELETE FROM $data_table WHERE id='$id';");
    $deleted = $result;
    if($deleted) {
        $result = $db -> query("DELETE FROM $map_table WHERE data_id='$id';");
    }
} else {
    $result = $db -> query("SELECT id,info,url FROM $data_table WHERE id='$id';");
	$array = $result -> fetch_array();
	$url = $array['url'];
	$info = $array['info'];
}

?>

<div class="panel-heading">
	<a href="?">删除图片</a>
</div>
<div class="panel-body" style="max-width:400px; margin: 0 auto;">
    <?php 
        if($id && $verified == "true") {
            $status = $deleted ? "成功" : "失败<br>".mysqli_error($db);
            show_text("删除${status}！");
            jump_to("?");
        } else {
            echo "
    <br>
    <form method='get'>
        <input name='action' value='delete' hidden=true/>
        <input name='id' value='$id' hidden=true/>
        <input name='verified' value='true' hidden=true/>
        <h2>您确认要删除此图片吗?</h2>
        <div class='grid-item'>
                <img width=100% src='$url' alt='$info' />
            
                <div class='grid-item-info'>
                <p>$info</p>
                </div>
        </div>
		<input class='btn btn-danger' type='submit' value='&nbsp;&nbsp;&nbsp;确认&nbsp;&nbsp;&nbsp;'>
	</form>
	";}
    ?>
</div>
