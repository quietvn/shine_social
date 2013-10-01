<?php
class MisfitMessage {
	static public function ApassedB($a, $b) {
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
		return "@{$a['id_twitter']} and @{$b['id_twitter']} are neck to neck with $points points!";
	}
}