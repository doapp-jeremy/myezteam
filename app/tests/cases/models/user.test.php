<?php
/* User Test cases generated on: 2011-01-01 22:01:10 : 1293942430*/
App::import('Model', 'User');

class UserTestCase extends CakeTestCase {
	public $fixtures = array('app.user');

	public function startTest() {
		$this->User = ClassRegistry::init('User');
	}

	public function endTest() {
		unset($this->User);
		ClassRegistry::flush();
	}

}
