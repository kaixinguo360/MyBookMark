<?php

# Get Data
$name = $_GET['name'];
$info = $_GET['info'];

# Check Data
if($name) {
    # Add to Database
    $added = run_sql("INSERT INTO $album_table (name, info) VALUES ('$name', '$info');");
}

?>

<script>
alert(window.clipboardData.getData("text"));
</script>

<div class="panel-heading">
	<a href="?">添加图集</a>
</div>
<div class="panel-body" style="max-width:400px; margin: 0 auto;">
    <?php 
        if($name) {
            $status = $added ? "成功" : "失败<br>".mysqli_error($db);
            jump_with_text("添加" . $status, "?");
        } else {
            echo "
    <br>
    <form method='get'>
        <input name='action' value='addalbum' hidden=true/>
        <div class='form-group' id='inputdiv'>
            <input class='form-control inputbox' id='name' placeholder='Name' type='text' name='name' value='$name' autocomplete='off' />
        </div>
        <div class='form-group' id='inputdiv'>
            <textarea class='form-control inputbox' id='info' placeholder='Info' type='text' name='info' style='height:40%;'>$info</textarea>
        </div>
		<input class='btn btn-info' type='submit' value='&nbsp;&nbsp;&nbsp;添加&nbsp;&nbsp;&nbsp;'>
	</form>
	";}
    ?>
</div>
