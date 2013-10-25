<?php
include_once 'Misfit/DbModelAbstract.php';
class MisfitTimeline extends MisfitDbModelAbstract {
	
	public static function getLatestTimeline($last_time = null) {
		$mongo = MisfitMongo::getInstance(1)->collection;
		$ts = array('$lt' => 1389101723);
		if (!empty($last_time)) {
			$ts['$gt'] = $last_time;
		}
		$query = array( 
			'$or' => array(
						array('itype' => 3),
						array('itype' => 4,
							'data.eventType' => array('$in' => array(2, 4, 5, 6, 7))))
				 );

		$items = $mongo
			->timelines_items
			->find($query, 
				array(
					"data.eventType"=>1, 
					"itype"=>1, 
					'data.info.streakNumber'=>1, 
					'data.milestoneType'=>1,
					'uid'=>1, 
					'ts'=>1))
			->sort(array('_id' => -1))->limit(50);
		
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
		$points = isset($goal['val']) ? round($goal['val'] / 2.5) : 0;
		$streakNumber = @isset($item['data']['info']['streakNumber']) ? $item['data']['info']['streakNumber'] : '';
		
		list($handle, $domain) = explode("@", $user['email']);
		
		if ($item['itype'] == 4) {
			switch ($item['data']['eventType']) {
				case 2:
					return "{$handle} hit a goal of {$points} points.";
					break;
				case 4:
					return "{$handle} passed 150% of his goal of {$points} points.";
					break;
				case 6:
					return "{$handle} passed 200% of his goal of {$points} points.";
					break;
				case 7:
					return "{$handle} is on a {$streakNumber} day streak!";
					break;
				case 5:
					return "{$handle} just reached a new personal best with {$points} points.";
					break;
			}
		} elseif ($item['itype'] == 3) {
			$marathons = array(2, 6, 12);
			$milestone = @isset($item['data']['milestoneType']) ? $item['data']['milestoneType'] : 0;
			$marathon = $marathons[$milestone];
			return "{$handle} hit a new achievement ... {$marathon} marathons!";
			break;
		} else {
			return '';
		}
		
	}
}
