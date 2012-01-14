<?php
class Team extends AppModel {
	public $name = 'Team';
	public $displayField = 'name';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	public $belongsTo = array(
		'League' => array(
			'className' => 'League',
			'foreignKey' => 'league_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Creator' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public $hasOne = array(
		'NextEvent' => array(
			'className' => 'Event',
			'foreignKey' => 'team_id',
			'dependent' => true,
			'conditions' => array('(NextEvent.date >= UTC_DATE() OR (NextEvent.date=UTC_DATE() AND NextEvent.time >= UTC_TIME()))'),
			'fields' => '',
			'order' => array('NextEvent.date' => 'ASC', 'NextEvent.time' => 'ASC'),
			'limit' => '1',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'LastEvent' => array(
			'className' => 'Event',
			'foreignKey' => 'team_id',
			'dependent' => true,
			'conditions' => array('(LastEvent.date <= UTC_DATE() OR (LastEvent.date=UTC_DATE() AND LastEvent.time < UTC_TIME()))'),
			'fields' => '',
			'order' => array('LastEvent.date' => 'DESC', 'LastEvent.time' => 'DESC'),
			'limit' => '1',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);
	
	public $hasMany = array(
		'Event' => array(
			'className' => 'Event',
			'foreignKey' => 'team_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => array('Event.date' => 'DESC', 'Event.time' => 'DESC'),
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'UpcomingEvent' => array(
			'className' => 'Event',
			'foreignKey' => 'team_id',
			'dependent' => true,
			//'conditions' => array('(UpcomingEvent.date >= UTC_DATE() OR (UpcomingEvent.date=UTC_DATE() AND UpcomingEvent.time >= UTC_TIME()))'),
			'conditions' => array('(CONCAT(UpcomingEvent.date, " ", UpcomingEvent.time) >= UTC_DATE())'),
			'fields' => '',
			'order' => array('UpcomingEvent.date' => 'ASC', 'UpcomingEvent.time' => 'ASC'),
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'PastEvent' => array(
			'className' => 'Event',
			'foreignKey' => 'team_id',
			'dependent' => true,
//			'conditions' => array('(PastEvent.date <= UTC_DATE() OR (PastEvent.date=UTC_DATE() AND PastEvent.time < UTC_TIME()))'),
			'conditions' => array('(CONCAT(PastEvent.date, " ", PastEvent.time) <= UTC_DATE())'),
			'fields' => '',
			'order' => array('PastEvent.date' => 'DESC', 'PastEvent.time' => 'DESC'),
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Player' => array(
			'className' => 'Player',
			'foreignKey' => 'team_id',
			'dependent' => true,
			'conditions' => array('Player.player_type_id != 3'),
			'fields' => '',
			'order' => array('Player.player_type_id' => 'ASC'),
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Regular' => array(
			'className' => 'Player',
			'foreignKey' => 'team_id',
			'dependent' => true,
			'conditions' => array('Regular.player_type_id' => 1),
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Sub' => array(
			'className' => 'Player',
			'foreignKey' => 'team_id',
			'dependent' => true,
			'conditions' => array('Sub.player_type_id' => 2),
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Member' => array(
			'className' => 'Player',
			'foreignKey' => 'team_id',
			'dependent' => true,
			'conditions' => array('Member.player_type_id' => 3),
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		
	);
	
	public $hasAndBelongsToMany = array(
		'Manager' => array(
			'className' => 'User',
	    'joinTable' => 'teams_managers',
			'foreignKey' => 'team_id',
	    'associationForeignKey' => 'user_id',
	    'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);
	
	function getFacebookSessionForTeam($team)
	{
	  $session = null;
	  if (!empty($team['Creator']['FacebookSession']))
	  {
	    $session = $team['Creator']['FacebookSession'];
	  }
	  foreach ($team['Manager'] as $manager)
	  {
	    if (!empty($manager['User']['FacebookSession']))
	    {
	      $session = $manager['User']['FacebookSession'];
	      break;
	    }
	  }
	  if ($session)
	  {
	    unset($session['created']);
	    unset($session['modified']);
	  }
	  return $session;
	}

	function getTeamsAndManagers($leagueId = null, $conditions = array())
	{
		$this->contain(array(
			'Creator' => array('FacebookSession'),
			'Manager' => array('FacebookSession'),
			)
		);
		if ($leagueId)
		{
			$conditions['Team.league_id'] = $leagueId;
		}
		
		return $this->find('all',array('conditions'=> $conditions));		
	}
	
	function getFacebookTeamsAndManagers($leagueId = null, $conditions = array())
	{
		if (!isset($conditions['AND']))
		{
			$conditions['AND'] = array();
		}
		$conditions['AND'][] = 'Team.facebook_group IS NOT NULL';
		$conditions['AND'][] = "Team.facebook_group != ''";
		return $this->getTeamsAndManagers($leagueId, $conditions);
	}
	
	function getFacebookTeamsAndManagersAndPlayers($leagueId = null, $conditions = array())
	{
		if (!isset($conditions['AND']))
		{
			$conditions['AND'] = array();
		}
		$conditions['AND'][] = 'Team.facebook_group IS NOT NULL';
		$conditions['AND'][] = "Team.facebook_group != ''";
		$this->contain(array(
			'Creator' => array('FacebookSession'),
			'Manager' => array('FacebookSession'),
			'Player' => array('User')
			)
		);
		if ($leagueId)
		{
			$conditions['Team.league_id'] = $leagueId;
		}
		
		return $this->find('all',array('conditions'=> $conditions));		
	}
	
	
	function getFacebookTeamsAndManagersAndUpcomingEvents($leagueId = null, $conditions = array())
  {
    if (!isset($conditions['AND']))
    {
      $conditions['AND'] = array();
    }
    $conditions['AND'][] = 'Team.facebook_group IS NOT NULL';
    $conditions['AND'][] = "Team.facebook_group != ''";
    $this->contain(array(
      'Creator' => array('fields' => array('Creator.id', 'Creator.facebook_id'), 'FacebookSession'),
      'Manager' => array('FacebookSession', 'TimeZone'),
      'UpcomingEvent'
    ));
    $teams = $this->find('all', array('conditions'=> $conditions));
    return $teams;
  }
	
  function getTeam($id, $fields = array('Team.id', 'Team.facebook_group', 'Team.name', 'Team.type', 'Team.calendar_id', 'Team.description'))
  {
    $eventFields = array('UpcomingEvent.name', 'UpcomingEvent.start', 'UpcomingEvent.end', 'UpcomingEvent.location', 'UpcomingEvent.description', 'UpcomingEvent.response_type_id', 'UpcomingEvent.cal_event_id', 'UpcomingEvent.facebook_event');
    $userFields = array('User.id', 'User.email', 'User.first_name', 'User.last_name', 'User.display_name', 'User.facebook_id');
    
    $playerContain = array('fields' => array('Player.id'),
      'PlayerType' => array('fields' => array('PlayerType.name')),
      'User' => array('fields' => $userFields)
    );
    
    $contain = array(
      'UpcomingEvent' => array('fields' => $eventFields,
        'DefaultResponse' => array('fields' => array('DefaultResponse.name', 'DefaultResponse.color')),
        'Response' => array('fields' => array('Response.comment', 'Response.ip', 'Response.created'),
          'ResponseType' => array('fields' => array('ResponseType.name', 'ResponseType.color')),
          'Player' => $playerContain
        ),
      ),
      'Player' => $playerContain,
    );
    
    $this->contain($contain);
    $team = $this->read($fields, $id);
    
    $this->Event->consolidateResponses($team['UpcomingEvent'], $team['Player']);
    
    return $team;
  }
}

