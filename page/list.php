<?php

//$sql = "SELECT $data_table.id,info,url FROM ($data_table LEFT JOIN $map_table ON $data_table.id=$map_table.data_id) LEFT JOIN $tag_table ON $map_table.tag_id=$tag_table.id ";

# Get Tag
$tags = $_GET["tags"];
$except = $_GET["except"];
$album = $_GET["album"];

if($album) {
	if($album != "_NULL_") {
		$result_album = run_sql("SELECT id, info FROM $album_table WHERE name='$album';");
	    if($result_album) {
		    $album_id = $result_album -> fetch_array()['id'];
		    $album_sql = " AND $data_table.id IN (SELECT $amap_table.data_id FROM $amap_table WHERE album_id='$album_id')";
	    }
	} 
} else {
	require("./page/common/albums.php");
	exit();
}

# Get Data
if($tags  || $except) {
    $tags = explode(",", $tags);
    $count = count($tags);
    $count_sub = 0;
    
    for($i = 0; $i < $count; $i++) {
        $tags[$i] = trim($tags[$i]);
        if(!$tags[$i]) {
            unset($tags[$i]);
            $count_sub++;
        }
    }
    $count -= $count_sub;
    
    if($count == 0) {           
        $except = explode(",", $except);
        $count_except = count($except);
        for($i = 0; $i < $count_except; $i++) {
            $except[$i] = trim($except[$i]);
            if(!$except[$i]) {
                unset($except[$i]);
            }
        }
        $except_to_organize = implode(",", $except);
        $tag_title .= ",-" . implode(",-", $except);
        $result=$db->query("SELECT $data_table.id,info,url FROM $data_table WHERE $data_table.id NOT IN (SELECT $data_table.id FROM $data_table, $map_table, $tag_table WHERE $map_table.data_id=$data_table.id AND $map_table.tag_id=$tag_table.id AND $tag_table.name IN ('". implode("','", $except) ."'))$album_sql GROUP BY $data_table.id ORDER BY $data_table.time DESC;");
    } else if($count == 1) {
        foreach($tags as $tag) {
            if($tag == "_NULL_") {
                $tag_title = "无标签";
                $tag_to_add = "";
                $tag_to_organize = $tag;
                $result=$db->query("SELECT id,info,url FROM $data_table WHERE id NOT IN (SELECT data_id FROM $map_table)$album_sql ORDER BY $data_table.time DESC;");
            } else {
                $allow_edit = TRUE;
                $tag_title = $tag;
                $tag_to_add = $tag;
                $tag_to_organize = $tag;
                $result_tag_info = $db->query("SELECT info FROM $tag_table WHERE name='$tag';");
                if($result_tag_info) {
                    $result_tag_info = $result_tag_info -> fetch_array()['info'];
                }
                if($except) {
                    $except = explode(",", $except);
                    $count_except = count($except);
                    for($i = 0; $i < $count_except; $i++) {
                        $except[$i] = trim($except[$i]);
                        if(!$except[$i]) {
                            unset($except[$i]);
                        }
                    }
                    $except_to_organize = implode(",", $except);
                    $tag_title .= ",-" . implode(",-", $except);
                    $except_sql = " AND $data_table.id NOT IN (SELECT $data_table.id FROM $data_table, $map_table, $tag_table WHERE $map_table.data_id=$data_table.id AND $map_table.tag_id=$tag_table.id AND $tag_table.name IN ('". implode("','", $except) ."'))";
                }
                $result=$db->query("SELECT $data_table.id,$data_table.info,url FROM $data_table,$map_table,$tag_table WHERE $map_table.data_id=$data_table.id AND $map_table.tag_id=$tag_table.id AND $tag_table.name='$tag'$except_sql$album_sql ORDER BY $data_table.time DESC;");
            }
            break;
        }
    } else {
        $tag_title = implode(",", $tags);
        $tag_to_add = $tag_title;
        $tag_to_organize = $tag_title;
        $sql = "'" . implode("','", $tags) . "'";
        if($except) {
            $except = explode(",", $except);
            $count_except = count($except);
            for($i = 0; $i < $count_except; $i++) {
                $except[$i] = trim($except[$i]);
                if(!$except[$i]) {
                    unset($except[$i]);
                }
            }
            $except_to_organize = implode(",", $except);
            $tag_title .= ",-" . implode(",-", $except);
            $except_sql = " AND $data_table.id NOT IN (SELECT $data_table.id FROM $data_table, $map_table, $tag_table WHERE $map_table.data_id=$data_table.id AND $map_table.tag_id=$tag_table.id AND $tag_table.name IN ('". implode("','", $except) ."'))";
        }
        $result=$db->query("SELECT $data_table.id,$data_table.info,url FROM $data_table,$map_table,$tag_table WHERE $map_table.data_id=$data_table.id AND $map_table.tag_id=$tag_table.id AND $tag_table.name IN ($sql)$except_sql$album_sql GROUP BY $data_table.id HAVING COUNT($tag_table.name)=$count ORDER BY $data_table.time DESC;");
    }
} else {
	$result=$db->query("SELECT id,info,url FROM $data_table WHERE id NOT IN ('')$album_sql ORDER BY $data_table.time DESC;");
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
	$tags_all[$i] = $result_tag -> fetch_array()['name'];
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
$().ready(function() {
    resize();
    $("#show_tag").click(function() {
        $(".tags").slideToggle(0, resize);
    });
});
$(window).load(resize);
$(window).resize(resize);
</script>

<div class="panel-heading">
	<?php
	$title = $album != "_NULL_" ? $album : "图片";
	$title = $tag_title ? ("$title : $tag_title") : $title;
	echo "<a href='?'>$title</a>";
	?>
</div>
<div class="panel-body text-center grid-div">
    <div style='margin:0 10px 0 10px;'>
        <div class='btn btn-info' id='show_tag'>筛选</div>
        <div class='tags' hidden=true style='margin:10px auto;'>
            <a href='?album=$album&tags=_NULL_'><div class='tag'>&nbsp;&nbsp;无标签&nbsp;&nbsp;</div></a>
            <?php
                if($tags || $except) {
                    if($tags_all) {
                        foreach($tags_all as $tag) {
                            echo"
                	        <div class='tag'>
                	            &nbsp;&nbsp;<a href='?album=$album&tags=$tag_to_add,$tag&except=$except_to_organize'>+</a>&nbsp;<a href='?album=$album&tags=$tag'>$tag</a>&nbsp;<a href='?album=$album&tags=$tag_to_add&except=$except_to_organize,$tag'>&nbsp;-&nbsp;</a>&nbsp;&nbsp;
                	        </div>
                	        ";
                	    }
                    }
                	echo"
                	    <a href='?album=$album'>
                	    <div class='tag'>
                	        &nbsp;&nbsp;清除筛选&nbsp;&nbsp;
                	    </div>
                	    </a>
                	";
                } else {
                    if($tags_all) {
                        foreach($tags_all as $tag) {
                            echo"
                	        <a href='?album=$album&tags=$tag'>
                	        <div class='tag'>
                	            &nbsp;&nbsp;$tag&nbsp;<a href='?album=$album&tags=$tag_to_add&except=$except_to_organize,$tag'>&nbsp;-&nbsp;</a>&nbsp;&nbsp;
                	        </div>
                	        </a>
                	        ";
                	    }
                    }
                }
            ?>
        </div>
	</div>
    <div style='margin-top:16px;'>
	    <?php
	    if($album != "_NULL_") {
	        echo "<a class='btn btn-info' href='?action=album&album=$album'>&nbsp;&nbsp;&nbsp;添加&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;";
	        echo "<a class='btn btn-info' href='?action=editalbum&album=$album'>&nbsp;&nbsp;&nbsp;编辑&nbsp;&nbsp;&nbsp;</a>";
	    }
	    if($result -> num_rows) {
	        echo ($album != "_NULL_" ? "&nbsp;&nbsp;&nbsp;" : "") . "<a class='btn btn-info' href='?action=organize&album=$album&tags=$tag_to_organize&except=$except_to_organize'>&nbsp;&nbsp;&nbsp;组织&nbsp;&nbsp;&nbsp;</a>";
	    }
	    ?>
	</div>
	<?php if($allow_edit) {
	    echo "<div style='margin-top:16px;'>";
	    echo "<a class='btn btn-info' href='?action=edittag&tag=$tag_to_add'>&nbsp;&nbsp;&nbsp;编辑标签&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;";
	    echo "<a class='btn btn-danger' href='?action=deletetag&tag=$tag_to_add'>&nbsp;&nbsp;&nbsp;删除标签&nbsp;&nbsp;&nbsp;</a>";
	    echo "</div>";
	    echo "<div style='margin-top:16px;'>". nl2br($result_tag_info) ."</div>";
    } ?>
	
    <?php
    #Display Data
    if($result -> num_rows) {
        echo "<div class='grid' style='margin-top:16px;'>";
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
        echo "</div>";
    } else {
        echo "<div style='color:#808080;margin-top:30px;margin-bottom:20px;'>没有图片</div>";
    }
    
    ?>
</div>
