<?php
set_include_path(get_include_path() . PATH_SEPARATOR . './libs');

include_once 'configs.php';
include_once 'Misfit/Users.php';
include_once 'Misfit/Exps.php';
include_once 'Misfit/Twitter.php';
include_once 'Misfit/Mongo.php';
include_once 'Misfit/Leaderboard.php';

$exps_db = new MisfitExps();

if (isset($_POST['groups'])) {
	foreach ($_POST['groups'] as $id => $name) {
		$sql = "
			INSERT INTO groups (id, name)
			VALUES ($id, '".$exps_db->escape($name)."')
			ON DUPLICATE KEY
			UPDATE name='".$exps_db->escape($name)."'
		";
		$exps_db->query($sql);
	}
}

$groups = $exps_db->getGroups();

?>
<a href="admin.php">USERS</a> |
<a href="leaderboard.php">LEADERBOARD</a> |
<a href="report.php">REPORT</a> |
<b>GROUPS</b>
<hr>
<form method="post">
<?php foreach ($groups as $group):?>
Group <?php echo $group['id_group'];?>: 
<input size=100 type="text" name="groups[<?php echo $group['id_group'];?>]" value="<?php echo $group['name'];?>" /><br>
<?php endforeach;?>
<input type="submit" />
</form>


