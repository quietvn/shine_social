<?php
set_include_path(get_include_path() . PATH_SEPARATOR . './libs');

include_once 'configs.php';
include_once 'Misfit/Users.php';
include_once 'Misfit/Twitter.php';
include_once 'Misfit/Mongo.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$f_id_group = isset($_GET['f_id_group']) ? $_GET['f_id_group'] : 0;

$users_db = new MisfitUsers();
$flash = array();
if (!empty($_POST) && !empty($_POST['email'])) {
	$result = $users_db->addNewUser($_POST);
	$flash = @$result['flash'];
}

if ($action == 'delete') {
	$users_db->delete($_GET['id']);
}

$users = $users_db->getAllByScore();
?>
<b>USERS</b> |
<a href="leaderboard.php">LEADERBOARD</a><hr>
<?php 
if (!empty($flash)):
?>
<div style='color: red'>
	<ul>
	<?php foreach ($flash as $message):?>
		<li><?php echo $message;?></li>
	<?php endforeach;?>
	</ul>
</div>
<?php endif;?>

<h3>Add new Shine user:</h3>
<form method="post">
	<div style="width: 150px;float: left">Server: </div>
	<select id="id_server" name="id_server">
		<option value="1">Production</option>
		<option value="2">Staging</option>
	</select><br>
	<div style="width: 150px;float: left">Email: </div><input type="text" name="email" id="email" /><br>
	<div style="width: 150px;float: left">Twitter handle: </div><input type="text" name="id_twitter" id="id_twitter" /><br>
	<div style="width: 150px;float: left">Group(s): </div><input type="text" name="groups" id="groups" /> 
		Comma separated value. Example: 1,2,3 OR 2<br>
	<input type="submit">
</form>
<hr>
Show users in Group: 
<select id="f_id_group" name="f_id_group" onchange="window.location='?f_id_group=' + this.value;">
	<option value="0">-all-</option>
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	<option value="6">6</option>
	<option value="7">7</option>
	<option value="8">8</option>
	<option value="9">9</option>
</select>

<br><br>
<table cellpadding="5px" cellspacing=0 border="1px;solid">
  <tr>
    <th>#</th>
    <th>Server</th>
    <th>Email</th>
    <th>Twitter</th>
    <th>Groups</th>
    <th>Old score</th>
    <th>Latest score</th>
    <th>Last sync</th>
    <th>Options</th>
  </tr>
  <?php $i=0;foreach ($users as $user):
  		$groups = explode(",",$user['groups']);
  		if (!empty($f_id_group) && !in_array($f_id_group, $groups)) continue;
  		$i++;?>
	  <tr>
	    <td><?php echo $i;?></td>
	    <td><?php echo $MONGO_CONFIG[ $user['id_server'] ]['name'];?></td>
	    <td><?php echo $user['email'];?></td>
	    <td><?php echo $user['id_twitter'];?></td>
	    <td><?php echo $user['groups'];?></td>
	    <td align="right"><?php echo round($user['old_score']/2.5);?></td>
	    <td align="right"><?php echo round($user['current_score']/2.5);?></td>
	    <td align="right"><?php echo $user['last_updated'];?></td>
	    <td>
	    	<a href="#" onclick="editUser(<?php echo $user['id_server']?>, '<?php echo $user['email']?>', '<?php echo $user['id_twitter']?>', '<?php echo $user['groups']?>')">Edit</a> | 
	    	<a href="#" onclick="deleteUser(<?php echo $user['id']?>, '<?php echo $user['email']?>')">Delete</a>
	    </td>
	  </tr>
  <?php endforeach;?>
</table>

<script language='javascript'>
	function editUser(id_server, email, id_twitter, groups) {
		document.getElementById('id_server').value=id_server;
		document.getElementById('email').value=email;
		document.getElementById('id_twitter').value=id_twitter;
		document.getElementById('groups').value=groups;

		document.getElementById('groups').focus();
	}

	function deleteUser(id, email) {
		var answer = confirm('Are you sure you want to delete user ' + email + '?');
		if (answer == true) {
			window.location = '?action=delete&id=' + id;
		}
	}

	document.getElementById('f_id_group').value=<?php echo $f_id_group;?>;
</script>