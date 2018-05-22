<?php

# Get Tags
$result = $db -> query("SELECT name FROM $tag_table;");

# Check Data
if(!$result)  {
	exit('Error:<br>'.mysqli_error($db));
}

# Decode Tags
for ($i = 0; $i < $result -> num_rows; $i++) {
	$tags[$i] = $result -> fetch_array()['name'];
}

?>

<script src="//unpkg.com/masonry-layout@4/dist/masonry.pkgd.js"></script>
<script>
function resize() {
    $('.tags').masonry({
        gutter: 8,
        itemSelector: '.tag',
        fitWidth: true,
    });
}
$().ready(resize);
$(window).resize(resize);
</script>

<div class="panel-heading">
	<a href="?">标签</a>
</div>
<div class="panel-body text-center grid-div">
	<div class='info' >
        <div style='margin:0 10px 0 10px;'>
        <?php list_tag($tags); ?>
	    </div>
    </div>
</div>
