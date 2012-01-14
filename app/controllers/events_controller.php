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
//	  $fields = array('Event.id', 'Event.facebook_event', 'Event.name', 'Event.start');
	  $fields = array();
	  $event = $this->Event->read($fields, $id);
	  // TODO: verify access to event
	  
	  $this->set(compact('event'));
//	  debug($event);
	}
}
