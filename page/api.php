<?php

# Get Data
$todo = $_GET['todo'];
$api_id = $_GET['t_api'];
$api_password = $_GET['t_pw'];
$t_actions = $_GET['t_actions'];

# Check Data
if($todo == "create" && $api_id) {
    $db -> query("INSERT INTO $api_table (api_id, password, user) VALUES ('$api_id', '$api_password', '$user');");
	$db -> query("DELETE FROM $user_api_table WHERE api_id='$api_id';");
	
	$t_actions = explode(",", $t_actions);
	foreach($t_actions as $t_action) {
	    $t_action = trim($t_action);
	    $db -> query("INSERT INTO $user_api_table (api_id, action) VALUES ('$api_id', '$t_action');");
	}
} else if($todo == "remove") {
    $db -> query("DELETE FROM $api_table WHERE api_id='$api_id';");
    $db -> query("DELETE FROM $user_api_table WHERE api_id='$api_id';");
}

$result = $db->query("SELECT api_id, password FROM $api_table WHERE user='$user';");
for ($i = 0; $i < $result -> num_rows; $i++) {
    $array = $result -> fetch_array();
    $api['api_id'] = $array['api_id'];
    $api['password'] = $array['password'];
    
    $result_actions = $db -> query("SELECT action FROM $user_api_table WHERE api_id='". $api['api_id'] ."';");
    for ($j = 0; $j < $result_actions -> num_rows; $j++) {
    	$api['actions'][$j] = $result_actions -> fetch_array()['action'];
    }

    $apis[$i] = $api;
}

?>

<div class="panel-heading">
	<a href="?">API设置</a>
</div>
<div class="panel-body" style="max-width:400px; margin: 0 auto;">
    <div class="panel-body">
        <div>
            <div><b>已有API</b></div>
            <br>
            <div>
<?php
foreach($apis as $api) {
    echo "<div><p>";
    echo $api['api_id'] . " - " . $api['password'] . " - " . implode(", ", $api['actions']);
    echo "&nbsp;&nbsp;&nbsp;<a href='?action=api&todo=remove&t_api=". $api['api_id'] ."'>删除</a>";
    echo "</p></div>";
}
?>
            </div>
        </div>

    </div>
    <br>
    <div><b>添加API</b></div>
    <form method='get'>
        <input name='action' value='api' hidden=true/>
        <input name='todo' value='create' hidden=true/>
        <div class='form-group' id='inputdiv'>
            <input class='form-control inputbox' placeholder='API ID' type='text' name='t_api' />
        </div>
        <div class='form-group' id='inputdiv'>
            <input class='form-control inputbox' placeholder='Password' type='text' name='t_pw' />
        </div>
        <div class='form-group' id='inputdiv'>
            <input class='form-control inputbox' placeholder='Actions' type='text' name='t_actions' />
        </div>
		<input class='btn btn-info' type='submit' value='&nbsp;&nbsp;&nbsp;添加&nbsp;&nbsp;&nbsp;'>
	</form>
</div>
