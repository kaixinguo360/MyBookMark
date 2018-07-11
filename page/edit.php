<?php

# Get Data
$tags = $_GET['tags'];
$info = $_GET['info'];
$url = $_GET['url'];
$source = $_GET['source'];
$id = $_GET['id'];
$nsfw = $_COOKIE['nsfw'] ? $_COOKIE['nsfw'] : 0;

# Check Data
if(isset($_GET['info'])) {
	$tags = explode(",", $tags);
    $updated = update_item($id, $url, $info, $tags, $source);
} else {
    # Get All Tags
    $result = $db -> query("SELECT name FROM $tag_table WHERE nsfw <= $nsfw;");
    for ($i = 0; $i < $result -> num_rows; $i++) {
    	$tags[$result -> fetch_array()['name']] = "";
    }

    # Get Tags Of Img
    $result = $db -> query("SELECT info,url,source FROM $data_table WHERE id='$id';");
	$array = $result -> fetch_array();
	$url = $array['url'];
	$info = $array['info'];
	$source = $array['source'];
	
	$result = $db -> query("SELECT $tag_table.name FROM $map_table,$tag_table WHERE $map_table.tag_id=$tag_table.id AND $map_table.data_id='$id';");
	for ($i = 0; $i < $result -> num_rows; $i++) {
		$tags[$result -> fetch_array()['name']] = "true";
	}
}

?>

<div class="panel-heading">
	<a href="?">编辑图片</a>
</div>
<div class="panel-body" style="max-width:400px; margin: 0 auto;">
    <?php 
        if(isset($_GET['info'])) {
            $status = $updated ? "成功" : "失败<br>".mysqli_error($db);
            show_text("更新${status}！");
            echo "<script>history.go(-2);</script>";
        } else {
            echo "
    <br>
    <form method='get'>
        <input name='action' value='edit' hidden=true/>
        <input name='id' value='$id' hidden=true/>
        <input name='url' value='$url' hidden=true/>
        <pre>$url</pre>
        <div class='form-group' id='inputdiv'>
            <input class='form-control inputbox' id='source' placeholder='Source' type='text' name='source' value='$source' autocomplete='off' />
        </div>
        <div class='form-group' >";
        list_tag_edit($tags);
        echo "
        </div>
        <div class='form-group' id='inputdiv'>
            <textarea class='form-control inputbox' id='info' placeholder='Info' type='text' name='info' style='height:40%;'>$info</textarea>
        </div>
		<input class='btn btn-info' type='submit' value='&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;'>
	</form>
	";}
    ?>
</div>
