<?php

# Get Data
$tag = $_GET['tag'];
$new_name = $_GET['new_name'];
$info = $_GET['info'];
$nsfw = $_GET['nsfw'];

if(($new_name || $info || $nsfw) && $tag) {
	$result = $db -> query("UPDATE $tag_table SET name='$new_name', info='$info', nsfw='$nsfw' WHERE name='$tag';");
    jump_with_text("更新" . ($result ? "成功" : "失败"), "?tags=" . ($new_name ? $new_name : $tag));
} else {
    $result = $db -> query("SELECT name, info, nsfw FROM $tag_table WHERE name='$tag';");
    if($result) {
        $array = $result -> fetch_array();
        $name = $array['name'];
        $info = $array['info'];
        $nsfw = $array['nsfw'];
    }
}

echo "
<div class='panel-heading'>
	<a href='?'>编辑标签</a>
</div>
<div class='panel-body' style='max-width:400px; margin: 0 auto;'>
    <br>
    <form method='get'>
        <input name='action' value='edittag' hidden=true/>
        <input name='tag' value='$tag' hidden=true/>
        <pre>$tag</pre>
        <div class='form-group' id='inputdiv'>
            <input class='form-control inputbox' placeholder='New Name' type='text' name='new_name' value='$name' autocomplete=off />
        </div>
        <div class='form-group' id='inputdiv'>
            <select class='form-control inputbox' name='nsfw'>
                <option value=0>Safe</option>
                <option value=1>NFSW</option>
            </select>
        </div>
        <div class='form-group' id='inputdiv'>
            <textarea class='form-control inputbox' id='info' placeholder='Info' type='text' name='info' style='height:40%;'>$info</textarea>
        </div>
		<input class='btn btn-info' type='submit' value='&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;'>
	</form>
</div>
";
