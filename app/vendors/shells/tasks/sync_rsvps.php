<?php 
//require_once('facebook_api_task.php');
class SyncRsvpsTask extends FacebookApiTask
{
	var $uses = array('Event');
	
	var $rsvpMap = array(
	  'attending' => array('2'),
	  'declined' => array('5'),
	  'maybe' => array('4', '3'),
	  'noreply' => array('1')
	);
	
	function execute()
	{
		$this->syncAllEvents();
	}
	
	function syncAllEvents()
	{
	  $events = $this->Event->getAllUpcomingEventsWithFacebookManagers();
	  debug($events);
	  foreach ($events as $event)
	  {
	    $this->syncRsvps($event);
	  }
	}
	
	function syncRsvps($event)
	{
	  debug($event);
	  $fbEvent = $this->getFBevent($event);
	  debug($fbEvent);
	  foreach ($fbEvent['rsvps'] as $rsvpTitle => $friends)
	  {
	    if ($rsvpTitle == 'noreply')
	    {
	      continue;
	    }
	    foreach ($friends as $friend)
	    {
	      debug($friend);
	      debug($rsvpTitle);
	      list($playerId, $responseTypeId) = $this->getMyEZTeamResponse($friend['id'], $event);
	      debug($playerId . ":" + $responseTypeId);
	      if ($playerId && !in_array($responseTypeId, $this->rsvpMap[$rsvpTitle]))
	      {
	        // create myezteam response
	        $response = array();
	        $response['response_type_id'] = $this->rsvpMap[$rsvpTitle][0];
	        $response['player_id'] = $playerId;
	        $response['event_id'] = $event['Event']['id'];
	        $response['comment'] = "RSVP'd via Facebook";
	        debug($response);
	        $this->Event->Response->create();
	        if (!$this->Event->Response->save(array('Response' => $response)))
	        {
	          debug("Error creating response");
	          exit();
	        }
	      }
	    }
	  }
	}
	
	function getMyEZTeamResponse($fbId, $event)
	{
	  foreach ($event['Response'] as $response)
	  {
	    if ($response['Player']['User']['facebook_id'] == $fbId)
	    {
	      return array($response['player_id'], $response['response_type_id']);
	    }
	  }
	  // get player id for facebook id
	  $playerId = $this->getPlayerId($fbId, $event);
	  return array($playerId, 1); // No Response
	}
	
	function getPlayerId($fbId, $event)
	{
	  foreach ($event['Team']['Player'] as $player)
	  {
	    if ($player['User']['facebook_id'] == $fbId)
	    {
	      return $player['id'];
	    }
	  }
	  return false;
	}
	
	function getFBevent($event)
	{
	  $fbEvent = $this->fbAPI("{$event['Event']['facebook_event']}");
	  
	  $rsvps = array();
	  foreach (array('attending', 'declined', 'maybe', 'noreply') as $rsvp)
	  {
	    $data = $this->fbAPI("{$event['Event']['facebook_event']}/{$rsvp}");
	    $rsvps[$rsvp] = $data['data'];
	  }
	  $fbEvent['rsvps'] = $rsvps;
	  return $fbEvent;
	}
}
?>