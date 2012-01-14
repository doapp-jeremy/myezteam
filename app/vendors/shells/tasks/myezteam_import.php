<?php 
class MyezteamImportTask extends ImportTask
{
	var $uses = array('User', 'OldModel', 'Team');
	var $database = 'myezteam_myezteam';
	var $special = array('teams_managers');
	
  function getOldModelToUse()
  {
    return $this->OldModel;
  }

  function getNewModelToUse()
  {
    return $this->User;
  }
	
	var $tableMap = array(
	  'users' => array(
	    'old_table' => array('database' => 'junker_teammanager', 'table' => 'users'),
	    'field_map' => array(
	      'id' => 'id',
	      'email' => 'email',
	      'legacy_password' => 'password',
	      'admin' => 'isAdmin',
	      'first_name' => 'first_name',
	      'last_name' => 'last_name',
	      'created' => 'created',
	      'modified' => 'modified'
	    )
	  ),
	  'leagues' => array(
	    'old_table' => array('database' => 'junker_teammanager', 'table' => 'teams'),
	    'field_map' => array(
	      'id' => 'id',
	      'user_id' => 'user_id',
	      'name' => 'getLeagueName',
	      'created' => 'created',
	      'modified' => 'modified'
	    )
	  ),
	  'teams' => array(
	    'old_table' => array('database' => 'junker_teammanager', 'table' => 'teams'),
	    'field_map' => array(
	      'id' => 'id',
	      'league_id' => 'id',
	      'user_id' => 'user_id',
	      'name' => 'name',
	      'description' => 'description',
	      'type' => 'type',
	      'default_location' => 'default_location',
	      'google_calendar' => 'calendar_id',
	      'facebook_group' => 'getTeamFacebookGroup',
	      'created' => 'created',
	      'modified' => 'modified'
	    )
	  ),
	  'response_types' => array(
	    'old_table' => array('database' => 'junker_teammanager', 'table' => 'response_types'),
	    'field_map' => array(
	      'id' => 'id',
	      'name' => 'getResponseName',
	      'color' => 'color',
	      'created' => 'created',
	      'modified' => 'modified'
	    )
	  ),
	  'events' => array(
	    'old_table' => array('database' => 'junker_teammanager', 'table' => 'events'),
	    'field_map' => array(
	      'id' => 'id',
	      'team_id' => 'team_id',
	      'date' => 'getEventDate',
	      'time' => 'getEventTime',
	      'name' => 'name',
	      'description' => 'description',
	      'location' => 'location',
	      'default_response' => 'response_type_id',
	      'google_calendar' => 'cal_event_id',
	      'created' => 'created',
	      'modified' => 'modified'
	    )
	  ),
//	  'teams_managers' => array(
//	    'old_table' => array('database' => 'junker_teammanager', 'table' => 'teams_users'),
//	    'field_map' => array(
//	      'id' => 'id',
//	      'team_id' => 'team_id',
//	      'user_id' => 'user_id',
//	      'created' => 'created',
//	      'modified' => 'modified'
//	    )
//	  ),
	  'players' => array(
	    'old_table' => array('database' => 'junker_teammanager', 'table' => 'players'),
	    'field_map' => array(
	      'id' => 'id',
	      'team_id' => 'team_id',
	      'user_id' => 'user_id',
	      'player_type_id' => 'player_type_id',
	      'created' => 'created',
	      'modified' => 'modified'
	    )
	  ),
	  
	);
	
	function getTeamFacebookGroup($oldData)
	{
	  if ($oldData['teams']['id'] == '3')
	  {
	    // sloppy waffles group id
	    return "183090751721048";
	  }
	  return 'NULL';
	}
	
	function getResponseName($oldData)
	{
	  return Inflector::humanize($oldData['response_types']['name']);
	}
	
	function getEventDate($oldData)
	{
	  return date_create($oldData['events']['start'])->format('Y-m-d');
	}
	
	function getEventTime($oldData)
	{
	  return date_create($oldData['events']['start'])->format('H:i:s');
	}
	
	function getLeagueName($oldData)
	{
	  return "{$oldData['teams']['type']} {$oldData['teams']['name']}";
	}
	
	function isAdmin($oldData)
	{
	  if ($oldData['users']['email'] == 'junker37@gmail.com')
	  {
	    return '1';
	  }
	  return '0';
	}
	
	var $teamsManagers = array(
	    'old_table' => array('database' => 'junker_teammanager', 'table' => 'teams_users'),
	    'field_map' => array(
	      'id' => 'id',
	      'team_id' => 'team_id',
	      'user_id' => 'user_id',
	      'created' => 'created',
	      'modified' => 'modified'
	    )
	  );
	
	function importTeamsManagers()
	{
	  $table = "teams_managers";
	  $data = $this->teamsManagers;
	  $this->import($table, $data);

	  // now create a manager out of all the creators
	  $teams = $this->OldModel->query("SELECT id,user_id FROM teams");
	  debug($teams[0]);
	  foreach ($teams as $team)
	  {
	    $replaceString = "REPLACE INTO teams_managers (team_id, user_id) VALUES ({$team['teams']['id']}, {$team['teams']['user_id']})";
	    debug($replaceString);
	    $this->NewModel->query($replaceString);
	  }
	  
	}
}
?>
