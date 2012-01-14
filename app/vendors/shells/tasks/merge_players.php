<?php 
class MergePlayersTask extends FacebookApiTask
{
	var $uses = array('Team');
	
	function execute()
	{
    $conditions = array();
    if (count($this->args) > 0)
    {
      $conditions['AND'] = array('Team.id' => $this->args[0]);
    }
		
    $teams = $this->Team->getFacebookTeamsAndManagersAndPlayers();
    foreach ($teams as $team)
    {
      $this->mergePlayersForTeam($team);
    }
	}
	
	function getFacebookMembersForTeam($team)
	{
	  $members = array();
	  $fbSession = $this->Team->getFacebookSessionForTeam($team);
		debug($fbSession);
		if ($fbSession && !empty($team['Team']['facebook_group']))
		{
		  $this->setSession($fbSession, false);
		  $path = "{$team['Team']['facebook_group']}/members";
		  debug($path);
		  $fbMembers = $this->fbAPI($path, 'GET', array('fields' => array('first_name', 'last_name', 'name', 'email')));
		  if (!empty($fbMembers['data']))
		  {
		    $members = $fbMembers['data'];
		  }
		}
		else
		{
		  debug("NO Facebook SESSION for team");
		  debug($team);
		  exit();
		}
		return $members;
	}
	
	function isPlayerSameAsFacebookMember($player, $facebookMember)
	{
	  return ($player['User']['facebook_id'] == $facebookMember['id']);
	  // don't assume based on name...allow manager to map players on site
//	  return (
//	    ($player['User']['facebook_id'] == $facebookMember['id']) || 
//	    ((strcasecmp($player['User']['first_name'], $facebookMember['first_name']) == 0) &&
//	    (strcasecmp($player['User']['last_name'], $facebookMember['last_name']) == 0)));
	}
	
	function mergePlayersForTeam($team)
	{
	  debug($team['Player']);
		$facebookPlayers = array();
		$players = array();
		$facebookMembers = $this->getFacebookMembersForTeam($team);
		debug($facebookMembers);
		foreach ($team['Player'] as $player)
		{
			// if a players email = fb user id@facebook.com, then it's a facebook user
			if (!empty($player['User']['facebook_id']))
			{
				if ($player['User']['email'] == $this->Team->Player->User->getTempEmailForFacebookUserId($player['User']['facebook_id']))
				{
					$facebookPlayers[] = $player;
				}
				else
				{
					$players[] = $player;
				}
			}
			else
			{
			  // try to find player in facebook members
			  foreach ($facebookMembers as $facebookMember)
			  {
			    if ($this->isPlayerSameAsFacebookMember($player, $facebookMember))
			    {
			      debug("Players are the same, set facebook id");
			      debug($player);
			      debug($facebookMember);
			      // set facebook id
			      $player['User']['facebook_id'] = $facebookMember['id'];
			      if (!$this->Team->Player->User->save($player))
			      {
			        debug("Could not save player with facebook id.");
			        exit();
			      }
			      exit();
			    }
			  }
			}
		}
		debug($players);
		debug($facebookPlayers);
		exit();
		foreach ($facebookPlayers as $facebookPlayer)
		{
			// search in players
			$player = Set::extract("/User[facebook_id={$facebookPlayer['User']['facebook_id']}]/..", $players);
      debug($player);
			if ($player)
			{
        $this->Team->mergePlayers($player[0], $facebookPlayer);
			}
		}
	}
}
?>