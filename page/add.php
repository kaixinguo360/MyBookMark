<?php

# Get Data
$info = $_GET['info'];
$url = $_GET['url'];
$tags = $_GET['tags'];

# Check Data
if($url) {
    #Get ID
    $id = md5($url);
    # Add Data To Database
    $result = $db -> query("insert into $data_table (id, url, info) values ('$id', '$url', '$info');");
    $added = $result;
    if($added && $tags) {
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
    # Get All Tags
    $result = $db -> query("SELECT name FROM $tag_table;");
    for ($i = 0; $i < $result -> num_rows; $i++) {
    	$tags[$result -> fetch_array()['name']] = "";
    }
}

?>

<div class="panel-heading">
	<a href="?">添加图片</a>
</div>
<div class="panel-body" style="max-width:400px; margin: 0 auto;">
    <?php 
        if($url) {
            $status = $added ? "成功" : "失败<br>".mysqli_error($db);
            
            #Display Data
            echo '
            <div style="margin-bottom:10px;">
                <a class="btn btn-info" href="?action=add">&nbsp;&nbsp;&nbsp;继续&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;
                <a class="btn btn-info" href="./index.php">&nbsp;&nbsp;&nbsp;返回&nbsp;&nbsp;&nbsp;</a>
            </div>
            ';
            echo "
            <div>
                <p><b>添加$status</b></p>
            </div>
            ";
        	echo "
            <a href='?action=img&id=$id'>
        	<div class='grid-item'>
                <img width=100% src='$url' alt='$info' />
            ";
            if($info) {
                echo "
                <div class='grid-item-info'>
                <p>$info</p>
                </div>"
            ;}
            echo "</div></a>";
        } else {
            echo "
    <br>
    <form method='get'>
        <input name='action' value='add' hidden=true/>
        <div class='form-group' id='inputdiv'>
            <input class='form-control inputbox' id='url' placeholder='URL' type='text' name='url' />
        </div>
        <div class='form-group' >";
        list_tag_edit($tags);
        echo "
        </div>
        <div class='form-group' id='inputdiv'>
            <textarea class='form-control inputbox' id='info' placeholder='Info' type='text' name='info' style='height:40%;'></textarea>
        </div>
		<input class='btn btn-info' type='submit' value='&nbsp;&nbsp;&nbsp;添加&nbsp;&nbsp;&nbsp;'>
	</form>
	";}
    ?>
</div>
