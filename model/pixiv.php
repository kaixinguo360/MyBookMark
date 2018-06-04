<?php

$urls = parse_url($url);

if($urls['host'] == 'i.pximg.net') {
	$p_id = substr($urls['path'], -15, -7);
	if(!$source)
	    $source = "https://www.pixiv.net/member_illust.php?mode=medium&illust_id=$p_id";
    $tags .= ",动漫";
} 
