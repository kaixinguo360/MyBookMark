<?php

# Get Tag
$tag = $_GET["tag"];

# Get Data
if($tag) {
    if($tag == "_NULL_") {
        $tag_title = "无标签";
        $result=$db->query("SELECT id,info,url FROM $data_table WHERE id NOT IN (SELECT data_id FROM $map_table);");
    } else {
        $tag_title = $tag;
        $result=$db->query("SELECT $data_table.id,info,url FROM $data_table,$map_table,$tag_table WHERE $map_table.data_id=$data_table.id AND $map_table.tag_id=$tag_table.id AND $tag_table.name='$tag';");
    }
} else {
	$result=$db->query("SELECT id,info,url FROM $data_table;");
}

# Check Data
if(!$result)  {
	exit('Error:<br>'.mysqli_error($db));
}

# Get Tags
$result_tag = $db -> query("SELECT name FROM $tag_table;");

# Check Data
if(!$result_tag)  {
	exit('Error:<br>'.mysqli_error($db));
}

# Decode Tags
for ($i = 0; $i < $result_tag -> num_rows; $i++) {
	$tags[$i] = $result_tag -> fetch_array()['name'];
}
?>

<script>
function resize() {
    if($(window).width() > 404) {
        $('.grid-item').width('200px');
    } else {
        $('.grid-item').width($('.grid-div').width() / 2 - 10);
    }
    $('.grid').masonry({
        gutter: 8,
        itemSelector: '.grid-item',
        fitWidth: true,
    });
    $('.tags').masonry({
        gutter: 8,
        itemSelector: '.tag',
        fitWidth: true,
    });
}
$().ready(resize);
$(window).load(resize);
$(window).resize(resize);
</script>

<div class="panel-heading">
	<a href="?">图片<?php if($tag) echo " - $tag_title"; ?></a>
</div>
<div class="panel-body text-center grid-div">
    <div style='margin:0 10px 0 10px;'>
        <div class='tags'>
            <a href='?tag=_NULL_'><div class='tag'>&nbsp;&nbsp;无标签&nbsp;&nbsp;</div></a>
            <?php list_tags($tags); ?>
        </div>
	</div>
    <div style='margin-top:16px;'>
	    <a class="btn btn-info" href="?action=add">&nbsp;&nbsp;&nbsp;添加&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;
	    <a class="btn btn-info" href="?action=organize&tag=<?php echo $tag; ?>">&nbsp;&nbsp;&nbsp;组织&nbsp;&nbsp;&nbsp;</a>
	</div>
	<?php if($tag && $tag != "_NULL_") {
	    echo "<div style='margin-top:16px;'>";
	    echo "<a class='btn btn-info' href='?action=edittag&tag=$tag'>&nbsp;&nbsp;&nbsp;编辑标签&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;";
	    echo "<a class='btn btn-danger' href='?action=deletetag&tag=$tag'>&nbsp;&nbsp;&nbsp;删除标签&nbsp;&nbsp;&nbsp;</a>";
	    echo "</div>";
    } ?>
	<div class="grid" style='margin-top:16px;'>
    <?php
    #Display Data
    for ($i = 0; $i < $result -> num_rows; $i++) {
    	$array = $result -> fetch_array();
    	$id = $array['id'];
    	$url = $array['url'];
    	$info = $array['info'];
        $info = strip_tags($info);
        if(strlen($info) > 50) {
            $info = mb_substr($info, 0, 50) . "...";
        }
    	item_image($url, $info, "?action=img&id=$id");
    }
    ?>
	</div>
</div>
