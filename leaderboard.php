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

$exps = new MisfitExps();
$groups = $exps->getGroups($f_id_exp);

$boards_db = new MisfitLeaderboard();
$boards = $boards_db->getLeaderboard2($f_id_exp, $f_id_group);

$today = strtotime(date('Y-m-d'));
$last_week = $today - 6*24*3600;
?>

Experiment: 
<select id="f_id_exp" name="f_id_exp" onchange="window.location='?f_id_exp=' + this.value;">
	<option value="2">2</option>
	<option value="3">3</option>
</select>

Group: 
<select id="f_id_group" name="f_id_group" onchange="window.location='?f_id_exp=<?php echo $f_id_exp?>&f_id_group=' + this.value;">
	<option value="0">-all-</option>
	<?php foreach ($groups as $group):?>
		<option value="<?php echo $group['id_group'];?>"><?php echo $group['id_group'];?></option>
	<?php endforeach;?>
</select>

<br><br>
<?php foreach ($boards as $id_group => $users):?>
<h3>Group <?php echo $id_group;?></h3>

<?php if ($f_id_exp == 3): $first_user = array_slice($users, 0 , 1);?>
	
    Start date: <?php echo $first_user[0]['start_date'];?><br>
    Goal: <?php echo number_format($first_user[0]['goal']);?><br><br>
<?php endif;?>
    			
<table cellpadding="5px" cellspacing=0 border="1px;solid">
  <tr>
    <th>#</th>
    <th>Email</th>
    <th>Twitter</th>
    <?php for ($date = $last_week; $date<=$today; $date+=24*3600):?>
    	<th><?php echo date('Y-m-d', $date);?></th>
    <?php endfor;?>
  </tr>
  <?php $i=0;foreach ($users as $user):
  		$i++;?>
	  <tr>
	    <td><?php echo $i;?></td>
	    <td><?php echo $user['email'];?></td>
	    <td><?php echo $user['id_twitter'];?></td>
	    <?php for ($date = $last_week; $date<=$today; $date+=24*3600):
	    	$date_string = date('Y-m-d', $date) . " 00:00:00";
	    ?>
	    	
    		<td align="right">
    			<?php echo isset($user['daily_points'][$date_string]) ? number_format($user['daily_points'][$date_string]) : 'n/a';?><br>
    			<?php if ($f_id_exp == 3):?>
    				<span style='color:green'><?php echo isset($user['weekly_points'][$date_string]) ? number_format($user['weekly_points'][$date_string]) : 'n/a'?></span> 
    			<?php endif;?>
    		</td>
    	<?php endfor;?>
	  </tr>
  <?php endforeach;?>
</table>
<br>
<?php endforeach;?>

<script language='javascript'>
	document.getElementById('f_id_group').value=<?php echo $f_id_group;?>;
	document.getElementById('f_id_exp').value=<?php echo $f_id_exp;?>;
</script>