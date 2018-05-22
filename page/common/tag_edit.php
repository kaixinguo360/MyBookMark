<script>
function resize() {
    $('.tags').masonry({
        gutter: 8,
        itemSelector: '.tag',
        fitWidth: true,
    });
}

$(document).ready(function(){
<?php
if(!$fold_tmp) {
    echo '$("#tag_box").show();$("#edit_tag").hide();resize();';
}
?>
    $("#edit_tag").click(function(){
        $("#tag_box").slideToggle("normal", resize);
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
    });
});
</script>

<div>
    <input hidden=true id='tags' type='text' name="tags" value="" />
    <a class="btn btn-info" id="edit_tag">编辑标签</a>
</div>
<div id="tag_box" hidden=true style='margin:20px;'>
    <div class='tags' id='tags_div'>
        <?php
        foreach($tags_tmp as $tag_name => $tag_status) {
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


