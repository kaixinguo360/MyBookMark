<?php

$sql = "SELECT $data_table.id,info,url FROM ($data_table LEFT JOIN $map_table ON $data_table.id=$map_table.data_id) LEFT JOIN $tag_table ON $map_table.tag_id=$tag_table.id ";

# Get Tag
$tags = $_GET["tags"];

# Get Data
if($tags) {
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
    
    if($count == 1) {
        $tag = $tags[0];
        if($tag == "_NULL_") {
            $tag_title = "无标签";
            $tag_to_add = "";
            $tag_to_organize = $tag;
            $result=$db->query("SELECT id,info,url FROM $data_table WHERE id NOT IN (SELECT data_id FROM $map_table);");
        } else {
            $allow_edit = TRUE;
            $tag_title = $tag;
            $tag_to_add = $tag;
            $tag_to_organize = $tag;
            $result=$db->query("SELECT $data_table.id,info,url FROM $data_table,$map_table,$tag_table WHERE $map_table.data_id=$data_table.id AND $map_table.tag_id=$tag_table.id AND $tag_table.name='$tag';");
        }
    } else {
        $tag_title = implode(",", $tags);
        $tag_to_add = $tag_title;
        $tag_to_organize = $tag_title;
        $sql = "'" . implode("','", $tags) . "'";
        $result=$db->query("SELECT $data_table.id,info,url FROM $data_table,$map_table,$tag_table WHERE $map_table.data_id=$data_table.id AND $map_table.tag_id=$tag_table.id AND $tag_table.name IN ($sql) GROUP BY $data_table.id HAVING COUNT($tag_table.name)=$count;");
    }
} else {
	$result=$db->query("SELECT id,info,url FROM $data_table;");
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
$().ready(resize);
$(window).load(resize);
$(window).resize(resize);
</script>

<div class="panel-heading">
	<a href="?">图片<?php if($tag_title) echo " - $tag_title"; ?></a>
</div>
<div class="panel-body text-center grid-div">
    <div style='margin:0 10px 0 10px;'>
        <div class='tags'>
            <a href='?tags=_NULL_'><div class='tag'>&nbsp;&nbsp;无标签&nbsp;&nbsp;</div></a>
            <?php
                if($tags_all) {
                    foreach($tags_all as $tag) {
                	    echo"
                	    <a href='?tags=$tag'>
                	    <div class='tag'>
                		    &nbsp;&nbsp;$tag". ($tag_to_add ? "&nbsp;<a href='?tags=$tag_to_add,$tag'>+</a>" : "") ."&nbsp;&nbsp;
                	    </div>
                	    </a>
                	    ";
                    }
                } else {
                	echo "
                        <div class='tag'>
                		    <font color='grey'>无</font>
                	    </div>
                    ";
                }
            ?>
        </div>
	</div>
    <div style='margin-top:16px;'>
	    <?php
	    echo "<a class='btn btn-info' href='?action=add&tags=$tag_to_add'>&nbsp;&nbsp;&nbsp;添加&nbsp;&nbsp;&nbsp;</a>";
	    if($result -> num_rows) {
	        echo "&nbsp;&nbsp;&nbsp;<a class='btn btn-info' href='?action=organize&tags=$tag_to_organize'>&nbsp;&nbsp;&nbsp;组织&nbsp;&nbsp;&nbsp;</a>";
	    }
	    ?>
	</div>
	<?php if($allow_edit) {
	    echo "<div style='margin-top:16px;'>";
	    echo "<a class='btn btn-info' href='?action=edittag&tag=$tag_to_add'>&nbsp;&nbsp;&nbsp;编辑标签&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;";
	    echo "<a class='btn btn-danger' href='?action=deletetag&tag=$tag_to_add'>&nbsp;&nbsp;&nbsp;删除标签&nbsp;&nbsp;&nbsp;</a>";
	    echo "</div>";
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
