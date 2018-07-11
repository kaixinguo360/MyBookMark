<?php

$data = $_POST['data'];

if($data) {
    # Clean Cookies
    setcookie("sort", NULL, time() - 100);
    setcookie("loadingimg", NULL, time() - 100);
    setcookie("updated", NULL, time() - 100);
    
    $data = json_decode($data, TRUE);
    $data_data = $data['data'];
    $data_tag = $data['tag'];
    $data_map = $data['map'];
    $data_album = $data['album'];
    $data_setting = $data['setting'];
    if($data_data) {
        echo "<br>Import Data...<br>";
        foreach($data_data as $id => $item) {
            $url = $item['url'];
            $info = $item['info'];
            $source = $item['source'];
            $time = $item['time'];
            if(!$db -> query("INSERT INTO $data_table (id, url, info, source, time) VALUES ('$id', '$url', '$info', '$source', '$time');")) {
                echo mysqli_error($db) . "<br>";
            } else {
                echo "Import Data '$id'<br>";
            }
        }
    }
    if($data_tag) {
        echo "<br>Import Tag...<br>";
        foreach($data_tag as $item) {
            $id = $item['id'];
            $name = $item['name'];
            $info = $item['info'];
            $nsfw = $item['nsfw'] ? $item['nsfw'] : 0;
            if(!$db -> query("INSERT INTO $tag_table (id, name, info, nsfw) VALUES ('$id', '$name', '$info', '$nsfw');")) {
                echo mysqli_error($db) . "<br>";
            } else {
                echo "Import Tag '$name'<br>";
            }
        }
    }
    if($data_map) {
        echo "<br>Import Map...<br>";
        foreach($data_map as $item) {
            $data_id = $item['data_id'];
            $tag_id = $item['tag_id'];
            if(!$db -> query("INSERT INTO $map_table (data_id, tag_id) VALUES ('${data_id}', '${tag_id}');")) {
                echo mysqli_error($db) . "<br>";
            } else {
                echo "Import Map '${data_id}-${tag_id}'<br>";
            }
        }
    }
    if($data_album) {
        echo "<br>Import Album...<br>";
        foreach($data_album as $item) {
            $id = $item['id'];
            $name = $item['name'];
            $tags = $item['tags'];
            $except = $item['except'];
            $info = $item['info'];
            $sort = $item['sort'];
            $loadingimg = $item['loadingimg'];
            $nsfw = $item['nsfw'] ? $item['nsfw'] : 0;
            if(!$db -> query("INSERT INTO $album_table (id, name, tags, except, info, sort, loadingimg, nsfw) VALUES ('$id', '$name', '$tags', '$except', '$info', '$sort', '$loadingimg', '$nsfw');")) {
                echo mysqli_error($db) . "<br>";
            } else {
                echo "Import Album '$name'<br>";
            }
        }
    }
    if($data_setting) {
        echo "<br>Import Setting...<br>";
        foreach($data_setting as $name => $value) {
            if(!$db -> query("INSERT INTO $setting_table (name, value) VALUES ('$name', '$value');")) {
                echo mysqli_error($db) . "<br>";
            } else {
                echo "Import Setting '$name'='$value'<br>";
            }
        }
    }
    
    exit("<br>操作完成<br><br><a class='btn btn-info' href='?'>返回</a><br><br>");
} else {
    $result = $db -> query("SELECT id, url, info, source, time FROM $data_table;");
    for ($i = 0; $i < $result -> num_rows; $i++) {
    	$array = $result -> fetch_array();
    	unset($item);
    	$item['url'] = $array['url'];
    	if($array['info']) {
    	    $item['info'] = $array['info'];
    	}
    	if($array['source']) {
    	    $item['source'] = $array['source'];
    	}
    	$item['time'] = $array['time'];
    	$data_data[$array['id']] = $item;
    }
    $result = $db -> query("SELECT id, name, info, nsfw FROM $tag_table;");
    for ($i = 0; $i < $result -> num_rows; $i++) {
    	$array = $result -> fetch_array();
    	unset($item);
    	$item['id'] = $array['id'];
    	$item['name'] = $array['name'];
    	if($array['info']) {
    	    $item['info'] = $array['info'];
    	}
    	$item['nsfw'] = $array['nsfw'];
    	$data_tag[$i] = $item;
    }
    $result = $db -> query("SELECT data_id, tag_id FROM $map_table;");
    for ($i = 0; $i < $result -> num_rows; $i++) {
    	$array = $result -> fetch_array();
    	unset($item);
    	$item['data_id'] = $array['data_id'];
    	$item['tag_id'] = $array['tag_id'];
    	$data_map[$i] = $item;
    }
    $result = $db -> query("SELECT id, name, tags, except, info FROM $album_table;");
    for ($i = 0; $i < $result -> num_rows; $i++) {
    	$array = $result -> fetch_array();
    	unset($item);
    	$item['id'] = $array['id'];
        $item['name'] = $array['name'];
        if($array['tags']) {
        	$item['tags'] = $array['tags'];
        }
        if($array['except']) {
            $item['except'] = $array['except'];
        }
        if($array['info']) {
            $item['info'] = $array['info'];
        }
        if($array['sort']) {
            $item['sort'] = $array['sort'];
        }
        if($array['loadingimg']) {
            $item['loadingimg'] = $array['loadingimg'];
        }
        $item['nsfw'] = $array['nsfw'];
    	$data_album[$i] = $item;
    }
    $result = $db -> query("SELECT name, value FROM $setting_table;");
    for ($i = 0; $i < $result -> num_rows; $i++) {
    	$array = $result -> fetch_array();
        if($array['value']) {
        	$data_setting[$array['name']] = $array['value'];
        }
    }
    unset($data);
    $data['data'] = $data_data;
    $data['tag'] = $data_tag;
    $data['map'] = $data_map;
    $data['album'] = $data_album;
    $data['setting'] = $data_setting;
    $data = json_encode($data, JSON_UNESCAPED_SLASHES);
}

?>

<div class="panel-heading">
	<a href="?">导出/导入</a>
</div>
<div class="panel-body" style="max-width:400px; margin: 0 auto;">
    <form method='post' action='?action=export'>
        <br>
        <textarea class='form-control inputbox' style='height:40%;' name='data' placeholder='Export/Import Data'><?php print_r($data); ?></textarea>
        <br>
        <input class='btn btn-info' type='submit' value='&nbsp;&nbsp;&nbsp;导入&nbsp;&nbsp;&nbsp;'>
    </form>
</div>
