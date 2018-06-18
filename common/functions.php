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
    echo "<a href='$link_url'><div class='grid-item lazyload' data-expand='1000'>$content</div></a>";
} else {
    echo "<div class='grid-item'>$content</div>";
}
}

function item_image($url, $info, $link_url=NULL, $loadingimg) {
$img_class = $info ? "grid-item-img" : "grid-item-img-only";
item("<img class='lazyload $img_class'". ($loadingimg ? " src='$loadingimg'" : "") ." data-src='$url'/>"
    .($info ? "<div class='grid-item-info'><p>$info</p></div>" : "")
, $link_url);
}

function item_image_linked($url, $info, $link_url) {
$img_class = $info ? "grid-item-img" : "grid-item-img-only";
item("<a href='$link_url'><img class='lazyload $img_class' style='width:100%;min-height:100px;' src='$url'/></a>"
    .($info ? "<div class='grid-item-info'><p>$info</p></div>" : "")
);
}

function list_tags($tags) {
if($tags) {
    foreach($tags as $tag) {
	    echo"
	    <a href='?tags=$tag'>
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

function add_item($url, $info, $tags = NULL, $source = NULL) {
	global $db, $data_table, $map_table, $tag_table;
	
	#Get ID
    $id = md5($url);
    
    # Add Data
    $result = $db -> query("insert into $data_table (id, url, info, source) values ('$id', '$url', '$info', '$source');");
    $added = $result;
    
    # Add Tag
    if($added && $tags) {
        set_item_tags($id, $tags);
    }
    
    return $added;
}

function update_item($id, $url, $info, $tags, $source) {
	global $db, $data_table, $map_table, $tag_table;
	
	# Update Data From Database
    $result = $db -> query("UPDATE $data_table SET info='$info' WHERE id='$id';");
    $result = $db -> query("UPDATE $data_table SET source='$source' WHERE id='$id';");
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

function explode_trim($str) {
	$tags = explode(",", $str);
    $count = count($tags);
    
    for($i = 0; $i < $count; $i++) {
        $tags[$i] = trim($tags[$i]);
        if(!$tags[$i]) {
            unset($tags[$i]);
        }
    }
    return $tags;
}

function get_items($tags, $except, $album=NULL) {
global $db, $data_table, $map_table, $tag_table;

if($album) {
	if($album != "_NULL_") {
		$result_album = run_sql("SELECT id, info FROM $album_table WHERE name='$album';");
	    if($result_album) {
		    $album_id = $result_album -> fetch_array()['id'];
		    $album_sql = " AND $data_table.id IN (SELECT $amap_table.data_id FROM $amap_table WHERE album_id='$album_id')";
	    }
	} 
}

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
return $result;
}