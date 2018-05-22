<?php
# Get Data
$tags = $_GET['tags'];
$info = $_GET['info'];
$url = $_GET['url'];
$id = $_GET['id'];

# Check Data
if(isset($_GET['info'])) {
    # Add Data To Database
    $result = $db -> query("UPDATE $data_table SET info='$info' WHERE id='$id';");
    $updated = $result;
    if($updated && isset($_GET['tags'])) {
        $result = $db -> query("DELETE FROM $map_table WHERE data_id='$id';");
        $tags = explode(",", $tags);
        foreach($tags as $tag) {
            $tag = trim($tag);
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
    }
} else {
    # Get All Tags
    $result = $db -> query("SELECT name FROM $tag_table;");
    for ($i = 0; $i < $result -> num_rows; $i++) {
    	$tags[$result -> fetch_array()['name']] = "";
    }

    # Get Tags Of Img
    $result = $db -> query("SELECT info,url FROM $data_table WHERE id='$id';");
	$array = $result -> fetch_array();
	$url = $array['url'];
	$info = $array['info'];
	
	$result = $db -> query("SELECT $tag_table.name FROM $map_table,$tag_table WHERE $map_table.tag_id=$tag_table.id AND $map_table.data_id='$id';");
	for ($i = 0; $i < $result -> num_rows; $i++) {
		$tags[$result -> fetch_array()['name']] = "true";
	}
}

?>

<script>
function resize() {
    $('.tags').masonry({
        gutter: 8,
        itemSelector: '.tag',
        fitWidth: true,
    });
}

$(document).ready(function(){
    $("#edit_tag").click(function(){
        $(".box").slideToggle("normal", resize);
    });
    $("#add_tag").click(function(){
        $("#add_div").slideToggle("normal", resize);
    });
    $("#add_tag_btn").click(function() {
        var new_tags = $("#add_tag_text").val();
        $("#add_tag_text").val("");
        $("#add_div").slideToggle("normal", resize);
        
        var hasAdded = false;
        
        $(".tag-checkbox").each(function() {
            if($(this).attr('name') == new_tags) {
                hasAdded = true;
            }
        });
        
        if(hasAdded) {
            return;
        }
        
        var new_item = $("<div class='tag'><input class='tag-checkbox' type='checkbox' name='"
        + new_tags
        + "' id='tag_" + new_tags
        + "' checked='checked'/><label for='tag_" + new_tags
        + "'>" + new_tags
        + "</label></div>");
        
        $(".tags")
            .prepend(new_item)
            .masonry( 'prepended', new_item);
        
    });
    $("form").submit(function() {
        var tags = "";
        $(".tag-checkbox").each(function(){
            if(this.checked) {
                tags += $(this).attr('name') + ",";
            }
        });
        $(".tags").remove();
        $("#tags").val(tags);
        alert($("#tags").val());
    });
});
</script>

<div>
    <input hidden=false id='tags' type='text' name="tags" value="" />
    <a class="btn btn-info" id="edit_tag">编辑</a>
    <input class="btn btn-info" type="submit" value="提交">
</div>
<div class="box" hidden=true style='margin:20px;'>
    <div class='tags' id='tags_div'>
        <?php
        foreach($tags as $tag_name => $tag_status) {
            $tag_status = $tag_status ? "checked" : "";
            echo"
            <div class='tag'>
                <input class='tag-checkbox' type='checkbox' name='$tag_name' id='tag_$tag_name' $tag_status/>
                <label for='tag_$tag_name'>$tag_name</label>
            </div>
            ";
        }
        ?>
        <div class='tag'>
            <div id='add_div' hidden=true>
                <input type='text' id='add_tag_text' />
                <a class="btn btn-info" id="add_tag_btn">添加</a>
            </div>
            <div id='add_tag'>
                <label>+</label>
            </div>
        </div>
    </div>
</div>


