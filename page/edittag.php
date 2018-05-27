<?php

# Get Data
$tag = $_GET['tag'];
$new_name = $_GET['new_name'];

if($new_name && $tag) {
    $result = $db -> query("UPDATE $tag_table SET name='$new_name' WHERE name='$tag';");
    $text = "更新" . $result ? "成功" : "失败";
    jump_with_text($text, "?tag=$new_name");
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
            <input class='form-control inputbox' placeholder='New Name' type='text' name='new_name' />
        </div>
		<input class='btn btn-info' type='submit' value='&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;'>
	</form>
</div>
";
