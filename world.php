<?php
set_include_path(get_include_path() . PATH_SEPARATOR . './libs');

include_once 'configs.php';
include_once 'Misfit/Timeline.php';
include_once 'Misfit/Mongo.php';

$result = MisfitTimeline::getLatestTimeline();
$items = $result['items'];
$users = $result['users'];
$goals = $result['goals'];

?>
<a href="admin.php">USERS</a> |
<a href="leaderboard.php">LEADERBOARD</a> |
<a href="report.php">REPORT</a> |
<a href="group.php">GROUPS</a> |
<b>REST OF THE WORLD</b>
<hr>

<ol>
<?php foreach ($items as $item):
	$user = $users[$item['uid']->{'$id'}];
	$goal = $goals[$item['uid']->{'$id'}];
?>
	<li><?php echo MisfitTimeline::getMessage($item, $user, $goal);?></li>
<?php endforeach;?>
</ol>