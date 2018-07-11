<?php

# Get Data
$album = $_GET['album'];
$name = $_GET['name'];
$info = $_GET['info'];
$tags = $_GET['tags'];
$except = $_GET['except'];
$sort = $_GET['sort'];
$loadingimg = $_GET['loadingimg'];
$nsfw = $_GET['nsfw'];

if(isset($_GET['name']) && $album) {
    $result = $db -> query("UPDATE $album_table SET name='$name', tags='$tags', except='$except', info='$info', sort='$sort', loadingimg='$loadingimg', nsfw='$nsfw' WHERE name='$album';");
    jump_with_text("更新" . ($result ? "成功" : "失败"), "?tags=" . ($new_name ? $new_name : $tag));
} else {
    $result = $db -> query("SELECT name, tags, except, info, sort, loadingimg, nsfw FROM $album_table WHERE name='$album';");
    if($result) {
        $array = $result -> fetch_array();
        $name = $array['name'];
        $tags = $array['tags'];
        $except = $array['except'];
        $info = $array['info'];
        $sort = $array['sort'];
        $loadingimg = $array['loadingimg'];
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
        <input name='action' value='editalbum' hidden=true/>
        <input name='album' value='$album' hidden=true/>
        <div class='form-group' id='inputdiv'>
            <input class='form-control inputbox' placeholder='Name' type='text' name='name' value='$name' autocomplete=off />
        </div>
        <div class='form-group' id='inputdiv'>
            <input class='form-control inputbox' id='tags' placeholder='Tags' type='text' name='tags' value='$tags' autocomplete='off' />
        </div>
        <div class='form-group' id='inputdiv'>
            <input class='form-control inputbox' id='except' placeholder='Except' type='text' name='except' value='$except' autocomplete='off' />
        </div>
        <div class='form-group' id='inputdiv'>
            <select class='form-control' name='sort'>
                <option value='' ". (($sort=='') ? "selected='selected'" : "" ) .">默认排序</option>
                <option value='DESC' ". (($sort=='DESC') ? "selected='selected'" : "" ) .">最新在前</option>
                <option value='ASC' ". (($sort=='ASC') ? "selected='selected'" : "" ) .">最旧在前</option>
                <option value='RAND' ". (($sort=='RAND') ? "selected='selected'" : "" ) .">随机</option>
            </select>
        </div>
        <div class='form-group' id='inputdiv'>
            <select class='form-control' name='nsfw'>
                <option value='0' ". (($nsfw=='0') ? "selected='selected'" : "" ) .">No NSFW</option>
                <option value='1' ". (($nsfw=='1') ? "selected='selected'" : "" ) .">Include NSFW</option>
            </select>
        </div>
        <div class='form-group' id='inputdiv'>
            <input class='form-control inputbox' id='loadingimg' placeholder='Loadingimg' type='text' name='loadingimg' value='$loadingimg' autocomplete='off' />
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
