<?php

$imgs = $_POST["imgs"];
$tags = $_POST["tags"];
$album = $_GET["album"];
$tag = $_GET["tag"];
$todo = $_POST["todo"];

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
}

if($todo == "settag") {
    
    $imgs = explode(",", $imgs);
    for($i = 0; $i < count($imgs); $i++) {
        $imgs[$i] = trim($imgs[$i]);
    }
    $tags = explode(",", $tags);
    for($i = 0; $i < count($tags); $i++) {
        $tags[$i] = trim($tags[$i]);
    }
    
    if($imgs) {
        foreach($imgs as $img) {
            //run_sql("DELETE FROM $map_table WHERE data_id='$img'");
            foreach($tags as $tag) {
                set_item_tag($img, $tag);
            }
        }
    }
    
    jump_with_text("操作完成!", "?");
} else if($todo == "delete") {
    $imgs = explode(",", $imgs);
    for($i = 0; $i < count($imgs); $i++) {
        $imgs[$i] = trim($imgs[$i]);
    }
    foreach($imgs as $img) {
        run_sql("DELETE FROM $data_table WHERE id='$img'");
        run_sql("DELETE FROM $map_table WHERE data_id='$img'");
    }
    jump_with_text("操作完成!", "?");
} else {
/* * * * */
$tags = $_GET["tags"];
$except = $_GET["except"];
$album = $_GET["album"];
$sort_mode = $_COOKIE["sort_mode"];
$nsfw = $_COOKIE['nsfw'] ? $_COOKIE['nsfw'] : 0;

if($sort_mode == "RAND") {
    $sort_sql = " ORDER BY RAND()";
} else if($sort_mode == "ASC") {
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

# NSFW?
$nsfw_except = array();
if(is_numeric($nsfw)) {
    $result_nsfw = $db -> query("SELECT name FROM $tag_table WHERE nsfw > $nsfw;");
    for ($i = 0; $i < $result_nsfw -> num_rows; $i++) {
    	$nsfw_except[$i] = $result_nsfw -> fetch_array()['name'];
    }
    $album_except = array_merge($album_except, $nsfw_except);
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
/* * * * */
    
    for ($i = 0; $i < $result -> num_rows; $i++) {
    	$array = $result -> fetch_array();
    	$img['id'] = $array['id'];
    	$img['url'] = $array['url'];
    	$info_tmp = $array['info'];
        $info_tmp = strip_tags($info_tmp);
        if(strlen($info_tmp) > 50) {
            $info_tmp = mb_substr($info_tmp, 0, 50) . "...";
        }
    	$img['info'] = $info_tmp;
        
        $imgs[$i] = $img;
    }
    
    $result = $db -> query("SELECT name FROM $tag_table WHERE nsfw <= $nsfw;");
    for ($i = 0; $i < $result -> num_rows; $i++) {
    	$tags_all[$result -> fetch_array()['name']] = "";
    }
}

?>

<script>
function resize() {
    if($(window).width() > 404) {
        $('.grid-item-out').width('200px');
    } else {
        $('.grid-item-out').width($('.grid-div').width() / 2 - 10);
    }
    $('.grid').masonry({
        gutter: 8,
        itemSelector: '.grid-item-out',
        fitWidth: true,
    });
}

function update_items() {
    var imgs_text = "";
    is_reset = false;
    $(".grid-item").each(function() {
        if(imgs.get(this.id)) {
            imgs_text += this.id + ",";
            is_reset = true;
            $(this).css("margin", "5px");
            $(this).css("margin-bottom", "3px");
            $(this).css("box-shadow", "1px 2px 4px #4444ff");
        } else {
            $(this).css("margin", "0");
            $(this).css("margin-bottom", "8px");
            $(this).css("box-shadow", "2px 4px 6px #888888");
        }
        if(is_reset) {
            $("#reset").html("&nbsp;&nbsp;&nbsp;重置&nbsp;&nbsp;&nbsp;");
        } else {
            $("#reset").html("&nbsp;&nbsp;&nbsp;全选&nbsp;&nbsp;&nbsp;");
        }
    });
    $("#imgs").val(imgs_text);
}
function goTop(acceleration, time) {
	acceleration = acceleration || 0.1;
	time = time || 16;
 
	var x1 = 0;
	var y1 = 0;
	var x2 = 0;
	var y2 = 0;
	var x3 = 0;
	var y3 = 0;
 
	if (document.documentElement) {
		x1 = document.documentElement.scrollLeft || 0;
		y1 = document.documentElement.scrollTop || 0;
	}
	if (document.body) {
		x2 = document.body.scrollLeft || 0;
		y2 = document.body.scrollTop || 0;
	}
	var x3 = window.scrollX || 0;
	var y3 = window.scrollY || 0;
 
	// 滚动条到页面顶部的水平距离
	var x = Math.max(x1, Math.max(x2, x3));
	// 滚动条到页面顶部的垂直距离
	var y = Math.max(y1, Math.max(y2, y3));
 
	// 滚动距离 = 目前距离 / 速度, 因为距离原来越小, 速度是大于 1 的数, 所以滚动距离会越来越小
	var speed = 1 + acceleration;
	window.scrollTo(Math.floor(x / speed), Math.floor(y / speed));
 
	// 如果距离不为零, 继续调用迭代本函数
	if(x > 0 || y > 0) {
		var invokeFunction = "goTop(" + acceleration + ", " + time + ")";
		window.setTimeout(invokeFunction, time);
	}
}
$(window).load(resize);
$(window).resize(resize);
$(document).ready(function(){
    resize();
    imgs = new Map();
<?php
foreach($imgs as $img) {
	$id = $img['id'];
	
	$is_selected = $img['is_selected'] ? "true" : "false";
    
    echo "imgs.set('$id', $is_selected);";
}
?>
    update_items();
    $(".grid-item").click(function(event) {
        var id = event.delegateTarget.id;
        if(imgs.get(id)) {
            imgs.set(id, false);
        } else {
            imgs.set(id, true);
        }
        update_items();
    });
    $("#reset").click(function() {
        for(var [img, is_selected] of imgs) {
            imgs.set(img, !is_reset);
        }
        update_items();
    });
    $("#settag").click(function() {
        $("#todo").val("settag");
    });
    $("#delete").click(function() {
        $("#todo").val("delete");
    });
});
</script>

<div class="panel-heading">
	<a href="?tags=<?php echo $_GET["tags"]; ?>">组织<?php if($tag_title) echo " - $tag_title"; ?></a>
</div>

<div class="panel-body text-center grid-div">
    <form method='post' action='?action=organize&tags=<?php echo $tag_to_add; ?>'>
    <input name='todo' id='todo' value='' hidden=true/>
	<input name='imgs' id='imgs' value='' hidden=true/>
	<div class='form-group' style="margin-top:-10px;width:100%">
        <?php
        list_tag_edit($tags_all);
        ?>
    </div>
    <div class='form-group' style="width:100%">
        <div class='btn btn-info' id='reset'>&nbsp;&nbsp;&nbsp;重置&nbsp;&nbsp;&nbsp;</div>&nbsp;&nbsp;&nbsp;
	    <input class='btn btn-info' id='settag' type='submit' value='&nbsp;&nbsp;&nbsp;确定&nbsp;&nbsp;&nbsp;'>&nbsp;&nbsp;&nbsp;
		<input class='btn btn-danger' id='delete' type='submit' value='&nbsp;&nbsp;&nbsp;删除&nbsp;&nbsp;&nbsp;'>
    </div>
	<div class="grid">

<?php

if($imgs) {
    foreach($imgs as $img) {
    	$id = $img['id'];
    	$url = $img['url'];
    	$info = $img['info'];
        
        $img_class = $info ? "grid-item-img" : "grid-item-img-only";
        $content = "<img class='$img_class' width=100% src='$url' />"
            .($info ? "<div class='grid-item-info'><p>$info</p></div>" : "");
        echo "<div class='grid-item-out'><div class='grid-item' id='$id'>$content</div></div>";
    }
} else {
    echo "<div style='color:#808080;'>没有图片</div>";
}

?>
    </div>
    <div class='form-group' style="width:100%;margin-top:16px;">
        <div onclick='goTop(1);' class='btn btn-info'>&nbsp;&nbsp;&nbsp;回到顶部&nbsp;&nbsp;&nbsp;</div>
    </div>
    </form>
</div>