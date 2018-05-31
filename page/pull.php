<?php

$url = $_GET['url'];

if($url) {
    require("./lib/simple_html_dom.php");
    $html = file_get_html($url);
}

?>

<div class="panel-heading">
	<a href="?">保存</a>
</div>

<?php
if($url) {
    echo("
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
    ");
    echo "<div class='panel-body text-center grid-div'><div class='grid' style='margin-top:16px;'>";
    foreach($html->find('img') as $element) {
        $src = $element->src;
        $alt = $element->alt;
        item_image($src, $alt, "?action=add&url=$src&info=$alt&source=$url&need_edit=true");
    }
    echo "</div></div>";
} else {
    echo("
<div class='panel-body' style='max-width:400px; margin: 0 auto;'>
    <form method='get'>
        <input name='action' value='pull' hidden=true/>
        <div class='form-group' id='inputdiv'>
            <input class='form-control inputbox' id='url' placeholder='URL' type='text' name='url' autocomplete='off' />
        </div>
        <input class='btn btn-info' type='submit' value='&nbsp;&nbsp;&nbsp;添加&nbsp;&nbsp;&nbsp;'>
    </form>
</div>
    ");
}
?>
