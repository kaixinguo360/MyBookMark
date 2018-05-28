<?php

function show_text($text) {
echo "
<div class='text-center' style='margin-top:10px;'>
    <p>$text</p>
</div>
";
}

function jump_to_direct($target) {
exit("
<html>
    <script language='javascript'>
        location.href='$target';
    </script>
<html>
");
}

function jump_to($target) {
exit("
<script language='javascript'>
    location.href='$target';
</script>
");
}

function jump_with_text($text, $target) {
show_text($text);
jump_to($target);
}

function item($content, $link_url=NULL) {
if($link_url) {
    echo "<a href='$link_url'><div class='grid-item'>$content</div></a>";
} else {
    echo "<div class='grid-item'>$content</div>";
}
}

function item_image($url, $info, $link_url=NULL) {
$img_class = $info ? "grid-item-img" : "grid-item-img-only";
item("<img class='$img_class' width=100% src='$url' />"
    .($info ? "<div class='grid-item-info'><p>$info</p></div>" : "")
, $link_url);
}

function item_image_linked($url, $info, $link_url) {
$img_class = $info ? "grid-item-img" : "grid-item-img-only";
item("<a href='$link_url'><img class='$img_class' width=100% src='$url' /></a>"
    .($info ? "<div class='grid-item-info'><p>$info</p></div>" : "")
);
}

function list_tags($tags) {
if($tags) {
    foreach($tags as $tag) {
	    echo"
	    <a href='?tag=$tag'>
	    <div class='tag'>
		    &nbsp;&nbsp;$tag&nbsp;&nbsp;
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
}

function list_tag($tags) {
echo "<div class='tags'>";
list_tags($tags);
echo "</div>";
}

function list_tag_edit($tags, $fold = false) {
$tags_tmp = $tags;
$fold_tmp = $fold;
require("./page/common/tag_edit.php");
unset($tags_tmp);
unset($fold_tmp);
}

function run_sql($sql) {
    global $db;
    return $db -> query($sql);
}

function check_table($table, $sql) {
    global $db;
    $result = $db -> query("SHOW TABLES LIKE '$table';");
    if(!$result -> num_rows){
        $db -> query($sql);
    }
}

function set_item_tag($id, $tag) {
	global $db, $data_table, $map_table, $tag_table;
	
    if($tag) {
        $result = $db -> query("SELECT id FROM $tag_table WHERE name='$tag';");
        if(!$result -> num_rows) {
            $result = $db -> query("INSERT INTO $tag_table (name) VALUES ('$tag');");
            $result = $db -> query("SELECT id FROM $tag_table WHERE name='$tag';");
        }
        $array = $result -> fetch_array();
	    $tag_id = $array['id'];
        
        $result = $db -> query("insert into $map_table (data_id, tag_id) values ('$id', $tag_id);");
    }
}

function set_item_tags($id, $tags) {
	global $db, $data_table, $map_table, $tag_table;
	
	foreach($tags as $tag) {
        $tag = trim($tag);
        set_item_tag($id, $tag);
    }
}

function add_item($url, $info, $tags) {
	global $db, $data_table, $map_table, $tag_table;
	
	#Get ID
    $id = md5($url);
    
    # Add Data
    $result = $db -> query("insert into $data_table (id, url, info) values ('$id', '$url', '$info');");
    $added = $result;
    
    # Add Tag
    if($added && $tags) {
        set_item_tags($id, $tags);
    }
    
    return $added;
}

function update_item($id, $url, $info, $tags) {
	global $db, $data_table, $map_table, $tag_table;
	
	# Update Data From Database
    $result = $db -> query("UPDATE $data_table SET info='$info' WHERE id='$id';");
    $updated = $result;
    
    if($updated && $tags) {
        $result = $db -> query("DELETE FROM $map_table WHERE data_id='$id';");
        set_item_tags($id, $tags);
    }
    
    return $updated;
}

function delete_item($id) {
	global $db, $data_table, $map_table, $tag_table;
	
	$result = $db -> query("DELETE FROM $data_table WHERE id='$id';");
    $deleted = $result;
    if($deleted) {
        $result = $db -> query("DELETE FROM $map_table WHERE data_id='$id';");
    }
    
    return $deleted;
}
