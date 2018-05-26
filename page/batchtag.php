<?php

$imgs = $_POST["imgs"];
$tag = $_GET["tag"];

if(isset($_POST["imgs"])) {
    
    $result = run_sql("SELECT id FROM $tag_table WHERE name='$tag';");
    if($result -> num_rows) {
        $array = $result -> fetch_array();
	    $tag_id = $array['id'];
	    run_sql("DELETE FROM $map_table WHERE tag_id='$tag_id'");
    }
    
    $imgs = explode(",", $imgs);
    if($imgs) {
        foreach($imgs as $img) {
            $img = trim($img);
            set_item_tag($img, $tag);
        }
    }
    
    jump_with_text("操作完成!", "?");
} else {
    $result=$db->query("SELECT $data_table.id FROM $data_table,$map_table,$tag_table WHERE $map_table.data_id=$data_table.id AND $map_table.tag_id=$tag_table.id AND $tag_table.name='$tag';");
    
    for ($i = 0; $i < $result -> num_rows; $i++) {
        $imgs_selected[$result -> fetch_array()['id']] = TRUE;
    }
    
    $result=$db->query("SELECT id,info,url FROM $data_table;");
    
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
    	$img['is_selected'] = $imgs_selected[$img['id']];
        
        $imgs[$i] = $img;
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
    $(".grid-item").each(function() {
        if(imgs.get(this.id)) {
            imgs_text += this.id + ",";
            $(this).css("margin", "5px");
            $(this).css("margin-bottom", "3px");
            $(this).css("box-shadow", "1px 2px 4px #4444ff");
        } else {
            $(this).css("margin", "0");
            $(this).css("margin-bottom", "8px");
            $(this).css("box-shadow", "2px 4px 6px #888888");
        }
    });
    $("#imgs").val(imgs_text);
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
});
</script>

<div class="panel-heading">
	<a href="?">批量设置标签 - <?php echo $tag; ?></a>
</div>

<div class="panel-body text-center grid-div">
    <form method='post' action='?action=batchtag&tag=<?php echo $tag; ?>'>
    <input name='imgs' id='imgs' value='' hidden=true/>
    <input class='btn btn-info' type='submit' value='&nbsp;&nbsp;&nbsp;确定&nbsp;&nbsp;&nbsp;'>
	<br>
	<br>
	<div class="grid">

<?php

foreach($imgs as $img) {
	$id = $img['id'];
	$url = $img['url'];
	$info = $img['info'];
    
    $img_class = $info ? "grid-item-img" : "grid-item-img-only";
    $content = "<img class='$img_class' width=100% src='$url' />"
        .($info ? "<div class='grid-item-info'><p>$info</p></div>" : "");
    echo "<div class='grid-item-out'><div class='grid-item' id='$id'>$content</div></div>";
}


?>
    </div>
    </form>
</div>