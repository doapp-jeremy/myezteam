<?php
/* Player Test cases generated on: 2011-01-08 06:01:31 : 1294467931*/
App::import('Model', 'Player');

class PlayerTestCase extends CakeTestCase {
	var $fixtures = array('app.player', 'app.user', 'app.facebook_session', 'app.player_type', 'app.team', 'app.league', 'app.event', 'app.response_type', 'app.teams_manager');

	function startTest() {
		$this->Player =& ClassRegistry::init('Player');
	}

	function endTest() {
		unset($this->Player);
		ClassRegistry::flush();
	}

}
?>