<?php

# Get Tag
$tag = $_GET["tag"];

# Get Data
if($tag) {
    if($tag == "_NULL_") {
        $tag = "无标签";
        $result=$db->query("SELECT id,info,url FROM $data_table WHERE id NOT IN (SELECT data_id FROM $map_table);");
    } else {
        $result=$db->query("SELECT $data_table.id,info,url FROM $data_table,$map_table,$tag_table WHERE $map_table.data_id=$data_table.id AND $map_table.tag_id=$tag_table.id AND $tag_table.name='$tag';");
    }
} else {
	$result=$db->query("SELECT id,info,url FROM $data_table;");
}

# Check Data
if(!$result)  {
	exit('Error:<br>'.mysqli_error($db));
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
}
$().ready(resize);
$(window).resize(resize);
</script>

<div class="panel-heading">
	<a href="?action=tags">图片<?php if($tag) echo " - $tag"; ?></a>
</div>
<div class="panel-body text-center grid-div">
	<a class="btn btn-info" href="?action=add">&nbsp;&nbsp;&nbsp;添加&nbsp;&nbsp;&nbsp;</a>
	<?php if($tag && $tag != "无标签") {echo "&nbsp;&nbsp;&nbsp;<a class='btn btn-danger' href='?action=deletetag&tag=$tag'>&nbsp;&nbsp;&nbsp;删除标签&nbsp;&nbsp;&nbsp;</a>";} ?>
	<br>
	<br>
	<div class="grid">
    <?php
    #Display Data
    for ($i = 0; $i < $result -> num_rows; $i++) {
    	$array = $result -> fetch_array();
    	$id = $array['id'];
    	$url = $array['url'];
    	$info = $array['info'];
    	item_image($url, $info, "?action=img&id=$id");
    }
    ?>
	</div>
</div>
