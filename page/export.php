<?php

$data = $_POST['data'];

if($data) {
    $data = json_decode($data, TRUE);
    $data_data = $data['data'];
    $data_tag = $data['tag'];
    $data_map = $data['map'];
    if($data_data) {
        echo "<br>Import Data...<br>";
        foreach($data_data as $id => $item) {
            $url = $item['url'];
            $info = $item['info'];
            $time = $item['time'];
            if(!$db -> query("INSERT INTO $data_table (id, url, info, time) VALUES ('$id', '$url', '$info', '$time');")) {
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
            if(!$db -> query("INSERT INTO $tag_table (id, name) VALUES ('$id', '$name');")) {
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
    exit("<br>操作完成<br><br><a class='btn btn-info' href='?'>返回</a><br><br>");
} else {
    $result = $db -> query("SELECT id, url, info, time FROM $data_table;");
    for ($i = 0; $i < $result -> num_rows; $i++) {
    	$array = $result -> fetch_array();
    	unset($item);
    	$item['url'] = $array['url'];
    	$item['info'] = $array['info'];
    	$item['time'] = $array['time'];
    	$data_data[$array['id']] = $item;
    }
    $result = $db -> query("SELECT id, name FROM $tag_table;");
    for ($i = 0; $i < $result -> num_rows; $i++) {
    	$array = $result -> fetch_array();
    	unset($item);
    	$item['id'] = $array['id'];
    	$item['name'] = $array['name'];
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
    unset($data);
    $data['data'] = $data_data;
    $data['tag'] = $data_tag;
    $data['map'] = $data_map;
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