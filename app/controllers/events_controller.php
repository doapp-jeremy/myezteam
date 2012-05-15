<?php
class EventsController extends AppController
{
	public $name = 'Events';
	public $scaffold = 'admin';

	function _saveOrSet($teamId = false)
	{
	  if (!empty($this->data))
	  {
	    debug($this->data);
	    $this->Event->create();
			if ($this->Event->save($this->data))
			{
				$this->Session->setFlash(__('The event has been saved', true));
				$this->redirect(array('controller' => 'Teams', 'action' => 'view', $this->data['Event']['team_id']));
			}
			else
			{
				$this->Session->setFlash(__('The event could not be saved. Please, try again.', true));
			}
	  }
	  else if ($teamId)
	  {
  	  $this->Event->Team->contain();
  	  $team = $this->Event->Team->read(null, $teamId);
  	  debug($team);
  	  $this->data['Event']['team_id'] = $teamId;
  	  $this->data['Event']['user_id'] = $this->_getUserId();
  	  $this->data['Event']['location'] = $team['Team']['default_location'];
  	  $this->data['Team'] = $team['Team'];
	  }
	  
	  
	  $defaultResponses = $this->Event->DefaultResponse->find('list', array('order' => 'DefaultResponse.id ASC'));
	  
	  $this->set(compact('team', 'defaultResponses'));
	}
	
	function edit($id = null)
	{
	  if (!$id)
	  {
	    $this->Session->setFlash("Invalid event id");
	    $this->redirect(array('controller' => 'Teams', 'action' => 'index'));
	  }
	  
	  $this->_saveOrSet();
	  $this->Event->contain(array('Team'));
	  $this->data = $this->Event->read(null, $id);
	}
	
	function add($teamId = null)
	{
	  if (!$teamId && empty($this->data))
	  {
	    $this->Session->setFlash("Invalid team id");
	    $this->redirect(array('controller' => 'Teams', 'action' => 'index'));
	  }
	  
	  $this->_saveOrSet($teamId);
	}
	
	private function getResponsesByType($event)
	{
	  $responsesByType = array('yes' => array(), 'probable' => array(), 'maybe' => array(), 'no_response' => array(), 'no' => array());
	  
	  foreach ($event['Response'] as $response)
	  {
	    $responsesByType[$response['ResponseType']['name']][] = $response;
	  }
	  
	  return $responsesByType;
	} 
	
	function view($id = null)
	{
	  if (!$id)
	  {
	    $this->Session->setFlash("Invalid event id");
	    $this->redirect(array('controller' => 'Teams', 'action' => 'index'));
	  }
	  
	  $contain = array(
	    'Team' => array('fields' => array('Team.id', 'Team.name')),
	    'DefaultResponse' => array('fields' => array('DefaultResponse.name')),
	    'Response' => array(
	      'ResponseType' => array('fields' => array('ResponseType.name')),
	      'Player' => array('fields' => array('Player.player_type_id'),
	        'PlayerType' => array('fields' => array('PlayerType.name')),
	        'User' => array('fields' => array('User.id', 'User.email', 'User.first_name', 'User.last_name', 'User.display_name', 'User.facebook_id'))
	      )
	    )
	  );
	  $this->Event->contain($contain);
	  $fields = array();
	  $event = $this->Event->read($fields, $id);
	  // TODO: verify access to event
	  
	  //debug($event);exit();
	  
	  $myResponse = Inflector::humanize($event['DefaultResponse']['name']);
	  
	  foreach ($event['Response'] as $response)
	  {
	    if ($response['Player']['user_id'] == $this->_getUserId())
	    {
	      $myResponse = Inflector::humanize($response['ResponseType']['name']) . ' at ' . date_create($response['created'])->format('g:ia \o\n n/d');
	    }
	  }
	  
	  $responsesByType = $this->getResponsesByType($event);
	  $this->set(compact('event', 'myResponse', 'responsesByType'));
	  
	  $this->layout = 'mobile';
	  $this->render('mobile/view');
	}
}
