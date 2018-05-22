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
item("<img width=100% src='$url' alt='$info' />"
    .($info ? "<div class='grid-item-info'><p>$info</p></div>" : "")
, $link_url);
}

function item_image_linked($url, $info, $link_url) {
item("<a href='$link_url'><img width=100% src='$url' alt='$info' /></a>"
    .($info ? "<div class='grid-item-info'><p>$info</p></div>" : "")
);
}

function list_tags($tags) {
if($tags) {
    foreach($tags as $tag) {
	    echo"
	    <a href='?tag=$tag'>
	    <div class='tag'>
		    $tag
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