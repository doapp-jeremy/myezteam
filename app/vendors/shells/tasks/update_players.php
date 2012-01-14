<?php 
class UpdatePlayersTask extends Shell
{
	var $uses = array('Team');
	
	function execute()
	{
//		$conditions = array('AND' => array('Team.facebook_group_id IS NOT NULL', "Team.facebook_group_id != ''"));
		$conditions = array();
		if (count($this->args) > 0)
		{
			$conditions['AND'] = array('Team.id' => $this->args[0]);
		}
		
//		$this->Team->contain(array('Player'));
//		$teams = $this->Team->find('all', array('conditions' => $conditions));
		$teams = $this->Team->getFacebookTeamsAndManagers();
//		debug($teams);
		foreach ($teams as $team)
		{
			$this->updatePlayersForTeam($team);
		}
	}
	
	function updatePlayersForTeam($team)
	{
		if (empty($team['Team']['facebook_group_id']))
		{
			$this->out("Not a facebook group: {$team['Team']['id']}:{$team['Team']['name']}");
			return;
		}
		$this->out("Checking players on team: {$team['Team']['name']}");
		App::import('Lib', 'Facebook.FB');
		$FB = new FB();
		
		$members = array();
		/// search managers until we find with with a facebook user session stored
		// then use it to retrieve members of group
		foreach ($team['Manager'] as $manager)
		{
			if (!empty($manager['User']['FacebookUser']))
			{
				$FB->setSession($manager['User']['FacebookUser'], false, false);
//				$session = $FB->getSession();
//				debug($session);
//				exit();
//				debug($manager['User']['FacebookUser']);
				$path = "{$team['Team']['facebook_group_id']}/members";
				$members = $FB->api($path, 'GET', array('fields' => array('name', 'email')));
				debug($members);
				break;
			}
		}
		
		foreach ($members['data'] as $member)
		{
			if (!$this->Team->isFacebookUserOnTeam($team, $member))
			{
				if (empty($member['email']))
				{
					$member = array_merge($member, $FB->api($member['id']));
				}
				debug($member);
				$this->Team->addFacebookUserToTeam($team, $member);
			}
		}
	}
}
?>
