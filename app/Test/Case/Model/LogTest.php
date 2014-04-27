<?php

App::uses('TestUtils', 'Lib');

class LogTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateLogs();
		$this->utils->generateLogsNotReviewed();
	}

	public function testBeforeSave() {
		$playerId = DEVELOPER_1_ID;
		$activity = $this->utils->Activity->find('first');
		$data = array(
			'Log' => array(
				'activity_id' => $activity['Activity']['id'], 
				'acquired' => date('Y-m-d'),
				'description' => 'anything',
				'player_id' => $playerId
		));

		$this->utils->Log->create();
		$saved = $this->utils->Log->save($data);
		$log = $this->utils->Log->findById($this->utils->Log->id);

		$this->assertEquals($activity['Activity']['domain_id'], $log['Log']['domain_id']);
		$this->assertEquals($activity['Activity']['xp'], $log['Log']['xp']);
	}

	public function testReviewedIncrementedActivityReportedCounter() {
		$log = $this->utils->Log->find('first', array('conditions' => 'Log.reviewed IS NULL'));
		$this->assertNotEmpty($log);
		$this->utils->Log->review($log['Log']['id']);
		$activity = $this->utils->Activity->findById($log['Log']['activity_id']);

		$this->assertEquals($activity['Activity']['reported'], $log['Activity']['reported'] + 1);
	}

	public function testWhenReviewedGeneratedXpLogToPlayer() {
		$log = $this->utils->Log->find('first', array('conditions' => 'Log.reviewed IS NULL'));
		$this->assertNotEmpty($log);
		$this->utils->Log->review($log['Log']['id']);

		$xpLog = $this->utils->XpLog->findByPlayerIdAndActivityId(
			$log['Log']['player_id'], 
			$log['Log']['activity_id']
		);
		$this->assertNotNull($xpLog);
	}

	public function testWhenReviewedGeneratedXpLogToScrumMaster() {
		$log = $this->utils->Log->find('first', array('conditions' => 'Log.reviewed IS NULL'));
		$this->assertNotEmpty($log);
		$this->utils->Log->review($log['Log']['id']);

		$xpLog = $this->utils->XpLog->findByPlayerIdAndActivityIdReviewed(
			SCRUMMASTER_ID, 
			$log['Log']['activity_id']
		);

		$developersCount = $this->utils->Player->find('count', array(
			'conditions' => array(
				'Player.player_type_id' => PLAYER_TYPE_DEVELOPER
			)
		));

		$expectedXp = floor($log['Activity']['xp'] / $developersCount);
		$this->assertEquals($expectedXp, $xpLog['XpLog']['xp']);
	}

	public function testAcquiredFutureRule() {
		$log = $this->utils->Log->find('first');
		$date = new DateTime();
		$date->modify('+1 day');
		$log['Log']['acquired'] = $date->format('Y-m-d');
		$this->utils->Log->data = $log;
		$this->assertFalse($this->utils->Log->acquiredFutureRule());
	}

	public function testAcquiredPastRule() {
		$log = $this->utils->Log->find('first');
		$date = new DateTime();
		$date->modify('-2 day');
		$log['Log']['acquired'] = $date->format('Y-m-d');
		$this->utils->Log->data = $log;
		$this->assertFalse($this->utils->Log->acquiredPastRule());
	}

	public function testAllNotReviewed() {
		$result = $this->utils->Log->allNotReviewed();
		if (empty($result)) {
			$this->fail('No data to test');
		}
		foreach ($result as $row) {
			$this->assertEquals(null, $row['Log']['reviewed']);
		}
	}

	public function testPlayerCount() {
		$player = $this->utils->Player->find('first');
		$this->assertNotEmpty($player, 'Player not found');
		$result = $this->utils->Log->playerCount($player['Player']['id']);
		$this->assertTrue(is_int($result));
	}

	public function testTimeline() {
		$logs = $this->utils->Log->timeline();
		$this->assertEquals(16, count($logs));
	}

	public function testAverage() {
		$avg = $this->utils->Log->average();
		$this->assertEquals(4.0, $avg);
	}

	public function testSimpleReviewed() {
		$simple = $this->utils->Log->simpleReviewed();
		$this->assertEquals(8, count($simple));
	}

	public function testAllReviewed() {
		$all = $this->utils->Log->allReviewed();
		$this->assertEquals(8, count($all));
	}

	public function testPendingFromPlayer() {
		$logs = $this->utils->Log->allPendingFromPlayer(1);
		$this->assertEquals(4, count($logs));
	}

	public function testReviewWithoutId() {
		$log = $this->utils->Log->findByReviewed(null);
		$this->assertNull($log['Log']['reviewed']);
		$this->utils->Log->review();
		$log = $this->utils->Log->read();
		$this->assertNotNull($log['Log']['reviewed']);
	}

	public function testReviewNotExists() {
		try {
			$this->assertEquals(false, $this->utils->Log->review(1000));
			$this->fail();
		} catch (Exception $ex) {
			$this->assertEquals('Log not found', $ex->getMessage());
		}
	}

	public function testReviewWithId() {
		$log = $this->utils->Log->findByReviewed(null);
		$this->assertNull($log['Log']['reviewed']);
		$id = $log['Log']['id'];
		$this->utils->Log->review($id);
		$log = $this->utils->Log->read();
		$this->assertNotNull($log['Log']['reviewed']);
	}

	public function testCountNotReviewed() {
		$count = $this->utils->Log->countNotReviewed();
		$this->assertEquals(8, $count);
	}

	public function testCountPendingFromPlayer() {
		$count = $this->utils->Log->countPendingFromPlayer(1);
		$this->assertEquals(4, $count);
	}

	public function testLastFromEachPlayer() {
		$playerLogs = $this->utils->Log->lastFromEachPlayer(1);
		$this->assertEquals(3, count($playerLogs));
		foreach ($playerLogs as $playerId => $logs) {
			if ($playerId != SCRUMMASTER_ID) {
				$this->assertEquals(1, count($logs));
			}
		}
	}

}