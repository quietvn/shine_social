<?php
set_include_path(get_include_path() . PATH_SEPARATOR . './libs');

include_once 'configs.php';
include_once 'Misfit/Users.php';
include_once 'Misfit/Exps.php';
include_once 'Misfit/Twitter.php';
include_once 'Misfit/Mongo.php';
include_once 'Misfit/Leaderboard.php';

$f_id_exp = isset($_GET['f_id_exp']) ? $_GET['f_id_exp'] : 2;
$f_id_group = isset($_GET['f_id_group']) ? $_GET['f_id_group'] : 0;

$exps_db = new MisfitExps();
$exps = $exps_db->getExpGroups($f_id_exp, $f_id_group);
$groups = $exps_db->getGroups($f_id_exp);

$user_db = new MisfitUsers();
?>
<a href="admin.php">USERS</a> |
<a href="leaderboard.php">LEADERBOARD</a> |
<b>REPORT</b> |
<a href="group.php">GROUPS</a>
<hr>

<span style='color:green'>Green: before exp</span>
 |
<span style='color:red'>Red: during exp</span>
<br>
Experiment: 
<select id="f_id_exp" name="f_id_exp" onchange="window.location='?f_id_exp=' + this.value;">
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
</select>

&nbsp; Group: 
<select id="f_id_group" name="f_id_group" onchange="window.location='?f_id_exp=<?php echo $f_id_exp?>&f_id_group=' + this.value;">
	<option value="0">-all-</option>
	<?php if ($f_id_exp !=0): 
			foreach ($groups as $group):?>
		<option value="<?php echo $group['id_group'];?>"><?php echo $group['id_group'];?> - <?php echo $group['name'];?></option>
	<?php 	endforeach;
		endif;?>
</select>
<br>

<?php 
	$end_date = date('Y-m-d', time() - 24*3600);
	foreach ($exps as $exp):
		$id_exp = $exp['id_exp'];
		$id_group = $exp['id_group'];
		$name = $exp['name'];
//		$start_date = $exp['participation_date'];
//		$end_date = !empty($exp['participation_end_date']) ? $exp['participation_end_date'] : date('Y-m-d', time() - 24*3600);
?>
<h3>Experiment #<?php echo $id_exp;?> - Group #<?php echo $id_group;?> - <?php echo $name;?></h3>
<table cellpadding="5px" cellspacing=0 border="1px;solid">
  	<tr>
	    <th>#</th>
	    <th>Email</th>
	    <th>Twitter</th>
	    <th>Start date</th>
	    <th>AVG Points</th>
	    <th>AVG Manual Sync</th>
	    <th>AVG FG Sync</th>
	    <th>AVG BG Sync</th>
  	</tr>

<?php	$users = $user_db->getAllByIdGroup($exp['id_group']);
		$i=0;
		foreach ($users as $user):
	  		$i++;
	  		$start_date = $user['start_date'];
	  		$points_before = $user_db->getAvgPointsBefore($user, $start_date, $end_date);
	  		$points_after = $user_db->getAvgPointsAfter($user, $start_date, $end_date);
	  		$sync_before = $user_db->getAvgSyncBefore($user, $start_date, $end_date);
	  		$sync_after = $user_db->getAvgSyncAfter($user, $start_date, $end_date);?>
	<tr>
	    <td><?php echo $i;?></td>
	    <td><?php echo $user['email'];?></td>
	    <td><?php echo $user['id_twitter'];?></td>
	    <td><?php echo $user['start_date'];?></td>
	    <td>
	    	<div style='width:40px;float:left;color:green'><?php echo $points_before?></div>
	    	|
	    	<span style='color:red'><?php echo $points_after?></span>
	    </td>
	    <?php for($j=1; $j<=3; $j++):?>
	    	<td>
	    		<div style='width:40px;float:left;color:green'><?php echo $sync_before[$j];?></div>
		    	|
		    	<span style='color:red'><?php echo $sync_after[$j];?></span>
	    	</td>
	    <?php endfor;?>
    </tr>
<?php 	endforeach;?>
</table><br>
<?php endforeach;?>
<script language='javascript'>
	document.getElementById('f_id_group').value=<?php echo $f_id_group;?>;
	document.getElementById('f_id_exp').value=<?php echo $f_id_exp;?>;
</script>