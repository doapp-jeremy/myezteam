<?php
/* Teams Test cases generated on: 2011-01-03 20:01:58 : 1294107898*/
App::import('Controller', 'Teams');

class TestTeamsController extends TeamsController {
	public $autoRender = false;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class TeamsControllerTestCase extends CakeTestCase {
	public $fixtures = array('app.team');

	public function startTest() {
		$this->Teams = new TestTeamsController();
		$this->Teams->constructClasses();
	}

	public function endTest() {
		unset($this->Teams);
		ClassRegistry::flush();
	}

}
