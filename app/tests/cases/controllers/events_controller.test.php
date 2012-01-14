<?php
/* Events Test cases generated on: 2011-01-03 21:01:19 : 1294112839*/
App::import('Controller', 'Events');

class TestEventsController extends EventsController {
	public $autoRender = false;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class EventsControllerTestCase extends CakeTestCase {
	public $fixtures = array('app.event');

	public function startTest() {
		$this->Events = new TestEventsController();
		$this->Events->constructClasses();
	}

	public function endTest() {
		unset($this->Events);
		ClassRegistry::flush();
	}

}
