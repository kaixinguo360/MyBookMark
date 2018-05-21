<?php

# Get Data
$result=$db->query("SELECT id,info,url FROM $data_table;");

# Check Data
if(!$result)  {
	exit('Error!');
}

?>

<script src="//unpkg.com/masonry-layout@4/dist/masonry.pkgd.js"></script>
<style type="text/css">
.grid {
    width: 100%;
    margin: 0 auto;
}
.grid-item {
    margin-bottom: 8px;
    box-shadow: 2px 4px 6px #888888;
}
.grid-item-info {
    max-height:200px;
    margin:8px 0 0 0;
    padding:0 8px 8px 8px;
    word-wrap:break-word;
    overflow:hidden;
    text-overflow:ellipsis;
}
</style>

<div class="panel-heading">
	图片
</div>
<div class="panel-body text-center">
	<a class="btn btn-info" href="?action=add">&nbsp;&nbsp;&nbsp;添加&nbsp;&nbsp;&nbsp;</a>
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
}
?>
	</div>
</div>

<script>
function resize() {
    if($(window).width() > 404) {
        $('.grid-item').width('200px');
    } else {
        $('.grid-item').width($('.grid').width() / 2 - 10);
    }
    $('.grid').masonry({
        gutter: 8,
        itemSelector: '.grid-item',
        fitWidth: true,
    });
}
resize();
$(window).resize(resize);
</script>