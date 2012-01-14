<?php
class TeamsController extends AppController
{
	public $name = 'Teams';
	public $scaffold = 'admin';
	public $components = array('FastrestServer');
	
	function index()
	{
	  $teamsManagedIds = $this->getTeamsManaged($this->Team->Manager);
	  $teamsPlayerIds = $this->getPlayerTeams($this->Team->Manager);
	  $justPlayerTeamIds = array();
	  foreach ($teamsPlayerIds as $teamId)
	  {
	    if (!in_array($teamId, $teamsManagedIds))
	    {
	      $justPlayerTeamIds[] = $teamId;
	    }
	  }
	  
	  $allTeamIds = array_merge($teamsManagedIds, $justPlayerTeamIds);
	  
//	  $fields = array('Team.id', 'Team.facebook_group', 'Team.name', '(NextEvent.id IS NULL) AS noEvent', 'NextEvent.id', 'NextEvent.name', 'NextEvent.date', 'NextEvent.time', 'LastEvent.id', 'LastEvent.name', 'LastEvent.date', 'LastEvent.time');
	  $fields = array('Team.id', 'Team.facebook_group', 'Team.name');
	  $conditions = array('Team.id' => $allTeamIds);
	  $contain = array('UpcomingEvent', 'PastEvent');
	  $teams = $this->Team->find('all', compact('fields', 'conditions', 'contain'));
	  
	  $this->setFacebookGroups($teams);
	  $this->set(compact('teams', 'teamsManagedIds'));
	}
	
	function save($ext = 'json')
	{
		//Set the default response to bad request, so any unhandled errors dont return 200
		header('HTTP/1.0 400 Bad Request');
		
		if (!$this->FastrestServer->isPost())
		{
			$this->log('POST not used'.' ['.__METHOD__.'::'.__LINE__.']');
			$this->FastrestServer->renderError(400,'ERROR: Must POST data');
			return;
		}
		
		//decode POST body and put in to $this->data
		// don't need to do this, call is coming from inside app
//		$this->FastrestServer->decodePOSTAndSetData($ext);
		if (empty($this->data))
		{
			$this->FastrestServer->renderError(400,'ERROR: No data specified');
			return;
		}
		
		if (!empty($this->data['Team']['id']))
		{
		  $teamId = $this->data['Team']['id'];
		  $team = false;
		  if (!$this->Team->save($this->data))
		  {
		    $this->FastrestServer->renderError(403,'ERROR: Could not save user');
		    return;
		  }
		  $this->FastrestServer->renderData($ext, $this->data);
			Configure::write('debug', 0);
		}
		else
		{
			$this->FastrestServer->renderError(400,'ERROR: Request data not formatted correctly.');
			$this->log('Incorrectly formatted data: '.var_export(utf8_encode(trim(file_get_contents('php://input'))),true).' ['.__METHOD__.'::'.__LINE__.']');
			return;
		}
	}
	
	function view($id = null)
	{
	  if (!$this->canReadTeam($id))
	  {
	    $this->Session->setFlash("You are not authorized to view that team");
	    $this->redirect(array('controller' => 'Teams', 'action' => 'index'));
	  }
	  
	  $team = $this->Team->getTeam($id);
	  
	  
	  $playerTypes = $this->Team->Player->PlayerType->find('list');
	  $this->setFriends();
	  $this->setFacebookGroups($team);
	  $this->set(compact('team', 'playerTypes'));
	}
	
}
