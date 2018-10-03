<?php

# Get ID
$id = $_GET['id'];

# Get Data
if(is_numeric($_GET['location'])) {
    include_once("./data/classes/Imgs.class.php");

    $tags = $_COOKIE["tags"];
    $except = $_COOKIE["except"];
    $album = $_COOKIE["album"];
    $nsfw = $_COOKIE["nsfw"];
    $sort = $_COOKIE["sort"];
    $location = $_GET["location"];

    if($location != 0) {
        $result = Imgs::getImgs(null, $album, $tags, $except, $nsfw, $sort, $location - 1, 3);
        if($result) {
            $previous = $result -> fetch_array()['id'];
            $current = $result -> fetch_array();
            $next = $result -> fetch_array()['id'];
            $location_previous = $location - 1;
            $location_next = $location + 1;
        } else {
            echo "Error!<br>";
            echo mysqli_error($db);
        }
    } else {
        $result = Imgs::getImgs(null, $album, $tags, $except, $nsfw, $sort, 0, 2);
        if($result) {
            $current = $result -> fetch_array();
            $next = $result -> fetch_array()['id'];
            $location_next = $location + 1;
        } else {
            echo "Error!<br>";
            echo mysqli_error($db);
        }
    }
} else {
	$result = $db->query("SELECT id,info,url,time,source FROM $data_table WHERE id='$id';");
    if($result)  {
        $current = $result -> fetch_array();
    } else {
	    exit('Error:<br>'.mysqli_error($db));
    }
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
    <?php
    $back  = "javascript:history.go(-1);";
    //$back  = "?album=$album";
    if(isset($_GET["location"])) {
        echo "&nbsp;&nbsp;&nbsp;" . ($previous ? "<a href='?action=img&location=$location_previous&id=$previous'>Prev</a>" : "Prev") . "&nbsp;&nbsp;&nbsp;";
        echo "&nbsp;&nbsp;&nbsp;<a href='$back'>详情</a>&nbsp;&nbsp;&nbsp;";
        echo "&nbsp;&nbsp;&nbsp;" . ($next ? "<a href='?action=img&location=$location_next&id=$previous'>Next</a>" : "Next") . "&nbsp;&nbsp;&nbsp;";
    } else {
        echo "<a href='?'>详情</a>";
    }
    ?>
</div>
<div class="panel-body text-center grid-div">
	<div class="grid">
<?php
#Display Data
//for ($i = 0; $i < $result -> num_rows; $i++) {
	$array = $current;
	$id = $array['id'];
	$url = $array['url'];
	$info = $array['info'];
	$source = $array['source'];
	$time = $array['time'];
	$info = nl2br($info);
	item_image_linked($url, $info, $url);
//}
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
