<?php

# Get ID
$id = $_GET['id'];

# Get Data
$result = $db->query("SELECT id,info,url,time,source FROM $data_table WHERE id='$id';");

# Check Data
if(!$result)  {
	exit('Error:<br>'.mysqli_error($db));
}

# Get Tags
$result_tags = $db -> query("SELECT $tag_table.name FROM $map_table,$tag_table WHERE $map_table.tag_id=$tag_table.id AND $map_table.data_id='$id';");
for ($i = 0; $i < $result_tags -> num_rows; $i++) {
	$tags[$i] = $result_tags -> fetch_array()['name'];
}

?>

<script>
function resize() {
    if($(window).width() > 1010) {
        $('.grid-item').width('500px');
    } else {
        $('.grid-item').width($('.grid-div').width() - 10);
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
	<a href="javascript:history.go(-1);">详情</a>
</div>
<div class="panel-body text-center grid-div">
	<div class="grid">
<?php
#Display Data
for ($i = 0; $i < $result -> num_rows; $i++) {
	$array = $result -> fetch_array();
	$id = $array['id'];
	$url = $array['url'];
	$info = $array['info'];
	$source = $array['source'];
	$time = $array['time'];
	$info = nl2br($info);
	item_image_linked($url, $info, $url);
}
if($models_img) {
    foreach($models_img as $model) {
	    include("./model/" . $model . ".php");
    }
}
?>
        <div class='grid-item info'>
            <?php if($mod) {echo "<div class='info' >".$mod."</div>";} ?>
            <div class='info' >
                <b>图片地址: </b>
                <pre style='margin:0 30px 0 30px;'><?php echo $url; ?></pre>
            </div>
<?php

if($source) {
    echo "
<div class='info' >
    <b>来源: </b>
    <pre style='margin:0 30px 0 30px;'><a href='$source'>$source</a></pre>
</div>";
}

?>
            <div class='info' >
                <b>标签: </b>
                <div style='margin:0 10px 0 10px;'>
                <?php list_tag($tags); ?>
	            </div>
            </div>
            <div class='info'>
                <b>创建时间: </b><?php echo $time ?>
            </div>
            <div class='info'>
                <a class='btn btn-info' href='?action=edit&id=<?php echo $id ?>'>&nbsp;&nbsp;&nbsp;编辑&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;
                <a class='btn btn-danger' href='?action=delete&id=<?php echo $id ?>'>&nbsp;&nbsp;&nbsp;删除&nbsp;&nbsp;&nbsp;</a>
            </div>
        </div>
	</div>
</div>
