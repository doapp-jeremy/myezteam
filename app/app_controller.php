<?php
/**
 * Base app controller, all your controllers inherit this class
 * 
 */
App::import('Core', 'Debugger');
class AppController extends Controller
{
  var $components = array('Session', 'Auth', 'Facebook.Connect' => array('createUser' => false), 'DebugKit.Toolbar' => array('panels' => array('history' => false)));	
	//	var $helpers = NULL;
	var $helpers = array('Html', 'Form', 'Session', 'Asset.asset', 'Facebook.Facebook');	
	var $allowedActions = array();
	
	var $FB = false;

	private function getFB()
	{
		if ($this->FB === false)
		{
			App::import('Lib', 'Facebook.FB');
			$this->FB = new FB();
		}
		return $this->FB;
	}

	public function fbRest($params)
	{
    $FB = $this->getFB();
    return $FB->api($params);
	}
	
	public function fbAPI($path, $method = "GET", $params = array())
	{
		$FB = $this->getFB();
		return $FB->api($path, $method, $params);
	}
	
	private function getFriendsFromTeams()
	{
	  $teamIds = $this->getTeams();
	  $this->loadModel('Team');
	  $fields = array('Team.id');
	  $conditions = array('Team.id' => $teamIds);
	  $contain = array(
	    'Player' => array('fields' => array('Player.user_id'), 'User' => array('fields' => array('User.id', 'User.first_name', 'User.last_name', 'User.email')))
	  );
	  $teams = $this->Team->find('all', compact('fields', 'conditions', 'contain'));
	  $users = Set::extract('/Player/User', $teams);
	  $friends = array();
	  $userIds = array();
	  foreach ($users as $user)
	  {
	    if (!in_array($user['User']['id'], $userIds))
	    {
	      $friends[] = array('id' => $user['User']['id'], 'email' => $user['User']['email'], 'first_name' => "{$user['User']['first_name']}", 'last_name' => "{$user['User']['last_name']}");
	      $userIds[] = $user['User']['id'];
	    }
	  }
	  return $friends;
	}
	
	private $fbFriendsKey = "facebookFriendsKey";
	private function getFriendsFromFB($fbUser, $params)
	{
	  $fbFriends = $this->Session->read($this->fbFriendsKey);
	  if (!$fbFriends && $this->Connect->user())
	  {
  	  $path = "{$fbUser['id']}/friends";
  	  $results = $this->fbAPI($path, 'GET', $params);
  	  $fbFriends = $results['data'];
  	  $this->Session->write($this->fbFriendsKey, $fbFriends);
	  }
	  else if (!$fbFriends)
	  {
	    $fbFriends = array();
	  }
	  return $fbFriends;
	}
	
	var $friendsKey = "friends";
	
	private function getFriendsFromSession($fbUserId)
	{
	  return $this->Session->read($this->friendsKey);
	}
	
//	function getFriends($fbUser = false, $params = array())
	function getFriends()
	{
	  $friends = $this->Session->read($this->friendsKey);
	  if (!$friends)
	  {
	    $friends = $this->getFriendsFromTeams();
	    if ($friends)
	    {
	      $this->Session->write($this->friendsKey, $friends);
	    }
	  }
	  return $friends;
	}

	var $teamsManagedKey = "teamsManagedKey";
	private function readTeamsManaged()
	{
	  $teams = $this->Session->read($this->teamsManagedKey);
	  if (!$teams)
	  {
	    $teams = array();
	  }
	  return $teams;
	}
	
	function getTeamsManaged($UserModel = null)
	{
	  $teamIds = $this->readTeamsManaged();
	  if (!$teamIds && $UserModel)
	  {
//	    $UserModel->contain(array('Team' => array('fields' => array('Team.id')), 'Player' => array('fields' => array('Player.team_id'))));
	    $UserModel->contain(array('Team' => array('fields' => array('Team.id'))));//, 'Player' => array('fields' => array('Player.team_id'))));
	    $auth = $this->Session->read("Auth.User");
	    if ($auth)
	    {
	      $user = $UserModel->read(array("{$UserModel->alias}.id"), $auth['id']);
	      $teamIds = Set::extract($user, '/Team/id');
	      $this->Session->write($this->teamsManagedKey, $teamIds);
	    }
	  }
	  
	  return $teamIds;
	}
	
	var $teamsPlayerKey = "teamsPlayerKey";
	private function readPlayerTeams()
	{
	  $teams =  $this->Session->read($this->teamsPlayerKey);
	  if (!$teams)
	  {
	    $teams = array();
	  }
	  return $teams;
	}
	
	function getPlayerTeams($UserModel = null)
	{
	  $teamIds = $this->readPlayerTeams();
	  if (!$teamIds)
	  {
	    if (!$UserModel)
	    {
	      $this->loadModel('User');
//	      App::import('Model', 'User');
//	      $UserModel = new User();
	    }
	    else
	    {
	      $this->User = $UserModel;
	    }
	    $this->User->contain(array('Player' => array('fields' => array('Player.team_id'))));
	    $auth = $this->Session->read("Auth.User");
	    if ($auth)
	    {
	      
	      $user = $this->User->read(array("{$this->User->alias}.id"), $auth['id']);
//	      debug($user);
	      $teamIds = Set::extract($user, '/Player/team_id');
//	      debug($teamIds);
	      $this->Session->write($this->teamsPlayerKey, $teamIds);
	    }
	  }
	  return $teamIds;
	}
	
	function getTeams($UserModel = null)
	{
	  $playerTeams = $this->getPlayerTeams($UserModel);
	  $managedTeams = $this->getTeamsManaged($UserModel);
	  return array_merge($playerTeams, $managedTeams);
	}
	
  function beforeFilter()
  {
    parent::beforeFilter();
    
    // Configure AuthComponent
    $this->Auth->authorize = "controller";
//    $this->Auth->actionPath = 'controllers/';
    $this->Auth->loginAction = array('controller' => 'Users', 'action' => 'login');
    $this->Auth->logoutRedirect = array('controller' => 'Users', 'action' => 'login');
    $this->Auth->loginRedirect = array('controller' => 'Teams', 'action' => 'index');
    $this->Auth->loginError = sprintf('Please login to access: %s/%s .', $this->name, $this->action);
    $this->Auth->authError = sprintf('You are not authorized to access: %s/%s .', $this->name, $this->action);
    // this doesn't seem to be working, use in isAuthorized
//    if (!empty($this->params['prefix']) && ($this->params['prefix'] == 'admin'))
//    {
//      $this->Auth->userScope = array('User.admin' => 1);
//    }

//    if (($this->name != 'Users') || !in_array($this->action, $this->allowedActions))
//    {
//      $fbUser = $this->Session->read('FB');
//      if (!$fbUser)
//      {
//        $this->Session->setFlash("Please login via facebook.");
//        $this->redirect(array('controller' => 'Users', 'action' => 'facebook_login'));
//      }
//    }
    
  }
	
  function _getUserId()
  {
    $user = $this->Session->read('Auth.User');
    return $user['id'];
  }
  
  function isAuthorized()
  {
//    debug($this->params);
    $user = $this->Session->read('Auth.User');
    if (!empty($this->params['prefix']) && ($this->params['prefix'] == 'admin'))
    {
      return ($user['admin']);
    }
    
    return true;
  }
  
  function canReadTeam($teamId = null, $UserModel = null)
  {
    if (!$teamId)
    {
      return false;
    }
    return in_array($teamId, $this->getTeams($UserModel));
  }

  function canWriteTeam($teamId = null, $UserModel = null)
  {
    if (!$teamId)
    {
      return false;
    }
    
    return in_array($teamId, $this->getTeamsManaged($UserModel));
  }
  
  function setFriends()
  {
    $friends = $this->getFriends($this->Connect->user(), array('first_name', 'last_name', 'name'));
//    $fbFriends = $this->getFriends($this->Connect->user(), array('first_name', 'last_name', 'name'));
    $this->set(compact('friends'));
    $this->setFacebookFriends();
  }
  
  function setFacebookFriends()
  {
    $fbFriends = $this->getFriendsFromFB($this->Connect->user(), array('email', 'first_name', 'last_name', 'name'));
//    $fbFriends = $this->getFbFriends($this->Connect->user(), array('first_name', 'last_name', 'name'));
    $this->set(compact('fbFriends'));
  }
  
  private $fbGroupKey = "FacebookGroupKey";
  private function readFacebookGroups()
  {
    $fbGroups = array();
	  $fbUser = $this->Connect->user();
	  if ($fbUser)
	  {
	    $data = $this->fbAPI("{$fbUser['id']}/groups");
	    $fbGroups = $data['data'];
	    $this->Session->write($this->fbGroupKey, $fbGroups);
	  }
	  return $fbGroups;
  } 
  
  function setFacebookGroups($teams = null)
  {
	  $fbGroups = $this->Session->read($this->fbGroupKey);
	  if (!$fbGroups)
	  {
	    $fbGroups = $this->readFacebookGroups();
	  }
	  $teamFbGroupIds = array();
	  if (is_array($teams))
	  {
	    $teamFbGroupIds = Set::extract("/Team/facebook_group", $teams);
	  }
	  else if (!empty($team['Team']['facebook_group']))
	  {
	    $teamFbGroupIds = array($team['Team']['facebook_group']);
	  }
	  
	  $this->set(compact('fbGroups', 'teamFbGroupIds'));
  }
}
?>