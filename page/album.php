<?php

$imgs = $_POST["imgs"];
$album = $_POST["album"];
$todo = $_POST["todo"];

if($todo == "add") {
    
    $imgs = explode(",", $imgs);
    for($i = 0; $i < count($imgs); $i++) {
        $imgs[$i] = trim($imgs[$i]);
    }
    
    $result = run_sql("SELECT id, info FROM $album_table WHERE name='$album';");
    
    if($result) {
        $album_id = $result -> fetch_array()['id'];
        if($imgs) {
            foreach($imgs as $img) {
                if($img) {
                    $result = run_sql("INSERT INTO $amap_table (data_id, album_id) VALUES ('$img', '$album_id');");
                }
            }
        }
    }
    
    jump_with_text("操作". ($result ? "成功" : "失败") ."!", "?");
} else if($todo == "remove") {
    
    $imgs = explode(",", $imgs);
    for($i = 0; $i < count($imgs); $i++) {
        $imgs[$i] = trim($imgs[$i]);
    }
    
    $result = run_sql("SELECT id, info FROM $album_table WHERE name='$album';");
    
    if($result) {
        $album_id = $result -> fetch_array()['id'];
        if($imgs) {
            foreach($imgs as $img) {
                if($img) {
                    $result = run_sql("DELETE FROM $amap_table WHERE data_id='$img' AND album_id='$album_id';");
                }
            }
        }
    }
    
    jump_with_text("操作". ($result ? "成功" : "失败") ."!", "?");
} else {
	$target = $_GET["target"];
	$album = $_GET["album"];
    $tags = $_GET["tags"];
    $except = $_GET["except"];
    
    $result = get_items($tags, $except);
    
    $tags = explode_trim($tags);
    $except = explode_trim($except);
    
    if($tags) {
        $tag_title .= implode(",", $tags);
    }
    if($except) {
        $tag_title .= ($tag_title ? "," : "") . "-" . implode(",-", $except);;
    }
    
    $tag_to_add = implode(",", $tags);
    $tag_to_organize = $tag_to_add;
    $except_to_organize = implode(",", $except);
    
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
    
    $result_tag = $db -> query("SELECT name FROM $tag_table;");
    for ($i = 0; $i < $result_tag -> num_rows; $i++) {
	    $tags_all[$i] = $result_tag -> fetch_array()['name'];
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
    $('.tags').masonry({
        gutter: 8,
        itemSelector: '.tag',
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
        $("#todo").val("add");
    });
    $("#remove").click(function() {
        $("#todo").val("remove");
    });
    $("#show_tag").click(function() {
        $(".tags").slideToggle(0, resize);
    });
});
</script>

<div class="panel-heading"> 
	<a href="?action=album&target=$target&album=<?php echo $album; ?>">添加到<?php echo "「${album}」";if($tag_title) echo " : $tag_title"; ?></a>
</div>

<div class="panel-body text-center grid-div">
	<div style='margin:0 10px 0 10px;'>
        <div class='btn btn-info' id='show_tag'>筛选</div>
        <div class='tags' hidden=true style='margin:10px auto;'>
            <?php
                echo "<a href='?action=album&target=$target&album=$album&tags=_NULL_'><div class='tag'>&nbsp;&nbsp;无标签&nbsp;&nbsp;</div></a>";
                if($tags || $except) {
                    if($tags_all) {
                        foreach($tags_all as $tag) {
                            echo"
                	        <div class='tag'>
                	            &nbsp;&nbsp;<a href='?action=album&target=$target&album=$album&tags=$tag_to_add,$tag&except=$except_to_organize'>+</a>&nbsp;<a href='?action=album&target=$target&album=$album&tags=$tag'>$tag</a>&nbsp;<a href='?action=album&target=$target&album=$album&tags=$tag_to_add&except=$except_to_organize,$tag'>&nbsp;-&nbsp;</a>&nbsp;&nbsp;
                	        </div>
                	        ";
                	    }
                    }
                	echo"
                	    <a href='?action=album&target=$target&album=$album'>
                	    <div class='tag'>
                	        &nbsp;&nbsp;清除筛选&nbsp;&nbsp;
                	    </div>
                	    </a>
                	";
                } else {
                    if($tags_all) {
                        foreach($tags_all as $tag) {
                            echo"
                	        <a href='?action=album&target=$target&album=$album&tags=$tag'>
                	        <div class='tag'>
                	            &nbsp;&nbsp;$tag&nbsp;<a href='?action=album&target=$target&album=$album&tags=$tag_to_add&except=$except_to_organize,$tag'>&nbsp;-&nbsp;</a>&nbsp;&nbsp;
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
    <form method='post' action='?action=album&tags=<?php echo $tag_to_add; ?>'>
    <input name='todo' id='todo' value='' hidden=true/>
	<input name='imgs' id='imgs' value='' hidden=true/>
	<?php
        echo "<input name=album value='$album' hidden=true/>";
    ?>
    <div class='form-group' style="width:100%">
        <div class='btn btn-info' id='reset'>&nbsp;&nbsp;&nbsp;重置&nbsp;&nbsp;&nbsp;</div>&nbsp;&nbsp;&nbsp;
	    <input class='btn btn-info' id='settag' type='submit' value='&nbsp;&nbsp;&nbsp;确定&nbsp;&nbsp;&nbsp;'>&nbsp;&nbsp;&nbsp;
		<input class='btn btn-danger' id='remove' type='submit' value='&nbsp;&nbsp;&nbsp;移除&nbsp;&nbsp;&nbsp;'>
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
</div>