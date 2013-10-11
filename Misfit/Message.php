<?php
class MisfitMessage {
	/////////////////////////////
	// Messages for experiment #2
	/////////////////////////////
	static public function ApassedB($a, $b) {
		$aPoint = round($a['current_score']/2.5);
		$points = round($a['current_score']/2.5) - round($b['current_score']/2.5);
		return "@{$a['id_twitter']} now has $aPoint points, passing @{$b['id_twitter']} by $points points!";
	}
	
	static public function AcreepedPassedB($a, $b) {
		$points = round($a['current_score']/2.5) - round($b['current_score']/2.5);
		return "@{$a['id_twitter']} just edged past @{$b['id_twitter']} with $points points!";
	}
	
	static public function AbehindB($a, $b) {
		$points = round($b['current_score']/2.5) - round($a['current_score']/2.5);
		return "@{$a['id_twitter']} is only $points points behind you @{$b['id_twitter']}! Better watch out...!";
	}
	
	static public function AfirstB($a, $b) {
		$points = round($a['current_score']/2.5);
		return "@{$a['id_twitter']} now has $points points, stealing first place from @{$b['id_twitter']}!";
	}
	
	static public function Apassed3B($a, $b) {
		$points = round($a['current_score']/2.5);
		$losers = '';
		foreach ($b as $loser)
			$losers .= " @{$loser['id_twitter']}";
		return "@{$a['id_twitter']} now has $points points and shot past$losers";
	}
	
	static public function AneckB($a, $b) {
		$points = round($a['current_score']/2.5);
		return "@{$a['id_twitter']} and @{$b['id_twitter']} are neck and neck with $points points!";
	}
	
	static public function summary2($rank, $user, $target_twitter) {
		$id_twitter = $user['id_twitter'];
		$points = $user['points'];
		
		$messages = array(
			8 => "@$id_twitter came in 8th place with $points points. Visit @$target_twitter to see the full leaderboard.",
			7 => "@$id_twitter came in 7th place with $points points. Visit @$target_twitter to see the full leaderboard.",
			6 => "@$id_twitter came in 6th place with $points points. Visit @$target_twitter to see the full leaderboard.",
			5 => "@$id_twitter came in 5th place with $points points, valiant effort. Visit @$target_twitter to see the full leaderboard.",
			4 => "@$id_twitter came in 4th place with $points points, just shy of the podium! Visit @$target_twitter to see the full leaderboard.",
			3 => "@$id_twitter came in 3rd place with $points points, nice one. Visit @$target_twitter to see the full leaderboard.",
			2 => "@$id_twitter came in 2nd place with $points points, great work! Visit @$target_twitter to see the full leaderboard.",
			1 => "@$id_twitter came in 1st place with $points points, way to go! Visit @$target_twitter to see the full leaderboard.",
		);
		
		return $messages[$rank];
	}
	
	/////////////////////////////
	// Messages for experiment #3
	/////////////////////////////
	static public function ApassedProgress($a, $progress, $remaining, $goal) {
		$tag = self::getProgressTag($progress);
		return "@{$a['id_twitter']} just helped push the team's progress past {$progress}0%. Only ".self::getPoints($remaining)." away from your goal of ".number_format($goal).". {$tag}";
	}
	
	static public function getProgressTag($progress) {
		$progress_tags = array(
			1 => "#gettingwarmedup",
			2 => "#offtotheraces",
			3 => "#livingonaprayer",
			4 => "#dontstopbelievin",
			5 => "#werehalfwaythere",
			6 => "#wecantstop",
			7 => "#lifeisahighway",
			8 => "#dontkillourvibe",
			9 => "#touchthesky",
		);
		return $progress_tags[$progress];
	}
	
	static public function AmetGoal($a) {
		return "Team challenge completed! Made it all the way across California.  #shine #likeaboss";
	}
	
	static public function wrapUp($exp, $total_percent, $highest, $weakest) {
		return "Team Challenge Summary: ".self::getPoints($exp['current_score'])." achieved ".self::getPercentage($total_percent)." of goal. Top contributor: @{$highest['id_twitter']}. Weakest link: @{$weakest['id_twitter']}";
	}
	
	static public function initGroup($exp) {
		$steps = 4*$exp['goal'];
		return "This week's challenge is to walk across California together (".self::getPoints($exp['goal'])." or ".self::getSteps($steps)."). Go for it!";
	}
	
	static public function dailyMetGoal() {
		return "Way to go, the team is on track to complete the challenge. Keep it up.";
	}
	
	static public function dailyBehindGoal($expected_percentage) {
		return "Step it up! To complete the challenge, the team will need to be ".self::getPercentage($expected_percentage)." more active than yesterday.";
	}
	
	static public function dailyMVP($highest_user, $weakest_user) {
		return "Way to go @{$highest_user['id_twitter']}, you were yesterday's point leader with ".self::getPoints($highest_user['points']).". @{$weakest_user['id_twitter']} can you beat that?";
	}
	
	static public function getPoints($points) {
		if ($points == 1) return "1 point";
		else return number_format($points) . " points";
	}
	
	static public function getSteps($steps) {
		if ($steps == 1) return "1 step";
		else return number_format($steps) . " steps";
	}
	
	static public function getPercentage($percentage) {
		return round($percentage) . "%";
	}
	
	/////////////////////////////
	// Messages for experiment #4
	/////////////////////////////
	static public function initChallenge($c) {
		return "#shinechallenge{$c['id']}: @{$c['twitter1']} @{$c['twitter2']} the game is on. Reply with '#starttimer {$c['id']}' to begin your {$c['duration']} minutes.";
	}
	
	static public function startUser($c, $i) {
		return "#shinechallenge{$c['id']}: @{$c["twitter$i"]} started timer at {$c["start$i"]}.";
	}
	
	static public function remindUser1($c, $i) {
		return "#shinechallenge{$c['id']}: @{$c["twitter$i"]} remember to sync your Shine to complete your turn!";
	}
	
	static public function remindUser2($c, $i) {
		return "#shinechallenge{$c['id']}: @{$c["twitter$i"]} remember to sync your Shine so you can see who won the challenge!";
	}
	
	static public function remindUserToStart($c, $i, $j) {
		return "#shinechallenge{$c['id']}: @{$c["twitter$i"]} completed their turn. @{$c["twitter$j"]} now it's your turn. Reply with '#starttimer {$c['id']}' to begin.";
	}
	
	static public function remindUserToSync($c, $i, $j) {
		return "#shinechallenge{$c['id']}: @{$c["twitter$i"]} completed their turn. @{$c["twitter$j"]} is still playing, remember to sync to complete your turn.";
	}
	
	static public function announceResult($c) {
		if ($c['points1'] > $c['points2']) {
			$i = 1;
		} else {
			$i = 2;
		}
		$j = 3 - $i;
		return "#shinechallenge{$c['id']} Round {$c['round']} result: @{$c["twitter$i"]} leads with {$c["points$i"]} points, @{$c["twitter$j"]} has {$c["points$j"]} points.";
	}
	
	static public function announceGrandResult($c, $result) {
		if ($result['total_points1'] > $result['total_points2']) {
			$i = 1;
		} else {
			$i = 2;
		}
		$j = 3 - $i;
		return "#shinechallenge{$c['id']} Game over. The winner is @{$c["twitter$i"]} with {$result["total_points$i"]} total points across 3 rounds. @{$c["twitter$j"]} had {$result["total_points$j"]} points.";
	}
}