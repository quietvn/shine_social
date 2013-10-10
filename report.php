<?php
set_include_path(get_include_path() . PATH_SEPARATOR . './libs');

include_once 'configs.php';
include_once 'Misfit/Users.php';
include_once 'Misfit/Exps.php';
include_once 'Misfit/Twitter.php';
include_once 'Misfit/Mongo.php';
include_once 'Misfit/Leaderboard.php';

$f_id_exp = isset($_GET['f_id_exp']) ? $_GET['f_id_exp'] : 0;
$f_id_group = isset($_GET['f_id_group']) ? $_GET['f_id_group'] : 0;

$exps_db = new MisfitExps();
$exps = $exps_db->getExpGroups($f_id_exp, $f_id_group);
$groups = $exps_db->getGroups($f_id_exp);

$user_db = new MisfitUsers();
?>
<a href="admin.php">USERS</a> |
<b>LEADERBOARD</b><hr>
Experiment: 
<select id="f_id_exp" name="f_id_exp" onchange="window.location='?f_id_exp=' + this.value;">
	<option value="0">-all-</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
</select>

&nbsp; Group: 
<select id="f_id_group" name="f_id_group" onchange="window.location='?f_id_exp=<?php echo $f_id_exp?>&f_id_group=' + this.value;">
	<option value="0">-all-</option>
	<?php if ($f_id_exp !=0): 
			foreach ($groups as $group):?>
		<option value="<?php echo $group['id_group'];?>"><?php echo $group['id_group'];?></option>
	<?php 	endforeach;
		endif;?>
</select>

<?php 
	foreach ($exps as $exp):
		$id_exp = $exp['id_exp'];
		$id_group = $exp['id_group'];
		$start_date = $exp['participation_date'];
		$end_date = !empty($exp['participation_end_date']) ? $exp['participation_end_date'] : date('Y-m-d', time() - 24*3600);
?>
<h3>Experiment #<?php echo $id_exp;?> - Group #<?php echo $id_group;?> 
- From <?php echo $start_date;?> to <?php echo $end_date;?></h3>
<table cellpadding="5px" cellspacing=0 border="1px;solid">
  	<tr>
	    <th>#</th>
	    <th>Email</th>
	    <th>Twitter</th>
	    <th>AVG Points Before</th>
	    <th>AVG Points After</th>
	    <th>AVG Sync Before</th>
	    <th>AVG Sync After</th>
  	</tr>

<?php	$users = $user_db->getAllByIdGroup($exp['id_group']);
		$i=0;
		foreach ($users as $user):
  		$i++;?>
	<tr>
	    <td><?php echo $i;?></td>
	    <td><?php echo $user['email'];?></td>
	    <td><?php echo $user['id_twitter'];?></td>
	    <td><?php echo $user_db->getAvgPointsBefore($user, $start_date, $end_date);?></td>
	    <td><?php echo $user_db->getAvgPointsAfter($user, $start_date, $end_date);?></td>
	    <td><?php $sync = $user_db->getAvgSyncBefore($user, $start_date, $end_date);
	    		echo "{$sync[1]} - {$sync[2]} - {$sync[3]}"?></td>
	    <td><?php $sync = $user_db->getAvgSyncAfter($user, $start_date, $end_date);
				echo "{$sync[1]} - {$sync[2]} - {$sync[3]}"?></td>
    </tr>
<?php 	endforeach;?>
</table><br>
<?php endforeach;?>
<script language='javascript'>
	document.getElementById('f_id_group').value=<?php echo $f_id_group;?>;
	document.getElementById('f_id_exp').value=<?php echo $f_id_exp;?>;
</script>