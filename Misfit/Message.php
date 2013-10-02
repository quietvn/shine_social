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
		return "@{$a['id_twitter']} just creeped past @{$b['id_twitter']} with $points points!";
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
	
	/////////////////////////////
	// Messages for experiment #3
	/////////////////////////////
	static public function ApassedProgress($a, $progress) {
		$tag = self::getProgressTag($progress);
		return "@{$a['id_twitter']}  just helped push the team's progress past {$progress}0%. #{$tag}";
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
		return "Team challenge completed!  Made it all the way across California.  #shine #likeaboss";
	}
	
	static public function wrapUp($exp, $total_percent, $highest, $weakest) {
		return "Team Challenge Summary: {$exp['current_score']} points achieved {$total_percent}% of goal. Top contributor: @{$highest['id_twitter']}. Weakest Link: @{$weakest['id_twitter']}";
	}
	
	static public function initGroup($exp) {
		return "This week's challenge is to walk across California together ({$exp['goal']} pts or {$exp['goal']} steps). Go for it!";
	}
}