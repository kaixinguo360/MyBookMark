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
