<?php

# Get Tag
$name = $_GET['tag'];
$verified = $_GET['verified'];

# Delete Data
if($name && $verified == "true") {
    $result = $db -> query("SELECT id FROM $tag_table WHERE name='$name';");
	$id = $result -> fetch_array()['id'];
	
    $result = $db -> query("DELETE FROM $tag_table WHERE id='$id';");
    $deleted = $result;
    if($deleted) {
        $result = $db -> query("DELETE FROM $map_table WHERE tag_id='$id';");
    }
}

?>

<div class="panel-heading">
	<a href="?">删除标签</a>
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
        <input name='action' value='deletetag' hidden=true/>
        <input name='tag' value='$name' hidden=true/>
        <input name='verified' value='true' hidden=true/>
        <h2>您确认要删除${name}标签吗?</h2>
		<input class='btn btn-danger' type='submit' value='&nbsp;&nbsp;&nbsp;确认&nbsp;&nbsp;&nbsp;'>
	</form>
	";}
    ?>
</div>
