<?php
include_once 'Misfit/DbModelAbstract.php';
class MisfitTimeline extends MisfitDbModelAbstract {
	
	public static function getLatestTimeline() {
		$mongo = MisfitMongo::getInstance(1)->collection;
		$query = array('itype' => 4);
		$items = $mongo->timelines_items->find($query)->sort(array('ts' => -1))->limit(20);
		
		$result = array();
		$uids = array();
		while ($items->hasNext()) {
			$item = $items->getNext();
			$uids[] = $item['uid'];		
			$result['items'][] = $item;
		}
		
		$query = array( "_id" => array('$in' => $uids));
		$users = $mongo->users->find($query);
		while ($users->hasNext()) {
			$user = $users->getNext();
			$result['users'][$user['_id']->{'$id'}] = $user;
		}
		
		$query = array( "uid" => array('$in' => $uids));
		$goals = $mongo->goals->find($query);
		while ($goals->hasNext()) {
			$goal = $goals->getNext();
			$result['goals'][$goal['uid']->{'$id'}] = $goal;
		}
		
		return $result;
	}
	
	public static function getMessage($item, $user, $goal) {
		$points = isset($goal['prgd']['points']) ? $goal['prgd']['points'] : 0;
		switch ($item['data']['eventType']) {
			case 2:
				return "{$user['email']} hit a goal of {$points} points.";
				break;
			case 4:
				return "{$user['email']} passed 150% of his goal of {$points} points.";
				break;
			case 6:
				return "{$user['email']} passed 200% of his goal of {$points} points.";
				break;
			case 7:
				return "{$user['email']} is on a {$item['info']['streakNumber']} day streak!";
				break;
			case 5:
				return "{$user['email']} just reached a new personal best with {$points} points.";
				break;
		}
		
	}
}
