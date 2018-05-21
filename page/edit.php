<?php

# Get Data
$tags = $_GET['tags'];
$info = $_GET['info'];
$url = $_GET['url'];
$id = $_GET['id'];

# Check Data
if(isset($_GET['info'])) {
    # Add Data To Database
    $result = $db -> query("UPDATE $data_table SET info='$info' WHERE id='$id';");
    $updated = $result;
    if($updated && isset($_GET['tags'])) {
        $result = $db -> query("DELETE FROM $map_table WHERE data_id='$id';");
        $tags = explode(",", $tags);
        foreach($tags as $tag) {
            $tag = trim($tag);
            if($tag) {
                $result = $db -> query("SELECT id FROM $tag_table WHERE name='$tag';");
                if(!$result -> num_rows) {
                    $result = $db -> query("INSERT INTO $tag_table (name) VALUES ('$tag');");
                    $result = $db -> query("SELECT id FROM $tag_table WHERE name='$tag';");
                }
                $array = $result -> fetch_array();
	            $tag_id = $array['id'];
                
                $result = $db -> query("insert into $map_table (data_id, tag_id) values ('$id', $tag_id);");
            }
        }
    }
} else {
    $result = $db -> query("SELECT info,url FROM $data_table WHERE id='$id';");
	$array = $result -> fetch_array();
	$url = $array['url'];
	$info = $array['info'];
	
	$result = $db -> query("SELECT $tag_table.name FROM $map_table,$tag_table WHERE $map_table.tag_id=$tag_table.id AND $map_table.data_id='$id';");
	for ($i = 0; $i < $result -> num_rows; $i++) {
		$tags .= " " . $result -> fetch_array()['name'] . ",";
	}
}

?>

<div class="panel-heading">
	编辑图片
</div>
<div class="panel-body" style="max-width:400px; margin: 0 auto;">
    <?php 
        if(isset($_GET['info'])) {
            $status = $updated ? "成功" : "失败<br>".mysqli_error($db);
            jump_with_text("更新${status}！", "?action=img&id=$id");
        } else {
            echo "
    <br>
    <form method='get'>
        <input name='action' value='edit' hidden=true/>
        <input name='id' value='$id' hidden=true/>
        <input name='url' value='$url' hidden=true/>
        <pre>$url</pre>
        <div class='form-group' id='inputdiv'>
            <input class='form-control inputbox' id='tags' placeholder='Tags' type='text' name='tags' value='$tags' />
        </div>
        <div class='form-group' id='inputdiv'>
            <textarea class='form-control inputbox' id='info' placeholder='Info' type='text' name='info' style='height:40%;'>$info</textarea>
        </div>
		<input class='btn btn-info' type='submit' value='&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;'>
	</form>
	";}
    ?>
</div>
