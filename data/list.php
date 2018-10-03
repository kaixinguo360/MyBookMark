<?php

include_once("./data/classes/Imgs.class.php");

# Get Params
$tags = $_GET["tags"];
$except = $_GET["except"];
$album = $_GET["album"];
$nsfw = $_GET["nsfw"];
$sort = $_GET["sort"];
$location = $_GET["location"];
$length = $_GET["length"] ? $_GET["length"] : 10;

$result = Imgs::getImgs("id, url, info", $album, $tags, $except, $nsfw, $sort, $location, $length);

for ($i = 0; $i < $result -> num_rows; $i++) {
	$data[$i] = $result -> fetch_assoc();
	if(mb_strlen($data[$i]['info']) > 50) {
        $data[$i]['info'] = mb_substr($data[$i]['info'], 0, 50) . "...";
    }
	/*
	$id = $array['id'];
	$url = $array['url'];
	$info = $array['info'];
    $info = strip_tags($info);
    if(mb_strlen($info) > 50) {
        $info = mb_substr($info, 0, 50) . "...";
    }
	item_image($url, $info, "?action=img&id=$id", $loadingimg);
	*/
}

$data = json_encode($data, JSON_UNESCAPED_SLASHES);
echo $data;