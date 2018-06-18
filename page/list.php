<?php

//$sql = "SELECT $data_table.id,info,url FROM ($data_table LEFT JOIN $map_table ON $data_table.id=$map_table.data_id) LEFT JOIN $tag_table ON $map_table.tag_id=$tag_table.id ";

# ReFresh Settings
if(!$_COOKIE["updated"]) {
    $result_settings = run_sql("SELECT name, value FROM $setting_table WHERE name IN ('sort', 'loadingimg')");
    for ($i = 0; $i < $result_settings -> num_rows; $i++) {
    	$array = $result_settings -> fetch_array();
    	setcookie($array["name"], $array["value"], time()+60*60*24*30);
    }
    setcookie("updated", "true", time()+60*60*24*30);
}

# Get Params
$tags = $_GET["tags"];
$except = $_GET["except"];
$album = $_GET["album"];

# Get Cookies
$sort = isset($_GET["sort"]) ? $_GET["sort"] : $_COOKIE["sort"];
$loadingimg = isset($_GET["loadingimg"]) ? $_GET["loadingimg"] : $_COOKIE["loadingimg"];

if($sort == "RAND") {
    $sort_sql = " ORDER BY RAND()";
} else if($sort == "ASC") {
    $sort_sql = " ORDER BY $data_table.time ASC";
} else {
    $sort_sql = " ORDER BY $data_table.time DESC";
}

if($album) {
	if($album != "_NULL_") {
		$result_album = run_sql("SELECT id, info, tags, except FROM $album_table WHERE name='$album';");
	    if($result_album) {
		    $array = $result_album -> fetch_array();
		    $album_id = $array['id'];
		    $tag_info = $array['info'];
		    $album_tags = explode(',', $array['tags']);
		    for($i = 0; $i < count($album_tags); $i++) {
                $album_tags[$i] = trim($album_tags[$i]);
                if(!$album_tags[$i]) {
                    unset($album_tags[$i]);
                }
            }
            $album_except = explode(',', $array['except']);
		    for($i = 0; $i < count($album_except); $i++) {
                $album_except[$i] = trim($album_except[$i]);
                if(!$album_except[$i]) {
                    unset($album_except[$i]);
                }
            }
		    //$album_sql = " AND $data_table.id IN (SELECT $amap_table.data_id FROM $amap_table WHERE album_id='$album_id')";
	    }
	} else {
		$album_tags = array();
		$album_except = array();
	}
} else {
	require("./page/common/albums.php");
	exit();
}

# Get Data
if($tags  || $except || $album_tags || $album_except) {
    $tags = explode(",", $tags);
    $count = count($tags);
    
    for($i = 0; $i < $count; $i++) {
        $tags[$i] = trim($tags[$i]);
        if(!$tags[$i]) {
            unset($tags[$i]);
        }
    }
    $count = count($tags) + count($album_tags);
    
    if($count == 0) {
        if($except || $album_except) {
            $except = explode(",", $except);
            $count_except = count($except);
            for($i = 0; $i < $count_except; $i++) {
                $except[$i] = trim($except[$i]);
                if(!$except[$i]) {
                    unset($except[$i]);
                }
            }
            $except_to_organize = implode(",", $except);
            $tag_title .= $except ? (",-" . implode(",-", $except)) : "";
        }
        $result=$db->query("SELECT $data_table.id,info,url FROM $data_table WHERE $data_table.id NOT IN (SELECT $data_table.id FROM $data_table, $map_table, $tag_table WHERE $map_table.data_id=$data_table.id AND $map_table.tag_id=$tag_table.id AND $tag_table.name IN ('". implode("','", array_merge($except ? $except : array(), $album_except)) ."'))$album_sql GROUP BY $data_table.id$sort_sql;");
    } else if($count == 1) {
        foreach($tags as $tag) {
            if($tag == "_NULL_") {
                $tag_title = "无标签";
                $tag_to_add = "";
                $tag_to_organize = $tag;
                $result=$db->query("SELECT id,info,url FROM $data_table WHERE id NOT IN (SELECT data_id FROM $map_table)$album_sql$sort_sql;");
                $is_null = TRUE;
            } else {
                $allow_edit = TRUE;
                $result_tag_info = $db->query("SELECT info FROM $tag_table WHERE name='$tag';");
                if($result_tag_info) {
                    $tag_info = $result_tag_info -> fetch_array()['info'];
                }
            }
            break;
        }
    }
    if($count >= 1 && !$is_null) {
        $tag_title = implode(",", $tags);
        $tag_to_add = $tag_title;
        $tag_to_organize = $tag_title;
        $sql = "'" . implode("','", array_merge($tags, $album_tags)) . "'";
        if($except || $album_except) {
            $except = explode(",", $except);
            $count_except = count($except);
            for($i = 0; $i < $count_except; $i++) {
                $except[$i] = trim($except[$i]);
                if(!$except[$i]) {
                    unset($except[$i]);
                }
            }
            $except_to_organize = implode(",", $except);
            $tag_title .= $except ? (",-" . implode(",-", $except)) : "";
            $except_sql = " AND $data_table.id NOT IN (SELECT $data_table.id FROM $data_table, $map_table, $tag_table WHERE $map_table.data_id=$data_table.id AND $map_table.tag_id=$tag_table.id AND $tag_table.name IN ('". implode("','", array_merge($except, $album_except)) ."'))";
        }
        $result=$db->query("SELECT $data_table.id,$data_table.info,url FROM $data_table,$map_table,$tag_table WHERE $map_table.data_id=$data_table.id AND $map_table.tag_id=$tag_table.id AND $tag_table.name IN ($sql)$except_sql$album_sql GROUP BY $data_table.id HAVING COUNT($tag_table.name)=$count$sort_sql;");
    }
} else {
	$result=$db->query("SELECT id,info,url FROM $data_table WHERE id NOT IN ('')$album_sql$sort_sql;");
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
function init() {
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
function resize() {
	$('.grid').masonry();
}
function resize_tags() {
	$('.tags').masonry();
}
$().ready(function() {
    init();
    $('img').each(function() {
        this.addEventListener('load', resize, true);
    });
    $("#show_tag").click(function() {
        $(".tags").slideToggle(0, resize_tags);
    });
});
$(window).load(init);
$(window).resize(init);
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
            <a href='?album=<?php echo $album; ?>&tags=_NULL_'><div class='tag'>&nbsp;&nbsp;无标签&nbsp;&nbsp;</div></a>
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
        if($tags || $except) {
            $tags_to_album = implode(',', array_merge($tags ? $tags : array(), $album_tags));
            $except_to_album = implode(',', array_merge($except ? $except : array(), $album_except));
            echo "<a class='btn btn-info' href='?action=addalbum&need_edit=true&tags=$tags_to_album&except=$except_to_album'>&nbsp;&nbsp;&nbsp;保存&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;";
	    }
	    if($album != "_NULL_") {
	        echo "<a class='btn btn-info' href='?action=editalbum&album=$album'>&nbsp;&nbsp;&nbsp;编辑&nbsp;&nbsp;&nbsp;</a>";
	    }
	    if($result -> num_rows) {
	        echo ($album != "_NULL_" ? "&nbsp;&nbsp;&nbsp;" : "") . "<a class='btn btn-info' href='?action=organize&album=$album&tags=$tag_to_organize&except=$except_to_organize'>&nbsp;&nbsp;&nbsp;组织&nbsp;&nbsp;&nbsp;</a>";
	    }
	    ?>
	</div>
	<?php 
    if($allow_edit) {
	    echo "<div style='margin-top:16px;'>";
	    echo "<a class='btn btn-info' href='?action=edittag&tag=$tag_to_add'>&nbsp;&nbsp;&nbsp;编辑标签&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;";
	    echo "<a class='btn btn-danger' href='?action=deletetag&tag=$tag_to_add'>&nbsp;&nbsp;&nbsp;删除标签&nbsp;&nbsp;&nbsp;</a>";
	    echo "</div>";
    }
    if($tag_info) echo "<div style='margin-top:16px;'>". nl2br($tag_info) ."</div>";
    ?>
	
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
            if(mb_strlen($info) > 50) {
                $info = mb_substr($info, 0, 50) . "...";
            }
        	item_image($url, $info, "?action=img&id=$id", $loadingimg);
        }
        echo "</div>";
    } else {
        echo "<div style='color:#808080;margin-top:30px;margin-bottom:20px;'>没有图片</div>";
    }
    
    ?>
</div>