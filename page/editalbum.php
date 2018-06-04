<?php

# Get Data
$album = $_GET['album'];
$name = $_GET['name'];
$info = $_GET['info'];

if(isset($_GET['name']) && $album) {
	if($name) {
        $result = $db -> query("UPDATE $album_table SET name='$name', info='$info' WHERE name='$album';");
    } else {
        $result = $db -> query("UPDATE $album_table SET info='$info' WHERE name='$album';");
    }
    jump_with_text("更新" . ($result ? "成功" : "失败"), "?tags=" . ($new_name ? $new_name : $tag));
} else {
    $result = $db -> query("SELECT info FROM $album_table WHERE name='$album';");
    if($result) {
        $info = $result -> fetch_array()['info'];
    }
}

echo "
<div class='panel-heading'>
	<a href='?'>编辑标签</a>
</div>
<div class='panel-body' style='max-width:400px; margin: 0 auto;'>
    <br>
    <form method='get'>
        <input name='action' value='editalbum' hidden=true/>
        <input name='album' value='$album' hidden=true/>
        <pre>$album</pre>
        <div class='form-group' id='inputdiv'>
            <input class='form-control inputbox' placeholder='New Name' type='text' name='name' autocomplete=off />
        </div>
        <div class='form-group' id='inputdiv'>
            <textarea class='form-control inputbox' id='info' placeholder='Info' type='text' name='info' style='height:40%;'>$info</textarea>
        </div>
        <div style='margin:20px;'>
		    <input class='btn btn-info' type='submit' value='&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;'>
        </div>
        <div style='margin:20px;'>
	        <a class='btn btn-danger' href='?action=deletealbum&album=$album'>&nbsp;&nbsp;&nbsp;删除&nbsp;&nbsp;&nbsp;</a>
	    </div>
	</form>
</div>
";
