<?php

# Get ID
$id = $_GET['id'];

# Get Data
$result = $db->query("SELECT id,info,url,time FROM $data_table WHERE id='$id';");

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
    width: 80%;
    margin-bottom: 8px;
    box-shadow: 2px 4px 6px #888888;
}
.grid-item-info {
    margin:8px 0 0 0;
    padding:0 8px 8px 8px;
    word-wrap:break-word;
    overflow:hidden;
    text-overflow:ellipsis;
}
.info {
    padding:8px 0 8px 0;
}
</style>

<div class="panel-heading">
	详情
</div>
<div class="panel-body text-center">
	<div class="grid">
<?php
#Display Data
for ($i = 0; $i < $result -> num_rows; $i++) {
	$array = $result -> fetch_array();
	$id = $array['id'];
	$url = $array['url'];
	$info = $array['info'];
	$time = $array['time'];
	echo "
	<div class='grid-item'>
	    <a href='$url'>
        <img width=100% src='$url' alt='$info' />
        </a>
    ";
    if($info) {
        echo "
        <div class='grid-item-info'>
        <p>$info</p>
        </div>"
    ;}
    echo "</div>";
}
?>
        <div class='grid-item info'>
            <div class='info'>
                <b>创建时间:</b><?php echo $time ?>
            </div>
            <div class='info'>
                <a class='btn btn-info' href='?action=edit&id=<?php echo $id ?>'>&nbsp;&nbsp;&nbsp;编辑&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;
                <a class='btn btn-danger' href='?action=delete&id=<?php echo $id ?>'>&nbsp;&nbsp;&nbsp;删除&nbsp;&nbsp;&nbsp;</a>
            </div>
        </div>
	</div>
</div>

<script>
function resize() {
    if($(window).width() > 1010) {
        $('.grid-item').width('500px');
    } else {
        $('.grid-item').width($('.grid').width() - 10);
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