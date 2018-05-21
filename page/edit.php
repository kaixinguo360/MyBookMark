<?php

# Get Data
$info = $_GET['info'];
$url = $_GET['url'];
$id = $_GET['id'];

# Check Data
if(isset($_GET['info'])) {
    # Add Data To Database
    $result = $db -> query("UPDATE $data_table SET info='$info' WHERE id='$id';");
    $updated = TRUE;
} else {
    $result = $db -> query("SELECT info,url FROM $data_table WHERE id='$id';");
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
	编辑图片
</div>
<div class="panel-body" style="max-width:400px; margin: 0 auto;">
    <?php 
        if($updated) {
            $status = $result ? "成功" : "失败<br>".mysqli_error($db);
            echo "
                <div class='text-center' style='margin-top:10px;'>
                    <p>更新${status}！</p>
                </div>
                <script>
                    location.href='?action=img&id=$id';
                </script>
                ";
        } else {
            echo "
    <br>
    <form method='get'>
        <input name='action' value='edit' hidden=true/>
        <input name='id' value='$id' hidden=true/>
        <input name='url' value='$url' hidden=true/>
        <pre>$url</pre>
        <div class='form-group' id='inputdiv'>
            <textarea class='form-control inputbox' id='info' placeholder='Info' type='text' name='info' style='height:40%;'>$info</textarea>
        </div>
		<input class='btn btn-info' type='submit' value='&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;'>
	</form>
	";}
    ?>
</div>
