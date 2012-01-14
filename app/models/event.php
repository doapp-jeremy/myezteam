<?php
class Event extends AppModel {
	public $name = 'Event';
	public $displayField = 'name';
	public $validate = array(
		'team_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'name' => array(
			'alphaNumeric' => array(
				'rule' => array('between', 1, 255),
				//'message' => 'You must enter a name',
				'allowEmpty' => false,
				'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	public $belongsTo = array(
		'Team' => array(
			'className' => 'Team',
			'foreignKey' => 'team_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
//		'User' => array(
//			'className' => 'User',
//			'foreignKey' => 'user_id',
//			'conditions' => '',
//			'fields' => '',
//			'order' => ''
//		),
		'DefaultResponse' => array(
			'className' => 'ResponseType',
			'foreignKey' => 'response_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	public $hasMany = array(
		'Response' => array(
			'className' => 'Response',
			'foreignKey' => 'event_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => array('Response.created' => 'DESC'),
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);
	
	function beforeSave()
	{
	  debug($this->data);
	  
	  if (!empty($this->data['Team']['facebook_group']))
	  {
	    $fbGroupId = $this->data['Team']['facebook_group'];
  	  $name = $this->data['Event']['name'];
  	  $description = $this->data['Event']['description'];
  	  $location = $this->data['Event']['location'];
  	  $startDate = date_create("{$this->data['Event']['date']} {$this->data['Event']['time']}");
  	  $endDate = date_create($startDate->format('Y-m-d H:i:s'));
  	  $endDate->modify("+1 hour");
  	  debug($startDate);
  	  debug($endDate);
  	  
  	  // TODO: check if facebook is fixed..currently 8 hours ahead
  	  $startDate->modify("+8 hours");
  	  $endDate->modify("+8 hours");
  
  	  $fbEvent = array('page_id' => $fbGroupId, 'name' => $name, 'description' => $description, 'location' => $location, 'privacy' => 'CLOSED', 'start_time' => $startDate->format("c"), 'end_time' => $endDate->format("c"));
	    if (!empty($this->data['Event']['facebook_event']))
	    {
	      $fbEvent['id'] = $this->data['Event']['facebook_event'];
	    }
  	  debug($fbEvent);
  
  	  debug($startDate->format("c"));
  
  	  $result = $this->fbAPI("/{$fbGroupId}/events", 'POST', $fbEvent);
  	  debug($result);
  	  if (empty($this->data['Event']['facebook_event']) && !empty($result['id']))
  	  {
  	    $this->data['Event']['facebook_event'] = $result['id'];
  	    debug("TODO: invite players to event");
//  	    $this->Team->invitePlayersToEvent($team1, $event, $result['id']);
//  	    return $result['id'];
  	  }
	  }
	  return true;
	}
	
	function getAllEventsWithFacebookManagers($conditions = array())
	{
	  $contain = array(
	    'Team' => array('fields' => array('Team.name'),
	      'Player' => array('fields' => array('Player.player_type_id'))
//	    	'Manager' => array('fields' => array('Manager.display_name', 'Manager.facebook_id'),
//	        'FacebookSession')
	    ),
	    'Response' => array('fields' => array('Response.response_type_id'),
	    	'Player' => array('fields' => array('Player.id', 'Player.player_type_id'),
	    		'User' => array('fields' => array('User.display_name', 'User.facebook_id'))))
	  );
	  $fields = array('Event.id', 'Event.team_id', 'Event.name', 'Event.facebook_event');
	  $conditions[] = array('Event.facebook_event IS NOT NULL');
	  $order = array('Event.start' => 'ASC');
	  $events = $this->find('all', compact('fields', 'conditions', 'contain', 'order'));
	  return $events;
	}
	
	function getAllUpcomingEventsWithFacebookManagers()
	{
	  return $this->getAllEventsWithFacebookManagers(array('Event.start > NOW()'));
	}
	
	function consolidateResponses(&$events, $players)
	{
	  $responseTypes = getResponseTypes();
	  $noResponseId = 1;
	  foreach ($events as &$event)
	  {
	    $event = getEvent($event);
	    // intialize ResponseByResponseType first
	    foreach ($responseTypes as $responseType)
	    {
	      $event['ResponseByResponseType'][$responseType['ResponseType']['id']] = array();
	    }
	    foreach ($event['Response'] as $response)
	    {
	      if (empty($event['ResponseByPlayer'][$response['player_id']]))
	      {
	        $event['ResponseByPlayer'][$response['player_id']] = $response;
	        $event['ResponseByResponseType'][$response['response_type_id']][] = $response;
	      }
	    }
	    foreach ($players as $player)
	    {
	      if (empty($event['ResponseByPlayer'][$player['id']]))
	      {
	        $response = array('response_type_id' => $noResponseId, 'player_id' => $player['id'], 'event_id' => $event['Event']['id']);
	        $event['ResponseByPlayer'][$player['id']] = $response;
	        if ($player['player_type_id'] == '1' /* regular */)
	        {
	          $event['ResponseByResponseType'][$noResponseId][] = $response;
	        }
	      }
	    }
	  }
	}
}
