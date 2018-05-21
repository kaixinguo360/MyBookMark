<?php

# Get Data
$id = $_GET['id'];
$verified = $_GET['verified'];

# Delete Data
if($id && $verified == "true") {
    $result = $db -> query("DELETE FROM $data_table WHERE id='$id';");
    $deleted = TRUE;
} else {
    $result = $db -> query("SELECT id,info,url FROM $data_table WHERE id='$id';");
	$array = $result -> fetch_array();
	$url = $array['url'];
	$info = $array['info'];
}

?>

<style>
.grid-item {
    margin-bottom: 8px;
    box-shadow: 2px 4px 6px #888888;
}
.grid-item-info {
    max-height:200px;
    margin:8px 0 0 0;
    padding:0 8px 8px 8px;
    word-wrap:break-word;
    overflow:hidden;
    text-overflow:ellipsis;
}
</style>

<div class="panel-heading">
	删除图片
</div>
<div class="panel-body" style="max-width:400px; margin: 0 auto;">
    <?php 
        if($deleted) {
            $status = $result ? "成功" : "失败<br>".mysqli_error($db);
            echo "
                <div class='text-center' style='margin-top:10px;'>
                    <p>更新${status}！</p>
                </div>
                <script>
                    location.href='?';
                </script>
                ";
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
