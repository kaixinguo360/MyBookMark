<div class="text-right" style="margin:0 20px 0 20px;">
	<a class="btn" href="?action=logout">登出</a>&nbsp;&nbsp;&nbsp;
	<a class="btn" href="?action=set">设置</a>&nbsp;&nbsp;&nbsp;
    <a class="btn btn-default" href="?">主页</a>&nbsp;&nbsp;&nbsp;
	<div class="btn btn-info" id="add-btn">添加</div>&nbsp;&nbsp;&nbsp;
</div>

<div class="text-right dialog" style="margin:20px 20px 0 20px;" hidden=true>
    <form class="text-right table-responsive" method="get">
        <input name='action' id="action" value='pull' hidden=true/>
        <input name='need_edit' value='true' hidden=true/>
        <table class="text-right" style="margin: 16px auto 0 auto">
            <tr>
                <td>
                    <input class='form-control inputbox' placeholder='URL' type='text' name='url' autocomplete='off' style="min-width:100px;" />&nbsp;&nbsp;&nbsp;
                </td>
                <td>
                    <input class="form-control btn btn-info" id='from_url' type="submit" value="来自网页" />&nbsp;&nbsp;&nbsp;
                </td>
                <td>
                    <input class="form-control btn btn-info" id='single_img' type="submit" value="单个图片" />&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
    </form>
</div>
<script>
	$("#from_url").click(function() {
        $("#action").val("pull");
    });
    $("#single_img").click(function() {
        $("#action").val("add");
    });
    $("#add-btn").click(function() {
        $(".dialog").slideToggle();
    });
</script>