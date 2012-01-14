<?php
class PlayersController extends AppController
{
	public $name = 'Players';
	public $scaffold = 'admin';
	public $components = array('FastrestServer');
	
	private $teamId = null;
	
	function beforeFilter()
	{
	  parent::beforeFilter();
	  if (($this->action != 'save') && (empty($this->params['prefix']) || (!$this->params['prefix'] != 'admin')) && (empty($this->params['named']['team'])))
	  {
	    $this->Session->setFlash("Invalid request");
	    $this->redirect(array('controller' => 'Teams', 'action' => 'index'));
	  }
	  if (!empty($this->params['named']['team']))
	  {
	    $this->teamId = $this->params['named']['team'];
	  }
	}
	
	function isAuthorized()
	{
	  if ($this->teamId && !in_array($this->teamId, $this->getTeams()))
	  {
	    return false;
	  }
	  return parent::isAuthorized();
	}
	
	function index()
	{
	  $contain = array('User', 'PlayerType', 'Team');
	  $players = $this->Player->find('all', array('join' => $contain, 'conditions' => array('Player.team_id' => $this->teamId)));
	  $team = array();
	  if (!empty($players))
	  {
	    $team = $players[0];
	  }
	  
	  $this->setFacebookFriends();
	  $this->set(compact('team', 'players'));
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
		if (!empty($this->data['Player']['team_id']) && (!empty($this->data['Player']['id']) || !empty($this->data['Player']['user_id']) || !empty($this->data['User']['email'])))
		{
		  $teamId = $this->data['Player']['team_id'];
		  
		  if (empty($this->data['Player']['id']))
		  {
		    $conditions = array();
		    // verify user isn't already on team
		    if (!empty($this->data['Player']['user_id']))
		    {
		      $conditions = array('User.id' => $this->data['Player']['user_id']);
		    }
		    else if (!empty($this->data['User']['email']))
		    {
		      $conditions = array('User.email' => $this->data['User']['email']);
		    }
		    else
		    {
    			$this->FastrestServer->renderError(400,'ERROR: Request data not formatted correctly.');
    			$this->log('Incorrectly formatted data: '.var_export(utf8_encode(trim(file_get_contents('php://input'))),true).' ['.__METHOD__.'::'.__LINE__.']');
    			return;
		    }
		    $fields = array('User.id');
		    $contain = array(
		      'Player' => array('fields' => array('Player.user_id'), 'conditions' => array('Player.team_id' => $teamId))
		    );
		    $user = $this->Player->User->find('first', compact('fields', 'conditions', 'contain'));
		    if (!empty($user['Player']))
		    {
		      $this->FastrestServer->renderData($ext, array('status' => 'error', 'message' => 'Player already exists on team'));
		      Configure::write('debug', 0);
		      return;
		    }
		  }
		  
		  $player = false;
		  if (!$this->Player->save($this->data))
		  {
		    $this->FastrestServer->renderError(403,'ERROR: Could not save player');
		    return;
		  }
		  if (empty($this->data['Player']['id']))
		  {
		    $this->data['Player']['id'] = $this->Player->getLastInsertID();
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
	
}
