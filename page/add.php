<?php

# Get Data
$info = $_GET['info'];
$url = $_GET['url'];
$tags = $_GET['tags'];
$source = $_GET['source'];
$need_edit = $_GET['need_edit'];

if($models_add) {
    foreach($models_add as $model) {
	    include("./model/" . $model . ".php");
    }
}

$tags_select = explode(",", $tags);

# Check Data
if($url && !$need_edit) {
    # Add to Database
    $added = add_item($url, $info, $tags_select, $source);
    $id = md5($url);
} else {
    # Get All Tags
    unset($tags);
    $result = $db -> query("SELECT name FROM $tag_table;");
    for($i = 0; $i < $result -> num_rows; $i++) {
    	$tags[$result -> fetch_array()['name']] = "";
    }
    foreach($tags_select as $tag) {
        if($tag) {
            $tags[trim($tag)] = TRUE;
        }
    }
}

?>

<script>
alert(window.clipboardData.getData("text"));
</script>

<div class="panel-heading">
	<a href="?">添加图片</a>
</div>
<div class="panel-body" style="max-width:400px; margin: 0 auto;">
    <?php 
        if($url && !$need_edit) {
            $status = $added ? "成功" : "失败<br>".mysqli_error($db);
            
            # Script
            echo "
            <script>
                window.setTimeout(\"location='?action=add&tags=$tags'\", 3000);
            </script>
            ";
            
            # Display Data
            echo '
            <div style="margin-bottom:10px;">
                <a class="btn btn-info" href="?action=add&tags='. $tags .'">&nbsp;&nbsp;&nbsp;继续&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;
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
            <input class='form-control inputbox' id='url' placeholder='URL' type='text' name='url' value='$url' autocomplete='off' />
        </div>
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
		<input class='btn btn-info' type='submit' value='&nbsp;&nbsp;&nbsp;添加&nbsp;&nbsp;&nbsp;'>
	</form>
	";}
    ?>
</div>
