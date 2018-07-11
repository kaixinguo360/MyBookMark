<?php

$result = run_sql("SELECT id, info, name FROM $album_table WHERE nsfw <= $nsfw;");
if($result) {
	for ($i = 0; $i < $result -> num_rows; $i++) {
		$array = $result -> fetch_array();
		unset($item);
		$item['id'] = $array['id'];
		$item['info'] = $array['info'];
		$item['name'] = $array['name'];
	    $albums[$i] = $item;
    }
} else {
	exit("获取图集列表失败!<br>" . sqli_error($db));
}

?>
<div class="panel-heading">
	图集
</div>
<div class='panel-body' style='max-width:400px; margin: 0 auto;'>
    <div class='grid'>
        <a href='?album=_NULL_'>
            <div class='grid-item grid-item-info'>
                <div><h3>收藏的图片</h3></div>
                <div><p> * * * </p></div>
            </div>
        </a>
<?php
if($albums) {
    foreach($albums as $album) {
	    $name = $album['name'];
	    $info = $album['info'];
        echo "<a href='?album=$name'><div class='grid-item grid-item-info'>";
        echo "<div><h3>$name</h3></div>";
        echo "<div><p>". ($info ? $info : " * * * ") ."</p></div>";
        echo "</div></a>";
    }
}
?>
	    <a href='?action=addalbum'>
            <div class='grid-item grid-item-info'>
                <div><h3>新建图集</h3></div>
                <div><p> * * * </p></div>
            </div>
        </a>
    </div>
</div>